<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payrolls.view'), 403);

        $query = Payroll::with('employee');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest()->paginate(10);
        $employees = Employee::where('status', 'active')->get();

        return view('payrolls.index', compact('payrolls', 'employees'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('payrolls.create'), 403);

        $employees = Employee::where('status', 'active')->get();

        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payrolls.create'), 403);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,paid',
        ]);

        $allowances = $request->allowances ?? 0;
        $deductions = $request->deductions ?? 0;
        $netSalary = ($request->basic_salary + $allowances) - $deductions;

        Payroll::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'basic_salary' => $request->basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('payrolls.index')
            ->with('success', 'تم إضافة الراتب بنجاح');
    }

    public function edit(Payroll $payroll)
    {
        abort_if(!auth()->user()->hasPermission('payrolls.edit'), 403);

        $payroll->load('employee');
        $employees = Employee::where('status', 'active')->get();

        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        abort_if(!auth()->user()->hasPermission('payrolls.edit'), 403);

        $data = [];

        if (auth()->user()->hasPermission('payrolls.edit.employee_id')) {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
            ]);

            $data['employee_id'] = $request->employee_id;
        }

        if (auth()->user()->hasPermission('payrolls.edit.month')) {
            $request->validate([
                'month' => 'required',
            ]);

            $data['month'] = $request->month;
        }

        if (auth()->user()->hasPermission('payrolls.edit.basic_salary')) {
            $request->validate([
                'basic_salary' => 'required|numeric|min:0',
            ]);

            $data['basic_salary'] = $request->basic_salary;
        }

        if (auth()->user()->hasPermission('payrolls.edit.allowances')) {
            $request->validate([
                'allowances' => 'nullable|numeric|min:0',
            ]);

            $data['allowances'] = $request->allowances ?? 0;
        }

        if (auth()->user()->hasPermission('payrolls.edit.deductions')) {
            $request->validate([
                'deductions' => 'nullable|numeric|min:0',
            ]);

            $data['deductions'] = $request->deductions ?? 0;
        }

        if (auth()->user()->hasPermission('payrolls.edit.status')) {
            $request->validate([
                'status' => 'required|in:draft,paid',
            ]);

            $data['status'] = $request->status;
        }

        $basicSalary = $data['basic_salary'] ?? $payroll->basic_salary;
        $allowances = $data['allowances'] ?? $payroll->allowances;
        $deductions = $data['deductions'] ?? $payroll->deductions;

        $data['net_salary'] = ($basicSalary + $allowances) - $deductions;

        $payroll->update($data);

        return redirect()
            ->route('payrolls.index')
            ->with('success', 'تم تعديل الراتب بنجاح');
    }

    public function destroy(Payroll $payroll)
    {
        abort_if(!auth()->user()->hasPermission('payrolls.delete'), 403);

        $payroll->delete();

        return redirect()
            ->route('payrolls.index')
            ->with('success', 'تم حذف الراتب بنجاح');
    }
}
