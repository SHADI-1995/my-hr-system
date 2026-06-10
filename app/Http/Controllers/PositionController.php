<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('positions.view'), 403);

        $query = Position::with('department');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $positions = $query->latest()->paginate(10);
        $departments = Department::where('is_active', 1)->get();

        return view('positions.index', compact('positions', 'departments'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('positions.create'), 403);

        $departments = Department::where('is_active', 1)->get();

        return view('positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('positions.create'), 403);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:positions,code',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|in:1',
        ]);

        Position::create([
            'department_id' => $request->department_id,
            'title' => $request->title,
            'code' => $request->code,
            'min_salary' => $request->min_salary ?? 0,
            'max_salary' => $request->max_salary ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()
            ->route('positions.index')
            ->with('success', 'تم إضافة الوظيفة بنجاح');
    }

    public function edit(Position $position)
    {
        abort_if(!auth()->user()->hasPermission('positions.edit'), 403);

        $departments = Department::where('is_active', 1)->get();

        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        abort_if(!auth()->user()->hasPermission('positions.edit'), 403);

        $data = [];

        if (auth()->user()->hasPermission('positions.edit.department_id')) {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
            ]);

            $data['department_id'] = $request->department_id;
        }

        if (auth()->user()->hasPermission('positions.edit.title')) {
            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $data['title'] = $request->title;
        }

        if (auth()->user()->hasPermission('positions.edit.code')) {
            $request->validate([
                'code' => 'required|string|max:255|unique:positions,code,' . $position->id,
            ]);

            $data['code'] = $request->code;
        }

        if (auth()->user()->hasPermission('positions.edit.min_salary')) {
            $request->validate([
                'min_salary' => 'nullable|numeric|min:0',
            ]);

            $data['min_salary'] = $request->min_salary ?? 0;
        }

        if (auth()->user()->hasPermission('positions.edit.max_salary')) {
            $request->validate([
                'max_salary' => 'nullable|numeric|min:0',
            ]);

            $data['max_salary'] = $request->max_salary ?? 0;
        }

        if (auth()->user()->hasPermission('positions.edit.is_active')) {
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
        }

        $position->update($data);

        return redirect()
            ->route('positions.index')
            ->with('success', 'تم تعديل الوظيفة بنجاح');
    }

    public function destroy(Position $position)
    {
        abort_if(!auth()->user()->hasPermission('positions.delete'), 403);

        if ($position->employees()->count() > 0) {
            return redirect()
                ->route('positions.index')
                ->with('error', 'لا يمكن حذف الوظيفة لأنها مرتبطة بموظفين');
        }

        $position->delete();

        return redirect()
            ->route('positions.index')
            ->with('success', 'تم حذف الوظيفة بنجاح');
    }
}
