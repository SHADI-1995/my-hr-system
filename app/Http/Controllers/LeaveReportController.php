<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaveReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_reports.view'), 403);

        $query = $this->buildReportQuery($request);

        $leaveRequests = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $summaryQuery = $this->buildReportQuery($request);

        $summary = [
            'total_requests' => (clone $summaryQuery)->count(),

            'pending_requests' => (clone $summaryQuery)->where('status', 'pending')->count(),
            'approved_requests' => (clone $summaryQuery)->where('status', 'approved')->count(),
            'rejected_requests' => (clone $summaryQuery)->where('status', 'rejected')->count(),
            'cancelled_requests' => (clone $summaryQuery)->where('status', 'cancelled')->count(),

            'pending_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'pending_manager')->count(),
            'pending_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'manager_approved_pending_hr')->count(),
            'approved_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->count(),
            'rejected_by_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_manager')->count(),
            'rejected_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_hr')->count(),
            'workflow_cancelled_requests' => (clone $summaryQuery)->where('workflow_status', 'cancelled')->count(),

            'approved_days' => (float) (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->sum('days_count'),
            'pending_manager_days' => (float) (clone $summaryQuery)->where('workflow_status', 'pending_manager')->sum('days_count'),
            'pending_hr_days' => (float) (clone $summaryQuery)->where('workflow_status', 'manager_approved_pending_hr')->sum('days_count'),
            'rejected_days' => (float) (clone $summaryQuery)
                ->whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])
                ->sum('days_count'),
            'cancelled_days' => (float) (clone $summaryQuery)->where('workflow_status', 'cancelled')->sum('days_count'),
        ];

        $employees = Employee::orderBy('full_name')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $directManagers = User::orderBy('name')->get();
        $workflowStatuses = $this->workflowStatuses();

        return view('leave_reports.index', compact(
            'leaveRequests',
            'summary',
            'employees',
            'leaveTypes',
            'directManagers',
            'workflowStatuses'
        ));
    }

    public function exportExcel(Request $request): Response
    {
        abort_if(!auth()->user()->hasPermission('leave_reports.export'), 403);

        $leaveRequests = $this->buildReportQuery($request)
            ->orderByDesc('id')
            ->get();

        $workflowStatuses = $this->workflowStatuses();

        $fileName = 'leave-report-' . now()->format('Y-m-d-H-i') . '.xls';

        $html = view('leave_reports.excel', compact('leaveRequests', 'workflowStatuses'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function printPdf(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_reports.export'), 403);

        $leaveRequests = $this->buildReportQuery($request)
            ->orderByDesc('id')
            ->get();

        $summaryQuery = $this->buildReportQuery($request);

        $summary = [
            'total_requests' => (clone $summaryQuery)->count(),
            'pending_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'pending_manager')->count(),
            'pending_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'manager_approved_pending_hr')->count(),
            'approved_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->count(),
            'rejected_by_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_manager')->count(),
            'rejected_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_hr')->count(),
            'cancelled_requests' => (clone $summaryQuery)->where('workflow_status', 'cancelled')->count(),
            'approved_days' => (float) (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->sum('days_count'),
        ];

        $workflowStatuses = $this->workflowStatuses();

        return view('leave_reports.print_pdf', compact('leaveRequests', 'summary', 'workflowStatuses'));
    }

    private function buildReportQuery(Request $request)
    {
        $query = LeaveRequest::with([
            'employee.department',
            'employee.directManagerUser',
            'leaveType',
            'approvedBy',
            'rejectedBy',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
            'hrApprovedBy',
            'hrRejectedBy',
        ]);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->workflow_status) {
            $query->where('workflow_status', $request->workflow_status);
        }

        if ($request->direct_manager_user_id) {
            $query->whereHas('employee', function ($employeeQuery) use ($request) {
                $employeeQuery->where('direct_manager_user_id', $request->direct_manager_user_id);
            });
        }

        if ($request->deduction_status) {
            if ($request->deduction_status === 'deducted') {
                $query->where('workflow_status', 'approved_by_hr');
            }

            if ($request->deduction_status === 'not_deducted') {
                $query->whereIn('workflow_status', [
                    'pending_manager',
                    'manager_approved_pending_hr',
                    'rejected_by_manager',
                    'rejected_by_hr',
                    'cancelled',
                ]);
            }

            if ($request->deduction_status === 'reversed') {
                $query->where('workflow_status', 'cancelled')
                    ->where('hr_status', 'cancelled_after_approval');
            }
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        return $query;
    }

    private function workflowStatuses(): array
    {
        return [
            'pending_manager' => 'بانتظار موافقة المدير المباشر',
            'manager_approved_pending_hr' => 'موافق من المدير - بانتظار الموارد البشرية',
            'approved_by_hr' => 'موافق نهائيًا من الموارد البشرية',
            'rejected_by_manager' => 'مرفوض من المدير المباشر',
            'rejected_by_hr' => 'مرفوض من الموارد البشرية',
            'cancelled' => 'ملغي',
        ];
    }
}
