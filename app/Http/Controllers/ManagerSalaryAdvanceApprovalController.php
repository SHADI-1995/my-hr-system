<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerSalaryAdvanceApprovalController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.manager_approval'), 403);

        $query = SalaryAdvanceRequest::with(['employee.department', 'employee.position'])
            ->whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('direct_manager_user_id', auth()->id());
            });

        if ($request->status) {
            $query->where('workflow_status', $request->status);
        } else {
            $query->where('workflow_status', 'pending_manager');
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

        $baseQuery = SalaryAdvanceRequest::whereHas('employee', function ($employeeQuery) {
            $employeeQuery->where('direct_manager_user_id', auth()->id());
        });

        $stats = [
            'pending_manager' => (clone $baseQuery)->where('workflow_status', 'pending_manager')->count(),
            'manager_approved_pending_hr' => (clone $baseQuery)->where('workflow_status', 'manager_approved_pending_hr')->count(),
            'rejected_by_manager' => (clone $baseQuery)->where('workflow_status', 'rejected_by_manager')->count(),
        ];

        return view('manager_salary_advance_approvals.index', compact('salaryAdvanceRequests', 'stats'));
    }

    public function approve(SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.manager_approval'), 403);

        $this->abortIfNotDirectManager($salaryAdvanceRequest);

        if (!$salaryAdvanceRequest->can_manager_approve) {
            return back()->with('error', 'لا يمكن اعتماد هذا الطلب لأنه لم يعد بانتظار المدير المباشر');
        }

        DB::transaction(function () use ($salaryAdvanceRequest) {
            $oldStatus = $salaryAdvanceRequest->workflow_status;

            $salaryAdvanceRequest->forceFill([
                'workflow_status' => 'manager_approved_pending_hr',
                'direct_manager_status' => 'approved',
                'direct_manager_approved_by' => auth()->id(),
                'direct_manager_approved_at' => now(),
                'direct_manager_rejected_by' => null,
                'direct_manager_rejected_at' => null,
                'direct_manager_reject_reason' => null,
                'hr_status' => 'pending',
                'status' => 'pending',
            ])->save();

            $salaryAdvanceRequest->addLog(
                'manager_approved',
                $oldStatus,
                'manager_approved_pending_hr',
                'تمت موافقة المدير المباشر وتم تحويل طلب السلفة إلى الموارد البشرية'
            );
        });

        return redirect()
            ->route('manager-salary-advance-approvals.index')
            ->with('success', 'تمت موافقة المدير المباشر وتم تحويل الطلب إلى الموارد البشرية');
    }

    public function reject(Request $request, SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        abort_if(!auth()->user()->hasPermission('salary_advance_requests.manager_approval'), 403);

        $this->abortIfNotDirectManager($salaryAdvanceRequest);

        if (!$salaryAdvanceRequest->can_manager_approve) {
            return back()->with('error', 'لا يمكن رفض هذا الطلب لأنه لم يعد بانتظار المدير المباشر');
        }

        $request->validate([
            'direct_manager_reject_reason' => ['required', 'string', 'max:1000'],
        ], [
            'direct_manager_reject_reason.required' => 'سبب الرفض مطلوب',
        ]);

        DB::transaction(function () use ($salaryAdvanceRequest, $request) {
            $oldStatus = $salaryAdvanceRequest->workflow_status;

            $salaryAdvanceRequest->forceFill([
                'workflow_status' => 'rejected_by_manager',
                'direct_manager_status' => 'rejected',
                'direct_manager_rejected_by' => auth()->id(),
                'direct_manager_rejected_at' => now(),
                'direct_manager_reject_reason' => $request->direct_manager_reject_reason,
                'hr_status' => 'not_required',
                'status' => 'rejected',
            ])->save();

            $salaryAdvanceRequest->addLog(
                'manager_rejected',
                $oldStatus,
                'rejected_by_manager',
                'تم رفض طلب السلفة من المدير المباشر: ' . $request->direct_manager_reject_reason
            );
        });

        return redirect()
            ->route('manager-salary-advance-approvals.index')
            ->with('success', 'تم رفض طلب السلفة من المدير المباشر');
    }

    private function abortIfNotDirectManager(SalaryAdvanceRequest $salaryAdvanceRequest): void
    {
        $salaryAdvanceRequest->loadMissing('employee');

        if (!$salaryAdvanceRequest->employee || (int) $salaryAdvanceRequest->employee->direct_manager_user_id !== (int) auth()->id()) {
            abort(403, 'هذا الطلب لا يتبع للمدير المباشر الحالي');
        }
    }
}
