@extends('layouts.employee_portal')

@section('title', 'نسيت كلمة المرور')

@section('content')
    <div class="portal-topbar">
        <div class="portal-title">
            <h2>نسيت كلمة المرور</h2>
            <p>أدخل رقم الإقامة والبريد الإلكتروني لإرسال رمز إعادة التعيين</p>
        </div>
        <a href="{{ route('employee-portal.login') }}" class="portal-btn secondary">رجوع للدخول</a>
    </div>

    <div class="portal-card">
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('employee-portal.forgot-password.send') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group full">
                    <label>رقم الإقامة</label>
                    <input type="text" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="أدخل رقم الإقامة">
                    @error('iqama_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group full">
                    <label>البريد الإلكتروني المسجل</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com">
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="margin-top:18px;">
                <button type="submit" class="portal-btn">إرسال رمز إعادة التعيين</button>
            </div>
        </form>
    </div>
@endsection

