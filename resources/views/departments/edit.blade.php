@extends('layouts.hr')

@section('title', 'تعديل قسم')
@section('page-title', 'تعديل القسم')

@section('content')

    <div class="card">

        <form action="{{ route('departments.update', $department->id) }}" method="POST">

            @csrf
            @method('PUT')

            @if(auth()->user()->hasPermission('departments.edit.name'))
                <div style="margin-bottom:15px;">
                    <label>اسم القسم</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $department->name) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>اسم القسم</label>
                    <input
                        type="text"
                        value="{{ $department->name }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('departments.edit.code'))
                <div style="margin-bottom:15px;">
                    <label>كود القسم</label>
                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $department->code) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>كود القسم</label>
                    <input
                        type="text"
                        value="{{ $department->code }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            <div style="margin-bottom:15px;">
                <label>الوصف</label>
                <textarea
                    name="description"
                    rows="4"
                    style="width:100%;padding:10px;margin-top:5px;">{{ old('description', $department->description) }}</textarea>
            </div>

            @if(auth()->user()->hasPermission('departments.edit.is_active'))
                <div style="margin-bottom:20px;">
                    <label>
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                        قسم نشط
                    </label>
                </div>
            @else
                <div style="margin-bottom:20px;">
                    <label>الحالة</label>
                    <div style="margin-top:5px;">
                        @if($department->is_active)
                            <span class="badge badge-active">نشط</span>
                        @else
                            <span class="badge badge-inactive">غير نشط</span>
                        @endif
                    </div>
                </div>
            @endif

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث القسم
            </button>

            <a href="{{ route('departments.index') }}" class="btn">
                رجوع
            </a>

        </form>

    </div>

@endsection
