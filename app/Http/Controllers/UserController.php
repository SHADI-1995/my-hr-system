<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('users.view'), 403);

        $query = User::with('role');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('users.create'), 403);

        $roles = Role::where('is_active', 1)->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('users.create'), 403);

        $request->validate([
            'name' => 'required',
            'username' => 'nullable|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.edit'), 403);

        $roles = Role::where('is_active', 1)->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.edit'), 403);

        $request->validate([
            'name' => 'required',
            'username' => 'nullable|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.delete'), 403);

        if (auth()->id() == $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'لا يمكنك حذف حسابك الحالي');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}
