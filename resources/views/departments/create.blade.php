@extends('layouts.hr')

@section('title', 'إضافة قسم')
@section('page-title', 'إضافة قسم جديد')

@section('content')
    @if ($errors->any())
        <div class="card" style="margin-bottom:20px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
            <strong>يوجد أخطاء:</strong>

            <ul style="margin-top:10px; margin-bottom:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="card" style="margin-bottom:20px; background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="card" style="margin-bottom:20px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">

        <form action="{{ route('departments.store') }}" method="POST">

            @csrf

            <div style="margin-bottom:15px;">
                <label>اسم القسم</label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    style="width:100%;padding:10px;margin-top:5px;"
                >
            </div>

            <div style="margin-bottom:15px;">
                <label>كود القسم</label>

                <input
                    type="text"
                    name="code"
                    value="{{ old('code') }}"
                    required
                    style="width:100%;padding:10px;margin-top:5px;"
                >
            </div>

            <div style="margin-bottom:15px;">
                <label>الوصف</label>

                <textarea
                    name="description"
                    rows="4"
                    style="width:100%;padding:10px;margin-top:5px;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label>
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', 1) ? 'checked' : '' }}>
                    قسم نشط
                </label>
            </div>

            <button
                type="submit"
                class="btn"
            >
                حفظ القسم
            </button>

        </form>

    </div>

@endsection
