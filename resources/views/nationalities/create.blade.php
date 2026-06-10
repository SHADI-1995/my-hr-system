@extends('layouts.hr')

@section('title', 'إضافة جنسية')
@section('page-title', 'إضافة جنسية')

@section('content')

    <style>
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; }
        .form-group { display:flex; flex-direction:column; }
        .form-group label { font-weight:bold; margin-bottom:7px; color:#333; }
        .form-group input, .form-group select {
            width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:10px; outline:none; background:#fff;
        }
        .required { color:#dc2626; }
        .form-actions { display:flex; gap:12px; margin-top:25px; }
        @media (max-width:650px) { .form-grid { grid-template-columns:1fr; } }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-plus"></i></div>
            <div>
                <h1>إضافة جنسية</h1>
                <p>إضافة جنسية جديدة لاستخدامها في ملف الموظف</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('nationalities.index') }}" class="hero-btn white">
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

        <form action="{{ route('nationalities.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>اسم الجنسية بالعربي <span class="required">*</span></label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" placeholder="مثال: اليمن">
                </div>

                <div class="form-group">
                    <label>اسم الجنسية بالإنجليزي</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="Example: Yemen">
                </div>

                <div class="form-group">
                    <label>الرمز</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="مثال: YE">
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="is_active">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>مفعلة</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>معطلة</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    حفظ الجنسية
                </button>

                <a href="{{ route('nationalities.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
