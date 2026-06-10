<?php

namespace App\Http\Controllers;

use App\Models\LeavePolicy;
use Illuminate\Http\Request;

class LeavePolicyController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.view'), 403);

        $policies = LeavePolicy::orderByDesc('is_active')->latest()->paginate(20);

        return view('leave_policies.index', compact('policies'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.create'), 403);

        return view('leave_policies.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.create'), 403);

        $data = $this->validatePolicy($request);

        if ($request->boolean('is_active')) {
            LeavePolicy::where('is_active', true)->update(['is_active' => false]);
        }

        LeavePolicy::create($data);

        return redirect()
            ->route('leave-policies.index')
            ->with('success', 'تم إضافة سياسة الإجازات بنجاح');
    }

    public function edit(LeavePolicy $leavePolicy)
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.edit'), 403);

        return view('leave_policies.edit', compact('leavePolicy'));
    }

    public function update(Request $request, LeavePolicy $leavePolicy)
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.edit'), 403);

        $data = $this->validatePolicy($request);

        if ($request->boolean('is_active')) {
            LeavePolicy::where('id', '!=', $leavePolicy->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $leavePolicy->update($data);

        return redirect()
            ->route('leave-policies.index')
            ->with('success', 'تم تعديل سياسة الإجازات بنجاح');
    }

    public function activate(LeavePolicy $leavePolicy)
    {
        abort_if(!auth()->user()->hasPermission('leave_policies.edit'), 403);

        LeavePolicy::where('is_active', true)->update(['is_active' => false]);
        $leavePolicy->update(['is_active' => true]);

        return redirect()
            ->route('leave-policies.index')
            ->with('success', 'تم تفعيل سياسة الإجازات بنجاح');
    }

    private function validatePolicy(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'annual_days_before_5_years' => 'required|integer|min:0|max:365',
            'annual_days_after_5_years' => 'required|integer|min:0|max:365',
            'after_years' => 'required|integer|min:1|max:50',
            'leave_year_type' => 'required|in:hire_date,gregorian,hijri',
            'max_carry_forward_days' => 'nullable|integer|min:0|max:365',
        ], [
            'name.required' => 'اسم سياسة الإجازات مطلوب',
            'annual_days_before_5_years.required' => 'عدد أيام الإجازة قبل 5 سنوات مطلوب',
            'annual_days_after_5_years.required' => 'عدد أيام الإجازة بعد 5 سنوات مطلوب',
            'after_years.required' => 'عدد سنوات زيادة الاستحقاق مطلوب',
            'leave_year_type.required' => 'طريقة احتساب سنة الإجازة مطلوبة',
        ]);

        return [
            'name' => $request->name,
            'annual_days_before_5_years' => $request->annual_days_before_5_years,
            'annual_days_after_5_years' => $request->annual_days_after_5_years,
            'after_years' => $request->after_years,
            'leave_year_type' => $request->leave_year_type,
            'carry_forward_enabled' => $request->boolean('carry_forward_enabled'),
            'max_carry_forward_days' => $request->max_carry_forward_days ?? 0,
            'exclude_weekends' => $request->boolean('exclude_weekends'),
            'exclude_official_holidays' => $request->boolean('exclude_official_holidays'),
            'inactive_employee_accrual' => $request->boolean('inactive_employee_accrual'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
