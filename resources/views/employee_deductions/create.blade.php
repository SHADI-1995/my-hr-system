@extends('layouts.hr')

@section('title', 'إضافة استقطاع')
@section('page-title', 'إضافة استقطاع')

@section('content')
    <style>
        .form-card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:7px}.field input,.field select,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:11px 12px;font-weight:800}.field textarea{min-height:90px}.full{grid-column:1/-1}.btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none}.primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}@media(max-width:800px){.grid{grid-template-columns:1fr}}
    </style>
    <div class="form-card">
        <form method="POST" action="{{ route('employee-deductions.store') }}">
            @csrf
            <div class="grid">
                <div class="field"><label>الموظف</label><select name="employee_id" required><option value="">اختر الموظف</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected(old('employee_id')==$employee->id)>{{ $employee->display_name }} - {{ $employee->employee_number }}</option>@endforeach</select>@error('employee_id')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>نوع الاستقطاع</label><input name="deduction_type" value="{{ old('deduction_type') }}" placeholder="مثال: خصم تأخير" required>@error('deduction_type')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>المبلغ</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required>@error('amount')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>طريقة الخصم</label><select name="deduction_mode" required><option value="one_time">مرة واحدة</option><option value="monthly">شهري ثابت</option><option value="installments">أقساط</option><option value="percentage">نسبة</option></select></div>
                <div class="field"><label>عدد الأقساط</label><input type="number" name="installments_count" value="{{ old('installments_count') }}"></div>
                <div class="field"><label>قيمة القسط الشهري</label><input type="number" step="0.01" name="monthly_amount" value="{{ old('monthly_amount') }}"></div>
                <div class="field"><label>تاريخ البداية</label><input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>تاريخ النهاية</label><input type="date" name="end_date" value="{{ old('end_date') }}"></div>
                <div class="field full"><label>السبب</label><textarea name="reason">{{ old('reason') }}</textarea></div>
                <div class="field full"><label>ملاحظات</label><textarea name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div style="margin-top:18px;display:flex;gap:10px">
                <button class="btn2 primary">حفظ الاستقطاع</button>
                <a class="btn2 soft" href="{{ route('employee-deductions.index') }}">رجوع</a>
            </div>
        </form>
    </div>
@endsection
