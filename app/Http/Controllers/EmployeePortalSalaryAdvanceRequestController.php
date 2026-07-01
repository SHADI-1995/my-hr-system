<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryAdvanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeePortalSalaryAdvanceRequestController extends Controller
{
    public function index()
    {
        $employee = $this->currentEmployee();

        if (!$employee) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $salaryAdvanceRequests = SalaryAdvanceRequest::query()
            ->with([
                'employee',
                'registeredSalaryAdvance',
            ])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view('employee_portal.salary_advance_requests.index', compact(
            'employee',
            'salaryAdvanceRequests'
        ));
    }

    public function create()
    {
        $employee = $this->currentEmployee();

        if (!$employee) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        return view('employee_portal.salary_advance_requests.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $employee = $this->currentEmployee();

        if (!$employee) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        if (!$employee->direct_manager_user_id) {
            return back()
                ->withInput()
                ->with('error', 'لا يمكن إرسال طلب السلفة لأن المدير المباشر غير محدد في ملفك الوظيفي');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'installments_count' => ['required', 'integer', 'min:1', 'max:60'],
            'deduction_start_date' => ['required', 'date_format:Y-m'],
            'reason' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'amount.required' => 'مبلغ السلفة مطلوب',
            'amount.numeric' => 'مبلغ السلفة يجب أن يكون رقمًا',
            'amount.min' => 'مبلغ السلفة يجب أن يكون أكبر من صفر',

            'installments_count.required' => 'عدد الأقساط مطلوب',
            'installments_count.integer' => 'عدد الأقساط يجب أن يكون رقمًا صحيحًا',
            'installments_count.min' => 'عدد الأقساط يجب ألا يقل عن 1',
            'installments_count.max' => 'عدد الأقساط يجب ألا يزيد عن 60',

            'deduction_start_date.required' => 'شهر بداية الخصم مطلوب',
            'deduction_start_date.date_format' => 'صيغة شهر بداية الخصم غير صحيحة',

            'reason.required' => 'سبب طلب السلفة مطلوب',
            'attachment.mimes' => 'المرفق يجب أن يكون PDF أو صورة JPG أو PNG أو WEBP',
            'attachment.max' => 'حجم المرفق يجب ألا يتجاوز 5MB',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('salary_advance_request_attachments', 'public');
        }

        $deductionStartDate = $validated['deduction_start_date'] . '-01';
        $installmentAmount = round(((float) $validated['amount']) / ((int) $validated['installments_count']), 2);

        DB::transaction(function () use ($employee, $validated, $attachmentPath, $deductionStartDate, $installmentAmount) {
            $salaryAdvanceRequest = SalaryAdvanceRequest::create([
                'request_number' => SalaryAdvanceRequest::generateNumber(),
                'employee_id' => $employee->id,
                'created_by' => null,

                'amount' => $validated['amount'],
                'approved_amount' => null,
                'installments_count' => $validated['installments_count'],
                'installment_amount' => $installmentAmount,
                'deduction_start_date' => $deductionStartDate,

                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'attachment' => $attachmentPath,

                'status' => 'pending',
                'workflow_status' => 'pending_manager',
                'direct_manager_status' => 'pending',
                'hr_status' => 'waiting_manager',
            ]);

            if (method_exists($salaryAdvanceRequest, 'addLog')) {
                $salaryAdvanceRequest->addLog(
                    'employee_submitted',
                    null,
                    'pending_manager',
                    'تم تقديم طلب السلفة من بوابة الموظف'
                );
            }
        });

        return redirect()
            ->route('employee-portal.salary-advance-requests.index')
            ->with('success', 'تم إرسال طلب السلفة بنجاح، وهو الآن بانتظار موافقة المدير المباشر');
    }

    public function show(SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        $employee = $this->currentEmployee();

        if (!$employee) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        if ((int) $salaryAdvanceRequest->employee_id !== (int) $employee->id) {
            abort(403);
        }

        $salaryAdvanceRequest->load([
            'employee',
            'registeredSalaryAdvance',
            'logs',
        ]);

        return view('employee_portal.salary_advance_requests.show', compact(
            'employee',
            'salaryAdvanceRequest'
        ));
    }

    public function cancel(SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        $employee = $this->currentEmployee();

        if (!$employee) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        if ((int) $salaryAdvanceRequest->employee_id !== (int) $employee->id) {
            abort(403);
        }

        if (!$salaryAdvanceRequest->can_employee_cancel) {
            return back()
                ->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية');
        }

        DB::transaction(function () use ($salaryAdvanceRequest) {
            $oldStatus = $salaryAdvanceRequest->workflow_status;

            $salaryAdvanceRequest->update([
                'status' => 'cancelled',
                'workflow_status' => 'cancelled',
            ]);

            if (method_exists($salaryAdvanceRequest, 'addLog')) {
                $salaryAdvanceRequest->addLog(
                    'employee_cancelled',
                    $oldStatus,
                    'cancelled',
                    'تم إلغاء طلب السلفة من بوابة الموظف'
                );
            }
        });

        return redirect()
            ->route('employee-portal.salary-advance-requests.index')
            ->with('success', 'تم إلغاء طلب السلفة بنجاح');
    }

    private function currentEmployee(): ?Employee
    {
        $employeeId = session('employee_portal_id');

        if (!$employeeId) {
            return null;
        }

        return Employee::query()
            ->with(['directManagerUser'])
            ->find($employeeId);
    }
}
