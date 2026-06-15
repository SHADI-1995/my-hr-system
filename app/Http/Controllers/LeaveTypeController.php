<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.view'), 403);

        $query = LeaveType::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $leaveTypes = $query
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('leave_types.create'), 403);

        return view('leave_types.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.create'), 403);

        $data = $this->validateLeaveType($request);

        LeaveType::create($data);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'تم إضافة نوع الإجازة بنجاح');
    }

    public function edit(LeaveType $leaveType)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.edit'), 403);

        return view('leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.edit'), 403);

        $data = $this->validateLeaveType($request, $leaveType);

        $leaveType->update($data);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'تم تعديل نوع الإجازة بنجاح');
    }

    public function toggleStatus(LeaveType $leaveType)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.edit'), 403);

        $leaveType->update([
            'is_active' => !$leaveType->is_active,
        ]);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'تم تحديث حالة نوع الإجازة بنجاح');
    }

    public function destroy(LeaveType $leaveType)
    {
        abort_if(!auth()->user()->hasPermission('leave_types.delete'), 403);

        if (method_exists($leaveType, 'leaveRequests') && $leaveType->leaveRequests()->exists()) {
            return redirect()
                ->route('leave-types.index')
                ->with('error', 'لا يمكن حذف نوع الإجازة لأنه مرتبط بطلبات إجازات');
        }

        $leaveType->delete();

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'تم حذف نوع الإجازة بنجاح');
    }

    private function validateLeaveType(Request $request, ?LeaveType $leaveType = null): array
    {
        $leaveTypeId = $leaveType?->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $leaveTypeId,
            'max_days_per_year' => 'nullable|integer|min:0|max:365',
            'is_paid' => 'nullable|boolean',
            'deduct_from_annual_balance' => 'nullable|boolean',
            'requires_attachment' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'auto_approved' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',

            // إعدادات تأثير نوع الإجازة على مسير الرواتب
            'affects_payroll' => 'nullable|boolean',
            'salary_percentage' => 'required|numeric|min:0|max:100',
            'payroll_policy_note' => 'nullable|string',
        ], [
            'name.required' => 'اسم نوع الإجازة مطلوب',
            'code.required' => 'كود نوع الإجازة مطلوب',
            'code.unique' => 'كود نوع الإجازة موجود مسبقًا',
            'salary_percentage.required' => 'نسبة الراتب أثناء الإجازة مطلوبة',
            'salary_percentage.numeric' => 'نسبة الراتب أثناء الإجازة يجب أن تكون رقم',
            'salary_percentage.min' => 'نسبة الراتب أثناء الإجازة لا يمكن أن تكون أقل من 0',
            'salary_percentage.max' => 'نسبة الراتب أثناء الإجازة لا يمكن أن تكون أكثر من 100',
        ]);

        return [
            'name' => $request->name,
            'code' => strtolower(trim($request->code)),
            'is_paid' => $request->boolean('is_paid'),
            'deduct_from_annual_balance' => $request->boolean('deduct_from_annual_balance'),
            'requires_attachment' => $request->boolean('requires_attachment'),
            'requires_approval' => $request->boolean('requires_approval'),
            'auto_approved' => $request->boolean('auto_approved'),
            'max_days_per_year' => $request->max_days_per_year,
            'is_active' => $request->boolean('is_active'),

            // إعدادات مسير الرواتب
            'affects_payroll' => $request->boolean('affects_payroll'),
            'salary_percentage' => $request->salary_percentage ?? 100,
            'payroll_policy_note' => $request->payroll_policy_note,
        ];
    }
}
