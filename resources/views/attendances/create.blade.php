@extends('layouts.hr')

@section('title', 'إضافة حضور')
@section('page-title', 'إضافة سجل حضور')

@section('content')

    <div class="card">

        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf

            <label>الموظف</label>
            <select name="employee_id" required>
                <option value="">اختر الموظف</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>

            <br><br>

            <label>التاريخ</label>
            <input type="date" name="attendance_date" required>

            <br><br>

            <label>وقت الدخول</label>
            <input type="time" name="check_in">

            <br><br>

            <label>وقت الخروج</label>
            <input type="time" name="check_out">

            <br><br>

            <label>الحالة</label>
            <select name="status" required>
                <option value="present">حاضر</option>
                <option value="absent">غائب</option>
                <option value="late">متأخر</option>
                <option value="leave">إجازة</option>
            </select>

            <br><br>

            <label>ملاحظات</label>
            <textarea name="notes" rows="4"></textarea>

            <br><br>

            <button type="submit" class="btn">حفظ</button>
        </form>

    </div>

@endsection
