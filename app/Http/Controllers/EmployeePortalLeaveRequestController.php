<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeePortalLeaveRequestController extends Controller
{
    public function index()
    {
        $employee = $this->employeeOrRedirect();

        if (!$employee instanceof Employee) {
            return $employee;
        }

        $leaveRequests = LeaveRequest::with(['leaveType'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view('employee_portal.leave_requests.index', compact('employee', 'leaveRequests'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $employee = $this->employeeOrRedirect();

        if (!$employee instanceof Employee) {
            return $employee;
        }

        abort_if((int) $leaveRequest->employee_id !== (int) $employee->id, 403);

        $leaveRequest->load([
            'leaveType',
            'employee',
        ]);

        /*
         * سجل الحركة في بوابة الموظف:
         * في نظامك يتم تسجيل حركات طلب الإجازة في جدول EmployeeLeaveTransaction
         * لذلك نقرأ السجل حسب reference_type و reference_id.
         */
        $leaveLogs = EmployeeLeaveTransaction::where('reference_type', LeaveRequest::class)
            ->where('reference_id', $leaveRequest->id)
            ->orderBy('created_at')
            ->get();

        /*
         * في حال كان الطلب قديمًا أو لا يوجد رصيد مفتوح وبالتالي لم يتم إنشاء Transaction،
         * نعرض سجلًا افتراضيًا حتى لا تظهر صفحة التفاصيل بدون سجل حركة.
         */
        if ($leaveLogs->isEmpty()) {
            $leaveLogs = collect([
                (object) [
                    'transaction_type' => 'leave_request_created',
                    'description' => 'تم تقديم طلب الإجازة من بوابة الموظف',
                    'created_at' => $leaveRequest->created_at,
                ],
            ]);

            if (($leaveRequest->workflow_status ?? null) === 'manager_approved_pending_hr') {
                $leaveLogs->push((object) [
                    'transaction_type' => 'manager_approved',
                    'description' => 'تمت موافقة المدير المباشر والطلب بانتظار الموارد البشرية',
                    'created_at' => $leaveRequest->updated_at,
                ]);
            }

            if (($leaveRequest->workflow_status ?? null) === 'approved_by_hr') {
                $leaveLogs->push((object) [
                    'transaction_type' => 'hr_approved',
                    'description' => 'تم اعتماد طلب الإجازة من الموارد البشرية',
                    'created_at' => $leaveRequest->updated_at,
                ]);
            }

            if (($leaveRequest->workflow_status ?? null) === 'rejected_by_manager') {
                $leaveLogs->push((object) [
                    'transaction_type' => 'manager_rejected',
                    'description' => $leaveRequest->direct_manager_reject_reason
                        ? 'تم رفض الطلب من المدير المباشر: ' . $leaveRequest->direct_manager_reject_reason
                        : 'تم رفض الطلب من المدير المباشر',
                    'created_at' => $leaveRequest->updated_at,
                ]);
            }

            if (($leaveRequest->workflow_status ?? null) === 'rejected_by_hr') {
                $leaveLogs->push((object) [
                    'transaction_type' => 'hr_rejected',
                    'description' => $leaveRequest->hr_reject_reason
                        ? 'تم رفض الطلب من الموارد البشرية: ' . $leaveRequest->hr_reject_reason
                        : 'تم رفض الطلب من الموارد البشرية',
                    'created_at' => $leaveRequest->updated_at,
                ]);
            }

            if (($leaveRequest->workflow_status ?? null) === 'cancelled') {
                $leaveLogs->push((object) [
                    'transaction_type' => 'leave_request_cancelled',
                    'description' => 'تم إلغاء طلب الإجازة من بوابة الموظف',
                    'created_at' => $leaveRequest->updated_at,
                ]);
            }
        }

        return view('employee_portal.leave_requests.show', compact(
            'employee',
            'leaveRequest',
            'leaveLogs'
        ));
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        $employee = $this->employeeOrRedirect();

        if (!$employee instanceof Employee) {
            return $employee;
        }

        abort_if((int) $leaveRequest->employee_id !== (int) $employee->id, 403);

        $workflowStatus = $leaveRequest->workflow_status ?? 'pending_manager';

        $canCancel = $leaveRequest->can_employee_cancel
            ?? in_array($workflowStatus, ['pending_manager', 'manager_approved_pending_hr'], true);

        if (!$canCancel) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية.');
        }

        DB::transaction(function () use ($leaveRequest) {
            $updates = [
                'status' => 'cancelled',
                'workflow_status' => 'cancelled',
            ];

            if (($leaveRequest->direct_manager_status ?? null) === 'pending') {
                $updates['direct_manager_status'] = 'cancelled';
            }

            if (in_array(($leaveRequest->hr_status ?? null), ['waiting_manager', 'pending'], true)) {
                $updates['hr_status'] = 'cancelled';
            }

            $leaveRequest->update($updates);

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest->fresh(),
                'leave_request_cancelled',
                'تم إلغاء طلب الإجازة من بوابة الموظف'
            );
        });

        return redirect()
            ->route('employee-portal.leave-requests.index')
            ->with('success', 'تم إلغاء طلب الإجازة بنجاح.');
    }

    public function create()
    {
        $employee = $this->employeeOrRedirect();

        if (!$employee instanceof Employee) {
            return $employee;
        }

        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employee_portal.leave_requests.create', compact('employee', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $employee = $this->employeeOrRedirect();

        if (!$employee instanceof Employee) {
            return $employee;
        }

        if (!$employee->direct_manager_user_id) {
            return back()
                ->withInput()
                ->with('error', 'لا يمكن إرسال طلب الإجازة لأن المدير المباشر غير محدد في ملف الموظف. يرجى التواصل مع الموارد البشرية.');
        }

        $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ], [
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'start_date.required' => 'تاريخ بداية الإجازة مطلوب',
            'end_date.required' => 'تاريخ نهاية الإجازة مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'attachment.required' => 'المرفق مطلوب لإرسال طلب الإجازة',
            'attachment.file' => 'يجب اختيار ملف صحيح',
            'attachment.mimes' => 'المرفق يجب أن يكون صورة أو PDF بصيغة JPG أو JPEG أو PNG أو WEBP أو PDF',
            'attachment.max' => 'حجم المرفق يجب ألا يتجاوز 4 ميجا',
        ]);

        $daysCount = Carbon::parse($request->start_date)
                ->diffInDays(Carbon::parse($request->end_date)) + 1;

        $attachmentPath = $request->file('attachment')
            ->store('leave-requests/attachments', 'public');

        DB::transaction(function () use ($employee, $request, $daysCount, $attachmentPath) {
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days_count' => $daysCount,
                'status' => 'pending',
                'workflow_status' => 'pending_manager',
                'direct_manager_status' => 'pending',
                'hr_status' => 'waiting_manager',
                'reason' => $request->reason,
                'attachment' => $attachmentPath,
            ]);

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'leave_request_created',
                'تم تقديم طلب إجازة من بوابة الموظف وحالته بانتظار موافقة المدير المباشر'
            );
        });

        return redirect()
            ->route('employee-portal.leave-requests.index')
            ->with('success', 'تم إرسال طلب الإجازة إلى المدير المباشر بنجاح');
    }

    private function employeeOrRedirect()
    {
        $employeeId = session('employee_portal_id');

        if (!$employeeId) {
            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $employee = Employee::with(['latestIqama', 'directManagerUser'])->find($employeeId);

        if (!$employee) {
            session()->forget('employee_portal_id');

            return redirect()
                ->route('employee-portal.login')
                ->with('error', 'انتهت الجلسة، يرجى تسجيل الدخول مرة أخرى');
        }

        return $employee;
    }

    private function recordLeaveWorkflowTransaction(
        LeaveRequest $leaveRequest,
        string $transactionType,
        string $description,
        float $days = 0
    ): void {
        $balance = EmployeeLeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('status', 'open')
            ->whereDate('service_year_start', '<=', $leaveRequest->start_date)
            ->whereDate('service_year_end', '>=', $leaveRequest->end_date)
            ->first();

        if (!$balance) {
            return;
        }

        $currentBalance = (float) $balance->remaining_days;

        EmployeeLeaveTransaction::create([
            'employee_id' => $leaveRequest->employee_id,
            'employee_leave_balance_id' => $balance->id,
            'transaction_type' => $transactionType,
            'days' => $days,
            'before_balance' => $currentBalance,
            'after_balance' => $currentBalance,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $leaveRequest->id,
            'description' => $description,
            'created_by' => null,
        ]);
    }
}
