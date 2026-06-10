<?php

namespace App\Http\Controllers;

use App\Models\OfficialHoliday;
use Illuminate\Http\Request;

class OfficialHolidayController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.view'), 403);

        $query = OfficialHoliday::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('year_label', 'like', '%' . $request->search . '%');
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        if ($request->year_label) {
            $query->where('year_label', $request->year_label);
        }

        $officialHolidays = $query
            ->orderByDesc('start_date')
            ->paginate(20)
            ->withQueryString();

        $years = OfficialHoliday::query()
            ->whereNotNull('year_label')
            ->distinct()
            ->orderByDesc('year_label')
            ->pluck('year_label');

        return view('official_holidays.index', compact('officialHolidays', 'years'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.create'), 403);

        return view('official_holidays.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.create'), 403);

        $data = $this->validateOfficialHoliday($request);

        OfficialHoliday::create($data);

        return redirect()
            ->route('official-holidays.index')
            ->with('success', 'تم إضافة الإجازة الرسمية بنجاح');
    }

    public function edit(OfficialHoliday $officialHoliday)
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.edit'), 403);

        return view('official_holidays.edit', compact('officialHoliday'));
    }

    public function update(Request $request, OfficialHoliday $officialHoliday)
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.edit'), 403);

        $data = $this->validateOfficialHoliday($request);

        $officialHoliday->update($data);

        return redirect()
            ->route('official-holidays.index')
            ->with('success', 'تم تعديل الإجازة الرسمية بنجاح');
    }

    public function destroy(OfficialHoliday $officialHoliday)
    {
        abort_if(!auth()->user()->hasPermission('official_holidays.delete'), 403);

        $officialHoliday->delete();

        return redirect()
            ->route('official-holidays.index')
            ->with('success', 'تم حذف الإجازة الرسمية بنجاح');
    }

    private function validateOfficialHoliday(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|string|max:50',
            'year_label' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'اسم الإجازة الرسمية مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ نهاية الإجازة يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        return [
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'year_label' => $request->year_label,
            'is_active' => $request->boolean('is_active'),
            'notes' => $request->notes,
        ];
    }
}
