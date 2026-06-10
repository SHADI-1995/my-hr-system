@extends('layouts.hr')

@section('title', 'إضافة وظيفة')
@section('page-title', 'إضافة وظيفة')

@section('content')

    <div class="card">

        <form action="{{ route('positions.store') }}" method="POST">

            @csrf

            <label>القسم</label>

            <select name="department_id">

                @foreach($departments as $department)

                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>

                @endforeach

            </select>

            <br><br>

            <label>المسمى الوظيفي</label>
            <input type="text" name="title">

            <br><br>

            <label>كود الوظيفة</label>
            <input type="text" name="code">

            <br><br>

            <label>الحد الأدنى للراتب</label>
            <input type="number" name="min_salary">

            <br><br>

            <label>الحد الأعلى للراتب</label>
            <input type="number" name="max_salary">

            <br><br>

            <label>
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       checked>
                وظيفة نشطة
            </label>

            <br><br>

            <button class="btn">
                حفظ
            </button>

        </form>

    </div>

@endsection
