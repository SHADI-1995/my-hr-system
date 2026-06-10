<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;

class ReportController extends Controller
{
    public function index()
    { abort_if(!auth()->user()->hasPermission('employees.create'), 403);
        $employeesCount = Employee::count();
        $departmentsCount = Department::count();
        $attendanceCount = Attendance::count();
        $leaveRequestsCount = LeaveRequest::count();
        $payrollsCount = Payroll::count();

        return view('reports.index', compact(
            'employeesCount',
            'departmentsCount',
            'attendanceCount',
            'leaveRequestsCount',
            'payrollsCount'
        ));
    }
}
