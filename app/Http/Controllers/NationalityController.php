<?php

namespace App\Http\Controllers;

use App\Models\Nationality;
use Illuminate\Http\Request;

class NationalityController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.view'), 403);

        $query = Nationality::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $nationalities = $query
            ->withCount('employees')
            ->orderBy('name_ar')
            ->paginate(20)
            ->withQueryString();

        return view('nationalities.index', compact('nationalities'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('nationalities.create'), 403);
        return view('nationalities.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.create'), 403);

        $request->validate([
            'name_ar' => 'required|string|max:255|unique:nationalities,name_ar',
            'name_en' => 'nullable|string|max:255|unique:nationalities,name_en',
            'code' => 'nullable|string|max:10|unique:nationalities,code',
            'is_active' => 'nullable|boolean',
        ], [
            'name_ar.required' => 'اسم الجنسية بالعربي مطلوب',
            'name_ar.unique' => 'اسم الجنسية بالعربي موجود مسبقًا',
            'name_en.unique' => 'اسم الجنسية بالإنجليزي موجود مسبقًا',
            'code.unique' => 'رمز الجنسية موجود مسبقًا',
        ]);

        Nationality::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'code' => $request->code ? strtoupper($request->code) : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('nationalities.index')->with('success', 'تم إضافة الجنسية بنجاح');
    }

    public function edit(Nationality $nationality)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.edit'), 403);
        return view('nationalities.edit', compact('nationality'));
    }

    public function update(Request $request, Nationality $nationality)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.edit'), 403);

        $request->validate([
            'name_ar' => 'required|string|max:255|unique:nationalities,name_ar,' . $nationality->id,
            'name_en' => 'nullable|string|max:255|unique:nationalities,name_en,' . $nationality->id,
            'code' => 'nullable|string|max:10|unique:nationalities,code,' . $nationality->id,
            'is_active' => 'nullable|boolean',
        ], [
            'name_ar.required' => 'اسم الجنسية بالعربي مطلوب',
            'name_ar.unique' => 'اسم الجنسية بالعربي موجود مسبقًا',
            'name_en.unique' => 'اسم الجنسية بالإنجليزي موجود مسبقًا',
            'code.unique' => 'رمز الجنسية موجود مسبقًا',
        ]);

        $nationality->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'code' => $request->code ? strtoupper($request->code) : null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('nationalities.index')->with('success', 'تم تعديل الجنسية بنجاح');
    }

    public function destroy(Nationality $nationality)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.delete'), 403);

        if ($nationality->employees()->exists()) {
            return redirect()->route('nationalities.index')->with('error', 'لا يمكن حذف الجنسية لأنها مرتبطة بموظفين');
        }

        $nationality->delete();

        return redirect()->route('nationalities.index')->with('success', 'تم حذف الجنسية بنجاح');
    }

    public function toggleStatus(Nationality $nationality)
    {
        abort_if(!auth()->user()->hasPermission('nationalities.edit'), 403);

        $nationality->update([
            'is_active' => !$nationality->is_active,
        ]);

        return redirect()->route('nationalities.index')->with('success', 'تم تحديث حالة الجنسية بنجاح');
    }
}
