@extends('layouts.hr')

@section('title', 'إيقاف موظف')
@section('page-title', 'إيقاف موظف')

@section('content')
    <style>
        .form-card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07)}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:7px}.field input,.field select,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:11px 12px;font-weight:800}.field textarea{min-height:90px}.full{grid-column:1/-1}.btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none}.primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}@media(max-width:800px){.grid{grid-template-columns:1fr}}
    </style>
    <div class="form-card">
        <form method="POST" action="{{ route('employee-suspensions.store') }}">
            @csrf
            <div class="grid">
                <div class="field"><label>الموظف</label><select name="employee_id" required><option value="">اختر الموظف</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected(old('employee_id')==$employee->id)>{{ $employee->display_name }} - {{ $employee->employee_number }}</option>@endforeach</select>@error('employee_id')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>نسبة الراتب أثناء الإيقاف</label><select name="salary_percentage" required><option value="0">0% - بدون راتب</option><option value="50">50% - نصف راتب</option><option value="100">100% - براتب كامل</option></select>@error('salary_percentage')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>تاريخ بداية الإيقاف</label><input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required>@error('start_date')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>تاريخ العودة للعمل اختياري</label><input type="date" name="resume_date" value="{{ old('resume_date') }}"><small>إذا عاد الموظف يوم 18، يتم خصم 14 إلى 17 فقط.</small>@error('resume_date')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field full"><label>سبب الإيقاف</label><textarea name="reason" required>{{ old('reason') }}</textarea>@error('reason')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field full"><label>ملاحظات</label><textarea name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div style="margin-top:18px;display:flex;gap:10px">
                <button class="btn2 primary">حفظ الإيقاف</button>
                <a class="btn2 soft" href="{{ route('employee-suspensions.index') }}">رجوع</a>
            </div>
        </form>
    </div>
@endsection

