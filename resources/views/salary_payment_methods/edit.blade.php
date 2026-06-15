@extends('layouts.hr')

@section('title', 'تعديل طريقة صرف راتب')
@section('page-title', 'تعديل طريقة صرف راتب')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-pen-to-square"></i>
            </div>
            <div>
                <h1>تعديل طريقة صرف راتب</h1>
                <p>التعديل هنا يعتمد على صلاحيات الحقول الممنوحة لك.</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('salary-payment-methods.index') }}" class="hero-btn white">
                رجوع
            </a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('salary-payment-methods.update', $salaryPaymentMethod) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                <div>
                    <label>الاسم العربي</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.name_ar'))
                        <input type="text" name="name_ar" value="{{ old('name_ar', $salaryPaymentMethod->name_ar) }}">
                    @else
                        <input type="text" value="{{ $salaryPaymentMethod->name_ar }}" disabled>
                    @endif
                </div>

                <div>
                    <label>الاسم الإنجليزي</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.name_en'))
                        <input type="text" name="name_en" value="{{ old('name_en', $salaryPaymentMethod->name_en) }}">
                    @else
                        <input type="text" value="{{ $salaryPaymentMethod->name_en ?? '-' }}" disabled>
                    @endif
                </div>

                <div>
                    <label>الكود</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.code'))
                        <input type="text" name="code" value="{{ old('code', $salaryPaymentMethod->code) }}">
                    @else
                        <input type="text" value="{{ $salaryPaymentMethod->code }}" disabled>
                    @endif
                </div>

                <div>
                    <label>الترتيب</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.sort_order'))
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $salaryPaymentMethod->sort_order) }}">
                    @else
                        <input type="text" value="{{ $salaryPaymentMethod->sort_order }}" disabled>
                    @endif
                </div>

                <div>
                    <label>الحالة</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.is_active'))
                        <select name="is_active">
                            <option value="1" @selected(old('is_active', $salaryPaymentMethod->is_active) == 1)>مفعل</option>
                            <option value="0" @selected(old('is_active', $salaryPaymentMethod->is_active) == 0)>غير مفعل</option>
                        </select>
                    @else
                        <input type="text" value="{{ $salaryPaymentMethod->is_active ? 'مفعل' : 'غير مفعل' }}" disabled>
                    @endif
                </div>

                <div style="grid-column:1/-1">
                    <label>ملاحظات</label>
                    @if(auth()->user()->hasPermission('salary_payment_methods.edit.notes'))
                        <textarea name="notes" rows="4">{{ old('notes', $salaryPaymentMethod->notes) }}</textarea>
                    @else
                        <textarea disabled rows="4">{{ $salaryPaymentMethod->notes }}</textarea>
                    @endif
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button class="btn">
                    <i class="fas fa-save"></i>
                    تحديث
                </button>
                <a href="{{ route('salary-payment-methods.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
