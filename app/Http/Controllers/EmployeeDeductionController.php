<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeDeductionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.view'), 403);

        $query = EmployeeDeduction::with(['employee.department', 'createdBy', 'approvedBy']);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->deduction_type) {
            $query->where('deduction_type', $request->deduction_type);
        }

        $deductions = $query->latest()->paginate(20);

        return view('employee_deductions.index', compact('deductions'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.create'), 403);

        $employees = Employee::orderBy('full_name')->get();

        return view('employee_deductions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.create'), 403);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'deduction_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'deduction_mode' => 'required|in:one_time,monthly,installments,percentage',
            'installments_count' => 'nullable|required_if:deduction_mode,installments|integer|min:1',
            'monthly_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'amount.required' => 'مبلغ الاستقطاع مطلوب',
            'deduction_type.required' => 'نوع الاستقطاع مطلوب',
            'start_date.required' => 'تاريخ بداية الاستقطاع مطلوب',
        ]);

        DB::transaction(function () use ($data) {
            $data['deduction_number'] = EmployeeDeduction::generateNumber();
            $data['status'] = 'pending';
            $data['created_by'] = auth()->id();

            EmployeeDeduction::create($data);
        });

        return redirect()
            ->route('employee-deductions.index')
            ->with('success', 'تم إضافة الاستقطاع بنجاح، بانتظار الاعتماد');
    }

    public function approve(EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.approve'), 403);

        if ($employeeDeduction->status !== 'pending') {
            return back()->with('error', 'لا يمكن اعتماد هذا الاستقطاع في حالته الحالية');
        }

        $employeeDeduction->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد الاستقطاع بنجاح');
    }

    public function cancel(Request $request, EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.cancel'), 403);

        if (!in_array($employeeDeduction->status, ['pending', 'approved'], true)) {
            return back()->with('error', 'لا يمكن إلغاء هذا الاستقطاع');
        }

        $employeeDeduction->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason,
        ]);

        return back()->with('success', 'تم إلغاء الاستقطاع بنجاح');
    }
}
