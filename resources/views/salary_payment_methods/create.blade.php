@extends('layouts.hr')

@section('title', 'إضافة طريقة صرف راتب')
@section('page-title', 'إضافة طريقة صرف راتب')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-money-check-dollar"></i>
            </div>
            <div>
                <h1>إضافة طريقة صرف راتب</h1>
                <p>أضف طريقة صرف جديدة تظهر في صفحة الموظفين.</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('salary-payment-methods.index') }}" class="hero-btn white">
                رجوع
            </a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('salary-payment-methods.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                <div>
                    <label>الاسم العربي *</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" placeholder="مثال: تحويل بنكي">
                </div>

                <div>
                    <label>الاسم الإنجليزي</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="Bank Transfer">
                </div>

                <div>
                    <label>الكود *</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="bank_transfer">
                </div>

                <div>
                    <label>الترتيب</label>
                    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}">
                </div>

                <div>
                    <label>الحالة</label>
                    <select name="is_active">
                        <option value="1" @selected(old('is_active', 1) == 1)>مفعل</option>
                        <option value="0" @selected(old('is_active') === '0')>غير مفعل</option>
                    </select>
                </div>

                <div style="grid-column:1/-1">
                    <label>ملاحظات</label>
                    <textarea name="notes" rows="4">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button class="btn">
                    <i class="fas fa-save"></i>
                    حفظ
                </button>
                <a href="{{ route('salary-payment-methods.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

