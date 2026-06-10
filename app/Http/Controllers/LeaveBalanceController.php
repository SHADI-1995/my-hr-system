<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_balances.view'), 403);

        $query = EmployeeLeaveBalance::with([
            'employee.department',
            'employee.position',
            'leavePolicy',
        ]);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('second_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->year_label) {
            $query->where('year_label', $request->year_label);
        }

        $leaveBalances = $query
            ->latest('service_year_start')
            ->paginate(20)
            ->withQueryString();

        $departments = \App\Models\Department::orderBy('name')->get();

        $yearLabels = EmployeeLeaveBalance::query()
            ->whereNotNull('year_label')
            ->distinct()
            ->orderByDesc('year_label')
            ->pluck('year_label');

        $totals = [
            'annual_entitled_days' => (clone $query)->sum('annual_entitled_days'),
            'carried_forward_days' => (clone $query)->sum('carried_forward_days'),
            'used_paid_days' => (clone $query)->sum('used_paid_days'),
            'used_unpaid_days' => (clone $query)->sum('used_unpaid_days'),
            'remaining_days' => (clone $query)->sum('remaining_days'),
        ];

        return view('leave_balances.index', compact(
            'leaveBalances',
            'departments',
            'yearLabels',
            'totals'
        ));
    }

    public function show(Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('leave_balances.view'), 403);

        $employee->load([
            'department',
            'position',
            'leaveBalances.leavePolicy',
        ]);

        $balances = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->with('leavePolicy')
            ->latest('service_year_start')
            ->get();

        $transactions = EmployeeLeaveTransaction::where('employee_id', $employee->id)
            ->with(['leaveBalance', 'createdBy'])
            ->latest()
            ->paginate(30);

        return view('leave_balances.show', compact(
            'employee',
            'balances',
            'transactions'
        ));
    }
}
