@extends('layouts.employee_portal')

@section('title', 'تسجيل موظف')

@section('content')
    <style>
        .register-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(76, 59, 145, .08);
        }

        .portal-alert {
            border-radius: 18px;
            padding: 15px 16px;
            margin-bottom: 16px;
            font-weight: 900;
            line-height: 1.9;
        }

        .portal-alert.success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .portal-alert.info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .portal-alert.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .email-choice-box {
            background: #f8f7ff;
            border: 1px solid #e7e0ff;
            border-radius: 20px;
            padding: 14px;
            margin-top: 8px;
        }

        .email-choice-title {
            color: #4c3b91;
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .email-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .email-option {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #fff;
            cursor: pointer;
            transition: .18s ease;
            min-height: 58px;
        }

        .email-option:hover {
            border-color: #c4b5fd;
            box-shadow: 0 10px 22px rgba(76, 59, 145, .08);
            transform: translateY(-1px);
        }

        .email-option input {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px;
            accent-color: #6d5bd0;
            margin: 0;
        }

        .email-option-text {
            display: grid;
            gap: 3px;
            min-width: 0;
        }

        .email-option-text strong {
            color: #111827;
            font-size: 13px;
            font-weight: 900;
        }

        .email-option-text span {
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .email-option:has(input:checked) {
            border-color: #6d5bd0;
            background: #f5f3ff;
            box-shadow: 0 0 0 4px rgba(109, 91, 208, .10);
        }

        .current-email-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #eef2ff;
            color: #4338ca;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 13px;
            font-weight: 900;
            margin-top: 8px;
            overflow-wrap: anywhere;
        }

        .new-email-field.is-hidden {
            display: none;
        }

        .form-grid.clean {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-help {
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
            margin-top: 7px;
            line-height: 1.7;
        }

        @media (max-width: 720px) {
            .register-card {
                padding: 16px;
                border-radius: 20px;
            }

            .form-grid.clean {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .email-options {
                grid-template-columns: 1fr;
            }

            .email-option {
                min-height: 54px;
                padding: 12px;
            }

            .portal-topbar {
                gap: 12px;
            }

            .portal-title h2 {
                font-size: 22px;
            }

            .portal-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="portal-topbar">
        <div class="portal-title">
            <h2>تسجيل موظف جديد</h2>
            <p>سجل برقم الإقامة، ثم اختر البريد الذي سيصل إليه رمز التحقق.</p>
        </div>

        <a href="{{ route('employee-portal.login') }}" class="portal-btn secondary">لدي حساب</a>
    </div>

    <div class="register-card">
        @if(session('error'))
            <div class="portal-alert error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="portal-alert success">{{ session('success') }}</div>
        @endif

        @if(session('info'))
            <div class="portal-alert success">{{ session('info') }}</div>
        @endif

        @if(session('existing_employee_email'))
            <div class="portal-alert info">
                يوجد بريد إلكتروني محفوظ لهذا الموظف:
                <span class="current-email-chip">
                    <i class="fas fa-envelope"></i>
                    {{ session('existing_employee_email') }}
                </span>
                <br>
                اختر هل تريد إرسال رمز التحقق إلى البريد الحالي أو استبداله ببريد جديد.
            </div>
        @endif

        <form method="POST" action="{{ route('employee-portal.register.store') }}" id="employeeRegisterForm">
            @csrf

            @if(session('existing_employee_email'))
                <input type="hidden" name="confirm_email_choice" value="1">
            @endif

            <div class="form-grid clean">
                <div class="form-group full">
                    <label>رقم الإقامة</label>
                    <input type="text" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="أدخل رقم الإقامة">
                    @error('iqama_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                @if(session('existing_employee_email'))
                    <div class="form-group full">
                        <label>اختيار البريد الإلكتروني لإرسال رمز التحقق</label>

                        <div class="email-choice-box">
                            <div class="email-choice-title">أين تريد إرسال الرمز؟</div>

                            <div class="email-options">
                                <label class="email-option">
                                    <input
                                        type="radio"
                                        name="email_action"
                                        value="use_existing"
                                        {{ old('email_action', 'use_existing') === 'use_existing' ? 'checked' : '' }}
                                        onchange="toggleEmailField()"
                                    >

                                    <span class="email-option-text">
                                        <strong>استخدام البريد الحالي</strong>
                                        <span>{{ session('existing_employee_email') }}</span>
                                    </span>
                                </label>

                                <label class="email-option">
                                    <input
                                        type="radio"
                                        name="email_action"
                                        value="replace"
                                        {{ old('email_action') === 'replace' ? 'checked' : '' }}
                                        onchange="toggleEmailField()"
                                    >

                                    <span class="email-option-text">
                                        <strong>استبدال البريد</strong>
                                        <span>إدخال بريد جديد واستلام الرمز عليه</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- لا نرسل email_action في أول محاولة حتى يتحقق Controller هل يوجد بريد محفوظ أم لا --}}
                @endif

                <div class="form-group full new-email-field" id="newEmailField">
                    <label>البريد الإلكتروني الجديد</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com">
                    <div class="form-help">
                        @if(session('existing_employee_email'))
                            يظهر هذا الحقل فقط عند اختيار استبدال البريد. إذا اخترت استخدام البريد الحالي يمكنك تركه فارغًا.
                        @else
                            سيتم حفظ البريد في ملف الموظف وإرسال رمز التحقق إليه.
                        @endif
                    </div>
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="******">
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" placeholder="******">
                </div>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <button type="submit" class="portal-btn">
                    <i class="fas fa-paper-plane"></i>
                    إنشاء الحساب وإرسال رمز التحقق
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleEmailField() {
            const selected = document.querySelector('input[name="email_action"]:checked');
            const field = document.getElementById('newEmailField');

            if (!field || !selected) {
                return;
            }

            if (selected.value === 'replace') {
                field.classList.remove('is-hidden');
            } else {
                field.classList.add('is-hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', toggleEmailField);
    </script>
@endsection
