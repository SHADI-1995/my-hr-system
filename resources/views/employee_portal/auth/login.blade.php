@extends('layouts.employee_portal')

@section('title', 'دخول الموظف')

@section('content')
    <div class="portal-topbar">
        <div class="portal-title">
            <h2>دخول الموظف</h2>
            <p>ادخل برقم الإقامة وكلمة المرور</p>
        </div>
        <a href="{{ route('employee-portal.register') }}" class="portal-btn secondary">تسجيل جديد</a>
    </div>

    <div class="portal-card">
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('employee-portal.login.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group full">
                    <label>رقم الإقامة</label>
                    <input type="text" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="أدخل رقم الإقامة">
                    @error('iqama_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group full">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="******">
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <button type="submit" class="portal-btn">دخول</button>

                <a href="{{ route('employee-portal.forgot-password') }}"
                   style="font-weight:900; color:#6d5bd0; text-decoration:none;">
                    نسيت كلمة المرور؟
                </a>
            </div>
        </form>
    </div>
@endsection
