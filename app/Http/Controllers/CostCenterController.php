<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.view'), 403);

        $query = CostCenter::withCount('employees')->orderBy('sort_order')->orderBy('name_ar');

        if ($request->search) {
            $query->where(fn($q) => $q->where('name_ar', 'like', '%' . $request->search . '%')
                ->orWhere('name_en', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $costCenters = $query->paginate(20)->withQueryString();

        return view('cost_centers.index', compact('costCenters'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.create'), 403);
        return view('cost_centers.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.create'), 403);

        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:100|unique:cost_centers,code',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        CostCenter::create($data);

        return redirect()->route('cost-centers.index')->with('success', 'تم إضافة مركز التكلفة بنجاح');
    }

    public function edit(CostCenter $costCenter)
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.edit'), 403);
        return view('cost_centers.edit', compact('costCenter'));
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.edit'), 403);

        $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100|unique:cost_centers,code,' . $costCenter->id,
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = [];

        if (auth()->user()->hasPermission('cost_centers.edit.name_ar')) $data['name_ar'] = $request->name_ar;
        if (auth()->user()->hasPermission('cost_centers.edit.name_en')) $data['name_en'] = $request->name_en;
        if (auth()->user()->hasPermission('cost_centers.edit.code')) $data['code'] = $request->code;
        if (auth()->user()->hasPermission('cost_centers.edit.is_active')) $data['is_active'] = $request->boolean('is_active');
        if (auth()->user()->hasPermission('cost_centers.edit.sort_order')) $data['sort_order'] = $request->sort_order ?? 0;
        if (auth()->user()->hasPermission('cost_centers.edit.notes')) $data['notes'] = $request->notes;

        if (empty($data)) {
            return back()->with('error', 'لا تملك صلاحية تعديل أي حقل');
        }

        $costCenter->update($data);

        return redirect()->route('cost-centers.index')->with('success', 'تم تعديل مركز التكلفة بنجاح');
    }

    public function destroy(CostCenter $costCenter)
    {
        abort_if(!auth()->user()->hasPermission('cost_centers.delete'), 403);

        if ($costCenter->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف مركز مرتبط بموظفين. يمكن تعطيله بدل الحذف.');
        }

        $costCenter->delete();

        return redirect()->route('cost-centers.index')->with('success', 'تم حذف مركز التكلفة بنجاح');
    }
}
