<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrLeaveApprovalController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.hr_approval'), 403);

        $query = LeaveRequest::with([
            'employee.department',
            'employee.position',
            'leaveType',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
        ]);

        /*
         * الحالة الافتراضية:
         * نعرض كل طلب وافق عليه المدير المباشر ولم يعتمد نهائياً من HR.
         * هذا يجعل الطلب يظهر حتى لو كان عندك سجل قديم فيه hr_status = pending
         * لكن workflow_status لم يتحدث بسبب النسخة السابقة.
         */
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
            $query->whereHas('employee', function ($employeeQuery) use ($request) {
                $employeeQuery->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_hr' => LeaveRequest::where(function ($q) {
                $q->where('workflow_status', 'manager_approved_pending_hr')
                    ->orWhere(function ($sub) {
                        $sub->where('direct_manager_status', 'approved')
                            ->where('hr_status', 'pending')
                            ->where('status', 'pending');
                    });
            })
                ->count(),

            'approved_by_hr' => LeaveRequest::where('workflow_status', 'approved_by_hr')->count(),

            'rejected_by_hr' => LeaveRequest::where('workflow_status', 'rejected_by_hr')->count(),
        ];

        return view('hr_leave_approvals.index', compact('leaveRequests', 'stats'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.hr_approval'), 403);

        if (!$this->isWaitingForHr($leaveRequest)) {
            return back()->with('error', 'لا يمكن اعتماد هذا الطلب لأنه ليس بانتظار الموارد البشرية');
        }

        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->loadMissing(['employee', 'leaveType']);

            /*
             * خصم الرصيد عند الاعتماد النهائي من الموارد البشرية.
             */
            $balance = EmployeeLeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $beforeBalance = $balance?->remaining_days ?? 0;
            $afterBalance = $beforeBalance;

            if ($balance) {
                $afterBalance = max(0, (float) $beforeBalance - (float) $leaveRequest->days_count);

                $balance->update([
                    'used_days' => ((float) $balance->used_days) + ((float) $leaveRequest->days_count),
                    'remaining_days' => $afterBalance,
                ]);

                EmployeeLeaveTransaction::create([
                    'employee_id' => $leaveRequest->employee_id,
                    'employee_leave_balance_id' => $balance->id,
                    'transaction_type' => 'paid_leave_deduction',
                    'days' => -abs((float) $leaveRequest->days_count),
                    'before_balance' => $beforeBalance,
                    'after_balance' => $afterBalance,
                    'description' => 'خصم رصيد بعد اعتماد الموارد البشرية لطلب إجازة رقم #' . $leaveRequest->id,
                    'reference_type' => LeaveRequest::class,
                    'reference_id' => $leaveRequest->id,
                    'created_by' => auth()->id(),
                ]);
            }

            $leaveRequest->forceFill([
                'workflow_status' => 'approved_by_hr',
                'hr_status' => 'approved',
                'hr_approved_by' => auth()->id(),
                'hr_approved_at' => now(),

                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'hr_approved',
                'تمت موافقة الموارد البشرية واعتماد الإجازة نهائيًا'
            );
        });

        return redirect()
            ->route('hr-leave-approvals.index')
            ->with('success', 'تم اعتماد الطلب من الموارد البشرية وخصم الرصيد بنجاح');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.hr_approval'), 403);

        if (!$this->isWaitingForHr($leaveRequest)) {
            return back()->with('error', 'لا يمكن رفض هذا الطلب لأنه ليس بانتظار الموارد البشرية');
        }

        $request->validate([
            'hr_reject_reason' => ['required', 'string', 'max:1000'],
        ], [
            'hr_reject_reason.required' => 'سبب الرفض مطلوب',
            'hr_reject_reason.max' => 'سبب الرفض يجب ألا يتجاوز 1000 حرف',
        ]);

        DB::transaction(function () use ($leaveRequest, $request) {
            $leaveRequest->forceFill([
                'workflow_status' => 'rejected_by_hr',
                'hr_status' => 'rejected',
                'hr_rejected_by' => auth()->id(),
                'hr_rejected_at' => now(),
                'hr_reject_reason' => $request->hr_reject_reason,

                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'reject_reason' => $request->hr_reject_reason,
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'hr_rejected',
                'تم رفض طلب الإجازة من الموارد البشرية'
            );
        });

        return redirect()
            ->route('hr-leave-approvals.index')
            ->with('success', 'تم رفض الطلب من الموارد البشرية');
    }

    private function isWaitingForHr(LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->workflow_status === 'manager_approved_pending_hr'
            || (
                $leaveRequest->direct_manager_status === 'approved'
                && $leaveRequest->hr_status === 'pending'
                && $leaveRequest->status === 'pending'
            );
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

        /*
         * لا نوقف موافقة أو رفض HR إذا لم يوجد رصيد مفتوح.
         * حركة الخصم الأساسية فقط تعتمد على وجود الرصيد.
         */
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
            'created_by' => auth()->id(),
        ]);
    }
}
