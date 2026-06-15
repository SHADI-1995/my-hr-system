@extends('layouts.hr')

@section('title', 'تعديل مركز تكلفة')
@section('page-title', 'تعديل مركز تكلفة')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <h1>تعديل مركز تكلفة</h1>
                <p>إدارة البيانات المستخدمة في الموظفين ومسير الرواتب.</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('cost-centers.index') }}" class="hero-btn white">رجوع</a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('cost-centers.update', $costCenter) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                <div>
                    <label>الاسم العربي</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.name_ar'))
                        <input type="text" name="name_ar" value="{{ old('name_ar', $costCenter->name_ar) }}">
                    @else
                        <input type="text" value="{{ $costCenter->name_ar ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الاسم الإنجليزي</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.name_en'))
                        <input type="text" name="name_en" value="{{ old('name_en', $costCenter->name_en) }}">
                    @else
                        <input type="text" value="{{ $costCenter->name_en ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الكود</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.code'))
                        <input type="text" name="code" value="{{ old('code', $costCenter->code) }}">
                    @else
                        <input type="text" value="{{ $costCenter->code ?? '-' }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الترتيب</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.sort_order'))
                        <input type="number" name="sort_order" value="{{ old('sort_order', $costCenter->sort_order) }}">
                    @else
                        <input type="text" value="{{ $costCenter->sort_order }}" disabled>
                    @endif
                </div>
                <div>
                    <label>الحالة</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.is_active'))
                        <select name="is_active">
                            <option value="1" @selected(old('is_active', $costCenter->is_active) == 1)>مفعل</option>
                            <option value="0" @selected(old('is_active', $costCenter->is_active) == 0)>غير مفعل</option>
                        </select>
                    @else
                        <input type="text" value="{{ $costCenter->is_active ? 'مفعل' : 'غير مفعل' }}" disabled>
                    @endif
                </div>

                <div style="grid-column:1/-1">
                    <label>ملاحظات</label>
                    @if(auth()->user()->hasPermission('cost_centers.edit.notes'))
                        <textarea name="notes" rows="4">{{ old('notes', $costCenter->notes) }}</textarea>
                    @else
                        <textarea rows="4" disabled>{{ $costCenter->notes }}</textarea>
                    @endif
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button class="btn"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('cost-centers.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

