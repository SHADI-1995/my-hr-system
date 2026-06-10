@extends('layouts.hr')

@section('title', 'تعديل حضور')
@section('page-title', 'تعديل سجل حضور')

@section('content')

    <div class="card">

        <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if(auth()->user()->hasPermission('attendances.edit.employee_id'))
                <div style="margin-bottom:15px;">
                    <label>الموظف</label>
                    <select name="employee_id" required style="width:100%;padding:10px;margin-top:5px;">
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الموظف</label>
                    <input
                        type="text"
                        value="{{ $attendance->employee->name ?? '-' }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.edit.attendance_date'))
                <div style="margin-bottom:15px;">
                    <label>التاريخ</label>
                    <input
                        type="date"
                        name="attendance_date"
                        value="{{ old('attendance_date', $attendance->attendance_date) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>التاريخ</label>
                    <input
                        type="text"
                        value="{{ $attendance->attendance_date }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.edit.check_in'))
                <div style="margin-bottom:15px;">
                    <label>وقت الدخول</label>
                    <input
                        type="time"
                        name="check_in"
                        value="{{ old('check_in', $attendance->check_in) }}"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>وقت الدخول</label>
                    <input
                        type="text"
                        value="{{ $attendance->check_in ?? '-' }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.edit.check_out'))
                <div style="margin-bottom:15px;">
                    <label>وقت الخروج</label>
                    <input
                        type="time"
                        name="check_out"
                        value="{{ old('check_out', $attendance->check_out) }}"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>وقت الخروج</label>
                    <input
                        type="text"
                        value="{{ $attendance->check_out ?? '-' }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.edit.status'))
                <div style="margin-bottom:15px;">
                    <label>الحالة</label>
                    <select name="status" required style="width:100%;padding:10px;margin-top:5px;">
                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>حاضر</option>
                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>غائب</option>
                        <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>متأخر</option>
                        <option value="leave" {{ old('status', $attendance->status) == 'leave' ? 'selected' : '' }}>إجازة</option>
                    </select>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الحالة</label>
                    <div style="margin-top:5px;">
                        @if($attendance->status == 'present')
                            <span class="badge badge-active">حاضر</span>
                        @elseif($attendance->status == 'late')
                            <span class="badge badge-active">متأخر</span>
                        @elseif($attendance->status == 'leave')
                            <span class="badge badge-active">إجازة</span>
                        @else
                            <span class="badge badge-inactive">غائب</span>
                        @endif
                    </div>
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.edit.notes'))
                <div style="margin-bottom:15px;">
                    <label>ملاحظات</label>
                    <textarea
                        name="notes"
                        rows="4"
                        style="width:100%;padding:10px;margin-top:5px;">{{ old('notes', $attendance->notes) }}</textarea>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>ملاحظات</label>
                    <textarea
                        rows="4"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">{{ $attendance->notes }}</textarea>
                </div>
            @endif

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث
            </button>

            <a href="{{ route('attendances.index') }}" class="btn">
                رجوع
            </a>
        </form>

    </div>

@endsection
