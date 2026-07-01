<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvanceRequest;
use Illuminate\Http\Request;

class SalaryAdvanceRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryAdvanceRequest::query()
            ->with(['employee.department', 'employee.position', 'registeredSalaryAdvance'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('employee_number', 'like', "%{$search}%")
                            ->orWhere('national_id', 'like', "%{$search}%")
                            ->orWhere('iqama_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->workflow_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $salaryAdvanceRequests = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => SalaryAdvanceRequest::count(),
            'pending_manager' => SalaryAdvanceRequest::where('workflow_status', 'pending_manager')->count(),
            'pending_hr' => SalaryAdvanceRequest::where('workflow_status', 'manager_approved_pending_hr')->count(),
            'registered' => SalaryAdvanceRequest::where(function ($q) {
                $q->where('workflow_status', 'registered')->orWhere('status', 'approved');
            })->count(),
            'rejected' => SalaryAdvanceRequest::whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])->count(),
            'cancelled' => SalaryAdvanceRequest::where('workflow_status', 'cancelled')->count(),
        ];

        return view('salary_advance_requests.index', compact('salaryAdvanceRequests', 'stats'));
    }

    public function show(SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        $salaryAdvanceRequest->load([
            'employee.department',
            'employee.position',
            'registeredSalaryAdvance.installments',
            'logs',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
            'hrApprovedBy',
            'hrRejectedBy',
        ]);

        return view('salary_advance_requests.show', compact('salaryAdvanceRequest'));
    }
}
