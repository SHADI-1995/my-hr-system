<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('departments.view'), 403);

        $query = Department::withCount('employees');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $departments = $query->latest()->paginate(10);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('departments.create'), 403);

        return view('departments.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('departments.create'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:departments,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:1',
        ], [
            'name.required' => 'اسم القسم مطلوب',
            'code.required' => 'كود القسم مطلوب',
            'code.unique' => 'كود القسم مستخدم من قبل، الرجاء اختيار كود آخر',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    public function edit(Department $department)
    {
        abort_if(!auth()->user()->hasPermission('departments.edit'), 403);

        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        abort_if(!auth()->user()->hasPermission('departments.edit'), 403);

        $data = [];

        if (auth()->user()->hasPermission('departments.edit.name')) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $data['name'] = $request->name;
        }

        if (auth()->user()->hasPermission('departments.edit.code')) {
            $request->validate([
                'code' => 'required|string|max:255|unique:departments,code,' . $department->id,
            ], [
                'code.unique' => 'كود القسم مستخدم من قبل، الرجاء اختيار كود آخر',
            ]);

            $data['code'] = $request->code;
        }

        if ($request->has('description')) {
            $data['description'] = $request->description;
        }

        if (auth()->user()->hasPermission('departments.edit.is_active')) {
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
        }

        $department->update($data);

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم تعديل القسم بنجاح');
    }

    public function destroy(Department $department)
    {
        abort_if(!auth()->user()->hasPermission('departments.delete'), 403);

        if ($department->employees()->count() > 0) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'لا يمكن حذف القسم لأنه مرتبط بموظفين');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'تم حذف القسم بنجاح');
    }
}
