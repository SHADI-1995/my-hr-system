@extends('layouts.hr')

@section('title', 'إضافة مجموعة رواتب')
@section('page-title', 'إضافة مجموعة رواتب')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <h1>إضافة مجموعة رواتب</h1>
                <p>إدارة البيانات المستخدمة في الموظفين ومسير الرواتب.</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('payroll-groups.index') }}" class="hero-btn white">رجوع</a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('payroll-groups.store') }}">
            @csrf


            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                <div>
                    <label>الاسم العربي</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', '') }}">
                </div>
                <div>
                    <label>الاسم الإنجليزي</label>
                    <input type="text" name="name_en" value="{{ old('name_en', '') }}">
                </div>
                <div>
                    <label>الكود</label>
                    <input type="text" name="code" value="{{ old('code', '') }}">
                </div>
                <div>
                    <label>الترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}">
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
                <button class="btn"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('payroll-groups.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

