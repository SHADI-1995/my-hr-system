@extends('layouts.hr')

@section('title', 'تعديل وظيفة')
@section('page-title', 'تعديل وظيفة')

@section('content')

    <div class="card">

        <form action="{{ route('positions.update', $position->id) }}" method="POST">

            @csrf
            @method('PUT')

            @if(auth()->user()->hasPermission('positions.edit.department_id'))
                <div style="margin-bottom:15px;">
                    <label>القسم</label>

                    <select name="department_id" style="width:100%;padding:10px;margin-top:5px;">
                        @foreach($departments as $department)
                            <option
                                value="{{ $department->id }}"
                                {{ old('department_id', $position->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>القسم</label>
                    <input
                        type="text"
                        value="{{ $position->department->name ?? '-' }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.edit.title'))
                <div style="margin-bottom:15px;">
                    <label>المسمى الوظيفي</label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $position->title) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>المسمى الوظيفي</label>

                    <input
                        type="text"
                        value="{{ $position->title }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.edit.code'))
                <div style="margin-bottom:15px;">
                    <label>كود الوظيفة</label>

                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $position->code) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>كود الوظيفة</label>

                    <input
                        type="text"
                        value="{{ $position->code }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.edit.min_salary'))
                <div style="margin-bottom:15px;">
                    <label>الحد الأدنى</label>

                    <input
                        type="number"
                        name="min_salary"
                        value="{{ old('min_salary', $position->min_salary) }}"
                        min="0"
                        step="0.01"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الحد الأدنى</label>

                    <input
                        type="text"
                        value="{{ $position->min_salary }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.edit.max_salary'))
                <div style="margin-bottom:15px;">
                    <label>الحد الأعلى</label>

                    <input
                        type="number"
                        name="max_salary"
                        value="{{ old('max_salary', $position->max_salary) }}"
                        min="0"
                        step="0.01"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الحد الأعلى</label>

                    <input
                        type="text"
                        value="{{ $position->max_salary }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.edit.is_active'))
                <div style="margin-bottom:20px;">
                    <label>
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                        وظيفة نشطة
                    </label>
                </div>
            @else
                <div style="margin-bottom:20px;">
                    <label>الحالة</label>

                    <div style="margin-top:5px;">
                        @if($position->is_active)
                            <span class="badge badge-active">نشطة</span>
                        @else
                            <span class="badge badge-inactive">غير نشطة</span>
                        @endif
                    </div>
                </div>
            @endif

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث
            </button>

            <a href="{{ route('positions.index') }}" class="btn">
                رجوع
            </a>

        </form>

    </div>

@endsection
