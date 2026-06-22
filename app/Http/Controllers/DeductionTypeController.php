<?php

namespace App\Http\Controllers;

use App\Models\DeductionType;
use Illuminate\Http\Request;

class DeductionTypeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.view'), 403);

        $query = DeductionType::query()->withCount('deductions');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        $types = $query->orderBy('sort_order')->orderBy('name_ar')->paginate(20)->withQueryString();

        return view('deduction_types.index', compact('types'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.create'), 403);

        return view('deduction_types.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.create'), 403);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:deduction_types,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'code.required' => 'كود نوع الاستقطاع مطلوب',
            'code.unique' => 'كود نوع الاستقطاع مستخدم من قبل',
            'name_ar.required' => 'اسم نوع الاستقطاع مطلوب',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_by'] = auth()->id();

        DeductionType::create($data);

        return redirect()->route('deduction-types.index')->with('success', 'تم إضافة نوع الاستقطاع بنجاح');
    }

    public function edit(DeductionType $deductionType)
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.edit'), 403);

        return view('deduction_types.edit', compact('deductionType'));
    }

    public function update(Request $request, DeductionType $deductionType)
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.edit'), 403);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:deduction_types,code,' . $deductionType->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'code.required' => 'كود نوع الاستقطاع مطلوب',
            'code.unique' => 'كود نوع الاستقطاع مستخدم من قبل',
            'name_ar.required' => 'اسم نوع الاستقطاع مطلوب',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $deductionType->update($data);

        return redirect()->route('deduction-types.index')->with('success', 'تم تحديث نوع الاستقطاع بنجاح');
    }

    public function destroy(DeductionType $deductionType)
    {
        abort_if(!auth()->user()->hasPermission('deduction_types.delete'), 403);

        if ($deductionType->deductions()->exists()) {
            return back()->with('error', 'لا يمكن حذف نوع الاستقطاع لأنه مرتبط باستقطاعات موظفين');
        }

        $deductionType->delete();

        return back()->with('success', 'تم حذف نوع الاستقطاع بنجاح');
    }
}
