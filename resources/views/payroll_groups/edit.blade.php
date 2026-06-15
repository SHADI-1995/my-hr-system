@extends('layouts.hr')

@section('title', 'تعديل مجموعة رواتب')
@section('page-title', 'تعديل مجموعة رواتب')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <h1>تعديل مجموعة رواتب</h1>
                <p>إدارة البيانات المستخدمة في الموظفين ومسير الرواتب.</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('payroll-groups.index') }}" class="hero-btn white">رجوع</a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('payroll-groups.update', $payrollGroup) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                <div>
                    <label>الاسم العربي</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.name_ar'))
                        <input type="text" name="name_ar" value="{{ old('name_ar', $payrollGroup->name_ar) }}">
                    @else
                        <input type="text" value="{{ $payrollGroup->name_ar ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الاسم الإنجليزي</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.name_en'))
                        <input type="text" name="name_en" value="{{ old('name_en', $payrollGroup->name_en) }}">
                    @else
                        <input type="text" value="{{ $payrollGroup->name_en ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الكود</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.code'))
                        <input type="text" name="code" value="{{ old('code', $payrollGroup->code) }}">
                    @else
                        <input type="text" value="{{ $payrollGroup->code ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الترتيب</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.sort_order'))
                        <input type="number" name="sort_order" value="{{ old('sort_order', $payrollGroup->sort_order) }}">
                    @else
                        <input type="text" value="{{ $payrollGroup->sort_order }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الحالة</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.is_active'))
                        <select name="is_active">
                            <option value="1" @selected(old('is_active', $payrollGroup->is_active) == 1)>مفعل</option>
                            <option value="0" @selected(old('is_active', $payrollGroup->is_active) == 0)>غير مفعل</option>
                        </select>
                    @else
                        <input type="text" value="{{ $payrollGroup->is_active ? 'مفعل' : 'غير مفعل' }}" disabled>
                    @endif
                </div>

                <div style="grid-column:1/-1">
                    <label>ملاحظات</label>
                    @if(auth()->user()->hasPermission('payroll_groups.edit.notes'))
                        <textarea name="notes" rows="4">{{ old('notes', $payrollGroup->notes) }}</textarea>
                    @else
                        <textarea rows="4" disabled>{{ $payrollGroup->notes }}</textarea>
                    @endif
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button class="btn"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('payroll-groups.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
