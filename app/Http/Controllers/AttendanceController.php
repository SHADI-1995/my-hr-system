<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('attendances.view'), 403);

        $query = Attendance::with('employee');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->attendance_date) {
            $query->whereDate('attendance_date', $request->attendance_date);
        }

        $attendances = $query->latest()->paginate(10);
        $employees = Employee::where('status', 'active')->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('attendances.create'), 403);

        $employees = Employee::where('status', 'active')->get();

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('attendances.create'), 403);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|in:present,absent,late,leave',
            'notes' => 'nullable|string',
        ]);

        Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function edit(Attendance $attendance)
    {
        abort_if(!auth()->user()->hasPermission('attendances.edit'), 403);

        $attendance->load('employee');
        $employees = Employee::where('status', 'active')->get();

        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        abort_if(!auth()->user()->hasPermission('attendances.edit'), 403);

        $data = [];

        if (auth()->user()->hasPermission('attendances.edit.employee_id')) {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
            ]);

            $data['employee_id'] = $request->employee_id;
        }

        if (auth()->user()->hasPermission('attendances.edit.attendance_date')) {
            $request->validate([
                'attendance_date' => 'required|date',
            ]);

            $data['attendance_date'] = $request->attendance_date;
        }

        if (auth()->user()->hasPermission('attendances.edit.check_in')) {
            $request->validate([
                'check_in' => 'nullable',
            ]);

            $data['check_in'] = $request->check_in;
        }

        if (auth()->user()->hasPermission('attendances.edit.check_out')) {
            $request->validate([
                'check_out' => 'nullable',
            ]);

            $data['check_out'] = $request->check_out;
        }

        if (auth()->user()->hasPermission('attendances.edit.status')) {
            $request->validate([
                'status' => 'required|in:present,absent,late,leave',
            ]);

            $data['status'] = $request->status;
        }

        if (auth()->user()->hasPermission('attendances.edit.notes')) {
            $request->validate([
                'notes' => 'nullable|string',
            ]);

            $data['notes'] = $request->notes;
        }

        $attendance->update($data);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'تم تعديل سجل الحضور بنجاح');
    }

    public function destroy(Attendance $attendance)
    {
        abort_if(!auth()->user()->hasPermission('attendances.delete'), 403);

        $attendance->delete();

        return redirect()
            ->route('attendances.index')
            ->with('success', 'تم حذف سجل الحضور بنجاح');
    }
}
