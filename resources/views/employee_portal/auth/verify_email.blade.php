@extends('layouts.employee_portal')

@section('title', 'تحقق البريد الإلكتروني')

@section('content')
    <div class="portal-topbar">
        <div class="portal-title">
            <h2>تحقق البريد الإلكتروني</h2>
            <p>أدخل رمز التحقق المرسل إلى بريدك الإلكتروني</p>
        </div>
        <a href="{{ route('employee-portal.login') }}" class="portal-btn secondary">رجوع للدخول</a>
    </div>

    <div class="portal-card">
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form method="POST" action="{{ route('employee-portal.verify-email.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group full">
                    <label>رمز التحقق</label>
                    <input type="text" name="code" maxlength="6" placeholder="000000" style="letter-spacing:5px; text-align:center; font-size:22px; font-weight:900;">
                    @error('code') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" class="portal-btn">تحقق ودخول</button>
            </div>
        </form>

        <form method="POST" action="{{ route('employee-portal.verify-email.resend') }}" style="margin-top:12px;">
            @csrf
            <button type="submit" class="portal-btn secondary">إعادة إرسال الرمز</button>
        </form>
    </div>
@endsection

