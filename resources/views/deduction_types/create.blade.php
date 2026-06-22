@extends('layouts.hr')

@section('title', 'إضافة نوع استقطاع')
@section('page-title', 'إضافة نوع استقطاع')

@section('content')
    <style>.card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:22px;box-shadow:0 16px 40px rgba(76,59,145,.07)}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.full{grid-column:1/-1}.field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:7px}.field input,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:12px;font-weight:800}.field textarea{min-height:100px}.btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;gap:7px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}@media(max-width:800px){.grid{grid-template-columns:1fr}}</style>
    <div class="card">
        <form method="POST" action="{{ route('deduction-types.store') }}">@csrf
            <div class="grid">
                <div class="field"><label>الاسم العربي</label><input name="name_ar" value="{{ old('name_ar') }}" required placeholder="مثال: خصم تأخير">@error('name_ar')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>الكود</label><input name="code" value="{{ old('code') }}" required placeholder="مثال: late">@error('code')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>الاسم الإنجليزي</label><input name="name_en" value="{{ old('name_en') }}"></div>
                <div class="field"><label>الترتيب</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"></div>
                <div class="field full"><label>الوصف</label><textarea name="description">{{ old('description') }}</textarea></div>
                <div class="field full"><label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="is_active" value="1" checked style="width:auto">نشط</label></div>
            </div>
            <div style="margin-top:18px;display:flex;gap:10px"><button class="btn2 primary" type="submit">حفظ</button><a class="btn2 soft" href="{{ route('deduction-types.index') }}">رجوع</a></div>
        </form>
    </div>
@endsection
