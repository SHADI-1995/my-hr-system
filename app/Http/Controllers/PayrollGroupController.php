<?php

namespace App\Http\Controllers;

use App\Models\PayrollGroup;
use Illuminate\Http\Request;

class PayrollGroupController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.view'), 403);

        $query = PayrollGroup::withCount('employees')->orderBy('sort_order')->orderBy('name_ar');

        if ($request->search) {
            $query->where(fn($q) => $q->where('name_ar', 'like', '%' . $request->search . '%')
                ->orWhere('name_en', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $payrollGroups = $query->paginate(20)->withQueryString();

        return view('payroll_groups.index', compact('payrollGroups'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.create'), 403);
        return view('payroll_groups.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.create'), 403);

        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:100|unique:payroll_groups,code',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        PayrollGroup::create($data);

        return redirect()->route('payroll-groups.index')->with('success', 'تم إضافة مجموعة الرواتب بنجاح');
    }

    public function edit(PayrollGroup $payrollGroup)
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.edit'), 403);
        return view('payroll_groups.edit', compact('payrollGroup'));
    }

    public function update(Request $request, PayrollGroup $payrollGroup)
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.edit'), 403);

        $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100|unique:payroll_groups,code,' . $payrollGroup->id,
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = [];

        if (auth()->user()->hasPermission('payroll_groups.edit.name_ar')) $data['name_ar'] = $request->name_ar;
        if (auth()->user()->hasPermission('payroll_groups.edit.name_en')) $data['name_en'] = $request->name_en;
        if (auth()->user()->hasPermission('payroll_groups.edit.code')) $data['code'] = $request->code;
        if (auth()->user()->hasPermission('payroll_groups.edit.is_active')) $data['is_active'] = $request->boolean('is_active');
        if (auth()->user()->hasPermission('payroll_groups.edit.sort_order')) $data['sort_order'] = $request->sort_order ?? 0;
        if (auth()->user()->hasPermission('payroll_groups.edit.notes')) $data['notes'] = $request->notes;

        if (empty($data)) {
            return back()->with('error', 'لا تملك صلاحية تعديل أي حقل');
        }

        $payrollGroup->update($data);

        return redirect()->route('payroll-groups.index')->with('success', 'تم تعديل مجموعة الرواتب بنجاح');
    }

    public function destroy(PayrollGroup $payrollGroup)
    {
        abort_if(!auth()->user()->hasPermission('payroll_groups.delete'), 403);

        if ($payrollGroup->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف مجموعة مرتبطة بموظفين. يمكن تعطيلها بدل الحذف.');
        }

        $payrollGroup->delete();

        return redirect()->route('payroll-groups.index')->with('success', 'تم حذف مجموعة الرواتب بنجاح');
    }
}
