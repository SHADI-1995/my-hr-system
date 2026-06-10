@extends('layouts.hr')

@section('title', 'إضافة سياسة إجازات')
@section('page-title', 'إضافة سياسة إجازات')

@section('content')

    <style>
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; }
        .form-group { display:flex; flex-direction:column; }
        .form-group.full { grid-column:1 / -1; }
        .form-group label { font-weight:bold; margin-bottom:7px; color:#333; }
        .form-group input, .form-group select { width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:10px; outline:none; background:#fff; }
        .toggle-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-top:18px; }
        .toggle-box { background:#f9fafb; border:1px solid #eee; border-radius:12px; padding:13px; display:flex; align-items:center; gap:10px; }
        .toggle-box input { width:auto; }
        .form-actions { display:flex; gap:12px; margin-top:25px; }
        .required { color:#dc2626; }
        .hint { color:#6b7280; font-size:12px; margin-top:6px; }
        @media (max-width:750px) { .form-grid, .toggle-grid { grid-template-columns:1fr; } }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-plus"></i></div>
            <div>
                <h1>إضافة سياسة إجازات</h1>
                <p>تحديد إعدادات احتساب الإجازات السنوية</p>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('leave-policies.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card">
        @if ($errors->any())
            <div style="background:#fef2f2; color:#991b1b; padding:15px; border-radius:12px; margin-bottom:20px;">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('leave-policies.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group full">
                    <label>اسم السياسة <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', 'سياسة الإجازات الافتراضية') }}">
                </div>

                <div class="form-group">
                    <label>أيام الإجازة قبل 5 سنوات <span class="required">*</span></label>
                    <input type="number" name="annual_days_before_5_years" value="{{ old('annual_days_before_5_years', 21) }}" min="0">
                    <div class="hint">النظام السعودي: 21 يوم قبل إكمال 5 سنوات.</div>
                </div>

                <div class="form-group">
                    <label>أيام الإجازة بعد 5 سنوات <span class="required">*</span></label>
                    <input type="number" name="annual_days_after_5_years" value="{{ old('annual_days_after_5_years', 30) }}" min="0">
                    <div class="hint">النظام السعودي: 30 يوم بعد إكمال 5 سنوات.</div>
                </div>

                <div class="form-group">
                    <label>بعد كم سنة تزيد الإجازة؟ <span class="required">*</span></label>
                    <input type="number" name="after_years" value="{{ old('after_years', 5) }}" min="1">
                </div>

                <div class="form-group">
                    <label>طريقة احتساب سنة الإجازة <span class="required">*</span></label>
                    <select name="leave_year_type">
                        <option value="hire_date" {{ old('leave_year_type', 'hire_date') == 'hire_date' ? 'selected' : '' }}>حسب تاريخ مباشرة الموظف</option>
                        <option value="gregorian" {{ old('leave_year_type') == 'gregorian' ? 'selected' : '' }}>حسب السنة الميلادية</option>
                        <option value="hijri" {{ old('leave_year_type') == 'hijri' ? 'selected' : '' }}>حسب السنة الهجرية</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>أقصى رصيد مرحل</label>
                    <input type="number" name="max_carry_forward_days" value="{{ old('max_carry_forward_days', 30) }}" min="0">
                </div>
            </div>

            <div class="toggle-grid">
                <label class="toggle-box"><input type="checkbox" name="carry_forward_enabled" value="1" {{ old('carry_forward_enabled', true) ? 'checked' : '' }}><span>تفعيل ترحيل الرصيد</span></label>
                <label class="toggle-box"><input type="checkbox" name="exclude_weekends" value="1" {{ old('exclude_weekends', true) ? 'checked' : '' }}><span>استبعاد عطلة نهاية الأسبوع</span></label>
                <label class="toggle-box"><input type="checkbox" name="exclude_official_holidays" value="1" {{ old('exclude_official_holidays', true) ? 'checked' : '' }}><span>استبعاد الإجازات الرسمية</span></label>
                <label class="toggle-box"><input type="checkbox" name="inactive_employee_accrual" value="1" {{ old('inactive_employee_accrual') ? 'checked' : '' }}><span>احتساب رصيد للموظف غير النشط</span></label>
                <label class="toggle-box"><input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}><span>تفعيل هذه السياسة</span></label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><i class="fas fa-save"></i> حفظ السياسة</button>
                <a href="{{ route('leave-policies.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

