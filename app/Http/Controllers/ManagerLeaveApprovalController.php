<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerLeaveApprovalController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.manager_approval'), 403);

        $query = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType'])
            ->whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('direct_manager_user_id', auth()->id());
            });

        if ($request->status) {
            $query->where('workflow_status', $request->status);
        } else {
            $query->where('workflow_status', 'pending_manager');
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
            'pending_manager' => LeaveRequest::whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('direct_manager_user_id', auth()->id());
            })
                ->where('workflow_status', 'pending_manager')
                ->count(),

            'manager_approved_pending_hr' => LeaveRequest::whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('direct_manager_user_id', auth()->id());
            })
                ->where('workflow_status', 'manager_approved_pending_hr')
                ->count(),

            'rejected_by_manager' => LeaveRequest::whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('direct_manager_user_id', auth()->id());
            })
                ->where('workflow_status', 'rejected_by_manager')
                ->count(),
        ];

        return view('manager_leave_approvals.index', compact('leaveRequests', 'stats'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.manager_approval'), 403);

        $this->abortIfNotDirectManager($leaveRequest);

        if ($leaveRequest->workflow_status !== 'pending_manager') {
            return back()->with('error', 'لا يمكن اعتماد هذا الطلب لأنه لم يعد بانتظار المدير المباشر');
        }

        DB::transaction(function () use ($leaveRequest) {
            /*
             * نستخدم forceFill حتى لو كان هناك أي مشكلة في fillable
             * ونضمن تحديث workflow_status الذي يقرأه بروجرس الموظف.
             */
            $leaveRequest->forceFill([
                'workflow_status' => 'manager_approved_pending_hr',

                'direct_manager_status' => 'approved',
                'direct_manager_approved_by' => auth()->id(),
                'direct_manager_approved_at' => now(),

                'direct_manager_rejected_by' => null,
                'direct_manager_rejected_at' => null,
                'direct_manager_reject_reason' => null,

                'hr_status' => 'pending',

                // تبقى pending حتى تعتمد الموارد البشرية نهائياً
                'status' => 'pending',
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'manager_approved',
                'تمت موافقة المدير المباشر وتم تحويل الطلب إلى الموارد البشرية'
            );
        });

        return redirect()
            ->route('manager-leave-approvals.index')
            ->with('success', 'تمت موافقة المدير المباشر وتم تحويل الطلب إلى الموارد البشرية');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.manager_approval'), 403);

        $this->abortIfNotDirectManager($leaveRequest);

        if ($leaveRequest->workflow_status !== 'pending_manager') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب لأنه لم يعد بانتظار المدير المباشر');
        }

        $request->validate([
            'direct_manager_reject_reason' => ['required', 'string', 'max:1000'],
        ], [
            'direct_manager_reject_reason.required' => 'سبب الرفض مطلوب',
            'direct_manager_reject_reason.max' => 'سبب الرفض يجب ألا يتجاوز 1000 حرف',
        ]);

        DB::transaction(function () use ($leaveRequest, $request) {
            $leaveRequest->forceFill([
                'workflow_status' => 'rejected_by_manager',

                'direct_manager_status' => 'rejected',
                'direct_manager_rejected_by' => auth()->id(),
                'direct_manager_rejected_at' => now(),
                'direct_manager_reject_reason' => $request->direct_manager_reject_reason,

                'hr_status' => 'not_required',

                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'reject_reason' => $request->direct_manager_reject_reason,
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'manager_rejected',
                'تم رفض طلب الإجازة من المدير المباشر'
            );
        });

        return redirect()
            ->route('manager-leave-approvals.index')
            ->with('success', 'تم رفض الطلب من المدير المباشر ولن يتم تحويله إلى الموارد البشرية');
    }

    private function abortIfNotDirectManager(LeaveRequest $leaveRequest): void
    {
        $leaveRequest->loadMissing('employee');

        if (!$leaveRequest->employee || (int) $leaveRequest->employee->direct_manager_user_id !== (int) auth()->id()) {
            abort(403, 'هذا الطلب لا يتبع للمدير المباشر الحالي');
        }
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
         * لا نوقف موافقة المدير إذا لم يوجد رصيد مفتوح.
         * فحص الرصيد الفعلي يتم عند موافقة الموارد البشرية النهائية.
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
