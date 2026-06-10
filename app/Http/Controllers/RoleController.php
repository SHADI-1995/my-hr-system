<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('roles.view'), 403);

        $query = Role::withCount(['users', 'permissions']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $roles = $query->latest()->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('roles.create'), 403);

        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('roles.create'), 403);

        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:roles,code',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->is_active ?? 0,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'تم إضافة الدور بنجاح');
    }

    public function edit(Role $role)
    {
        abort_if(!auth()->user()->hasPermission('roles.edit'), 403);

        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if(!auth()->user()->hasPermission('roles.edit'), 403);

        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:roles,code,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->is_active ?? 0,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'تم تعديل الدور والصلاحيات بنجاح');
    }

    public function destroy(Role $role)
    {
        abort_if(!auth()->user()->hasPermission('roles.delete'), 403);

        if ($role->users()->count() > 0) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'لا يمكن حذف هذا الدور لأنه مرتبط بمستخدمين');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
}
