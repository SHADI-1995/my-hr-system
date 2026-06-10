@extends('layouts.hr')

@section('title', 'إضافة راتب')
@section('page-title', 'إضافة راتب')

@section('content')

    <div class="card">

        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf

            <label>الموظف</label>
            <select name="employee_id" required>
                <option value="">اختر الموظف</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>

            <br><br>

            <label>الشهر</label>
            <input type="month" name="month" required>

            <br><br>

            <label>الراتب الأساسي</label>
            <input type="number" name="basic_salary" value="0" required>

            <br><br>

            <label>البدلات</label>
            <input type="number" name="allowances" value="0">

            <br><br>

            <label>الخصومات</label>
            <input type="number" name="deductions" value="0">

            <br><br>

            <label>الحالة</label>
            <select name="status" required>
                <option value="draft">مسودة</option>
                <option value="paid">مدفوع</option>
            </select>

            <br><br>

            <button type="submit" class="btn">حفظ</button>
        </form>

    </div>

@endsection
