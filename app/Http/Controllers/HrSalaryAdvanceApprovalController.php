<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\SalaryAdvanceInstallment;
use App\Models\SalaryAdvanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrSalaryAdvanceApprovalController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.hr_approval'), 403);

        $query = SalaryAdvanceRequest::with([
            'employee.department',
            'employee.position',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
        ]);

        if ($request->status) {
            $query->where('workflow_status', $request->status);
        } else {
            $query->where(function ($q) {
                $q->where('workflow_status', 'manager_approved_pending_hr')
                    ->orWhere(function ($sub) {
                        $sub->where('direct_manager_status', 'approved')
                            ->where('hr_status', 'pending')
                            ->where('status', 'pending');
                    });
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('request_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('employee', function ($employeeQuery) use ($request) {
                        $employeeQuery->where('full_name', 'like', '%' . $request->search . '%')
                            ->orWhere('employee_number', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $salaryAdvanceRequests = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_hr' => SalaryAdvanceRequest::where(function ($q) {
                $q->where('workflow_status', 'manager_approved_pending_hr')
                    ->orWhere(function ($sub) {
                        $sub->where('direct_manager_status', 'approved')
                            ->where('hr_status', 'pending')
                            ->where('status', 'pending');
                    });
            })->count(),

            'registered' => SalaryAdvanceRequest::where('workflow_status', 'registered')->count(),
            'rejected_by_hr' => SalaryAdvanceRequest::where('workflow_status', 'rejected_by_hr')->count(),
        ];

        return view('hr_salary_advance_approvals.index', compact('salaryAdvanceRequests', 'stats'));
    }

    public function approve(Request $request, SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.hr_approval'), 403);

        if (!$this->isWaitingForHr($salaryAdvanceRequest)) {
            return back()->with('error', 'لا يمكن اعتماد هذا الطلب لأنه ليس بانتظار الموارد البشرية');
        }

        $request->validate([
            'approved_amount' => ['required', 'numeric', 'min:1'],
            'installments_count' => ['required', 'integer', 'min:1', 'max:60'],
            'deduction_start_date' => ['required', 'date_format:Y-m'],
            'hr_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'approved_amount.required' => 'المبلغ المعتمد مطلوب',
            'installments_count.required' => 'عدد الأقساط مطلوب',
            'deduction_start_date.required' => 'شهر بداية الخصم مطلوب',
        ]);

        DB::transaction(function () use ($salaryAdvanceRequest, $request) {
            $oldStatus = $salaryAdvanceRequest->workflow_status;

            $approvedAmount = round((float) $request->approved_amount, 2);
            $installmentsCount = (int) $request->installments_count;
            $installmentAmount = round($approvedAmount / $installmentsCount, 2);
            $deductionStartDate = Carbon::createFromFormat('Y-m', $request->deduction_start_date)->startOfMonth();

            $advance = SalaryAdvance::create([
                'salary_advance_request_id' => $salaryAdvanceRequest->id,
                'employee_id' => $salaryAdvanceRequest->employee_id,
                'advance_number' => SalaryAdvance::generateNumber(),
                'amount' => $approvedAmount,
                'installments_count' => $installmentsCount,
                'installment_amount' => $installmentAmount,
                'request_date' => now()->toDateString(),
                'deduction_start_date' => $deductionStartDate->toDateString(),
                'status' => 'approved',
                'reason' => $salaryAdvanceRequest->reason,
                'notes' => $request->hr_note ?: $salaryAdvanceRequest->notes,
                'created_by' => $salaryAdvanceRequest->created_by,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->generateInstallments($advance);

            $salaryAdvanceRequest->forceFill([
                'approved_amount' => $approvedAmount,
                'installments_count' => $installmentsCount,
                'installment_amount' => $installmentAmount,
                'deduction_start_date' => $deductionStartDate->toDateString(),
                'workflow_status' => 'registered',
                'hr_status' => 'approved',
                'hr_approved_by' => auth()->id(),
                'hr_approved_at' => now(),
                'hr_note' => $request->hr_note,
                'status' => 'approved',
                'registered_salary_advance_id' => $advance->id,
            ])->save();

            $salaryAdvanceRequest->addLog(
                'hr_approved_and_registered',
                $oldStatus,
                'registered',
                'تمت موافقة الموارد البشرية وتم تسجيل السلفة على الموظف',
                [
                    'salary_advance_id' => $advance->id,
                    'advance_number' => $advance->advance_number,
                ]
            );
        });

        return redirect()
            ->route('hr-salary-advance-approvals.index')
            ->with('success', 'تم اعتماد الطلب من الموارد البشرية وتسجيل السلفة على الموظف بنجاح');
    }

    public function reject(Request $request, SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.hr_approval'), 403);

        if (!$this->isWaitingForHr($salaryAdvanceRequest)) {
            return back()->with('error', 'لا يمكن رفض هذا الطلب لأنه ليس بانتظار الموارد البشرية');
        }

        $request->validate([
            'hr_reject_reason' => ['required', 'string', 'max:1000'],
        ], [
            'hr_reject_reason.required' => 'سبب الرفض مطلوب',
        ]);

        DB::transaction(function () use ($salaryAdvanceRequest, $request) {
            $oldStatus = $salaryAdvanceRequest->workflow_status;

            $salaryAdvanceRequest->forceFill([
                'workflow_status' => 'rejected_by_hr',
                'hr_status' => 'rejected',
                'hr_rejected_by' => auth()->id(),
                'hr_rejected_at' => now(),
                'hr_reject_reason' => $request->hr_reject_reason,
                'status' => 'rejected',
            ])->save();

            $salaryAdvanceRequest->addLog(
                'hr_rejected',
                $oldStatus,
                'rejected_by_hr',
                'تم رفض طلب السلفة من الموارد البشرية: ' . $request->hr_reject_reason
            );
        });

        return redirect()
            ->route('hr-salary-advance-approvals.index')
            ->with('success', 'تم رفض طلب السلفة من الموارد البشرية');
    }

    private function isWaitingForHr(SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        return $salaryAdvanceRequest->workflow_status === 'manager_approved_pending_hr'
            || (
                $salaryAdvanceRequest->direct_manager_status === 'approved'
                && $salaryAdvanceRequest->hr_status === 'pending'
                && $salaryAdvanceRequest->status === 'pending'
            );
    }

    private function generateInstallments(SalaryAdvance $advance): void
    {
        $amount = round((float) $advance->amount, 2);
        $count = (int) $advance->installments_count;
        $monthlyAmount = round((float) $advance->installment_amount, 2);
        $startDate = Carbon::parse($advance->deduction_start_date)->startOfMonth();

        for ($i = 0; $i < $count; $i++) {
            $number = $i + 1;
            $currentAmount = $monthlyAmount;

            if ($number === $count) {
                $currentAmount = round($amount - ($monthlyAmount * ($count - 1)), 2);
            }

            SalaryAdvanceInstallment::create([
                'salary_advance_id' => $advance->id,
                'employee_id' => $advance->employee_id,
                'installment_number' => $number,
                'amount' => $currentAmount,
                'due_date' => $startDate->copy()->addMonths($i)->toDateString(),
                'status' => 'pending',
            ]);
        }
    }
}
