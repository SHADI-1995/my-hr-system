@extends('layouts.employee_portal')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
    <div class="portal-topbar">
        <div class="portal-title">
            <h2>إعادة تعيين كلمة المرور</h2>
            <p>أدخل الرمز المرسل إلى بريدك الإلكتروني ثم أنشئ كلمة مرور جديدة</p>
        </div>
        <a href="{{ route('employee-portal.login') }}" class="portal-btn secondary">رجوع للدخول</a>
    </div>

    <div class="portal-card">
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('employee-portal.reset-password.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group full">
                    <label>رمز التحقق</label>
                    <input type="text" name="code" maxlength="6" placeholder="000000" style="letter-spacing:5px; text-align:center; font-size:22px; font-weight:900;">
                    @error('code') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>كلمة المرور الجديدة</label>
                    <input type="password" name="password" placeholder="******">
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="password_confirmation" placeholder="******">
                </div>
            </div>

            <div style="margin-top:18px;">
                <button type="submit" class="portal-btn">حفظ كلمة المرور الجديدة</button>
            </div>
        </form>
    </div>
@endsection

