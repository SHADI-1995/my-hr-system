<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSuspension;
use Illuminate\Http\Request;

class EmployeeSuspensionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_suspensions.view'), 403);

        $query = EmployeeSuspension::with(['employee.department', 'createdBy', 'approvedBy', 'resumedBy']);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $suspensions = $query->latest()->paginate(20);

        return view('employee_suspensions.index', compact('suspensions'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('employee_suspensions.create'), 403);

        $employees = Employee::orderBy('full_name')->get();

        return view('employee_suspensions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_suspensions.create'), 403);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'resume_date' => 'nullable|date|after:start_date',
            'salary_percentage' => 'required|numeric|min:0|max:100',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'start_date.required' => 'تاريخ بداية الإيقاف مطلوب',
            'salary_percentage.required' => 'نسبة الراتب أثناء الإيقاف مطلوبة',
            'reason.required' => 'سبب الإيقاف مطلوب',
            'resume_date.after' => 'تاريخ العودة يجب أن يكون بعد تاريخ الإيقاف',
        ]);

        $data['status'] = $request->filled('resume_date') ? 'resumed' : 'active';
        $data['created_by'] = auth()->id();
        $data['approved_by'] = auth()->id();
        $data['approved_at'] = now();

        if ($request->filled('resume_date')) {
            $data['resumed_by'] = auth()->id();
            $data['resumed_at'] = now();
        }

        EmployeeSuspension::create($data);

        return redirect()
            ->route('employee-suspensions.index')
            ->with('success', 'تم تسجيل إيقاف الموظف بنجاح');
    }

    public function resume(Request $request, EmployeeSuspension $employeeSuspension)
    {
        abort_if(!auth()->user()->hasPermission('employee_suspensions.resume'), 403);

        if ($employeeSuspension->status !== 'active') {
            return back()->with('error', 'هذا الإيقاف ليس نشطًا');
        }

        $data = $request->validate([
            'resume_date' => 'required|date|after:start_date',
        ], [
            'resume_date.required' => 'تاريخ العودة للعمل مطلوب',
            'resume_date.after' => 'تاريخ العودة يجب أن يكون بعد تاريخ الإيقاف',
        ]);

        $employeeSuspension->update([
            'resume_date' => $data['resume_date'],
            'status' => 'resumed',
            'resumed_by' => auth()->id(),
            'resumed_at' => now(),
        ]);

        return back()->with('success', 'تم استئناف الموظف بنجاح');
    }

    public function cancel(Request $request, EmployeeSuspension $employeeSuspension)
    {
        abort_if(!auth()->user()->hasPermission('employee_suspensions.cancel'), 403);

        if ($employeeSuspension->status === 'cancelled') {
            return back()->with('error', 'الإيقاف ملغي مسبقًا');
        }

        $employeeSuspension->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason,
        ]);

        return back()->with('success', 'تم إلغاء الإيقاف بنجاح');
    }
}
