@extends('layouts.hr')

@section('title', 'إضافة نوع إجازة')
@section('page-title', 'إضافة نوع إجازة')

@section('content')

    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 7px;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            background: #fff;
        }

        .form-group textarea {
            min-height: 95px;
            resize: vertical;
        }

        .toggle-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-top: 18px;
        }

        .toggle-box {
            background: #f9fafb;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-box input {
            width: auto;
        }

        .payroll-section {
            margin-top: 22px;
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #ddd6fe;
            background: #f7f3ff;
        }

        .payroll-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4c3b91;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .payroll-section-title i {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #ede9fe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .required {
            color: #dc2626;
        }

        .hint {
            color: #6b7280;
            font-size: 12px;
            margin-top: 6px;
            line-height: 1.7;
        }

        @media (max-width: 750px) {
            .form-grid,
            .toggle-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-plus"></i>
            </div>

            <div>
                <h1>إضافة نوع إجازة</h1>
                <p>تحديد قواعد نوع الإجازة وهل تخصم من الرصيد السنوي أو تؤثر على مسير الرواتب</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('leave-types.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card">
        @if ($errors->any())
            <div style="background:#fef2f2; color:#991b1b; padding:15px; border-radius:12px; margin-bottom:20px;">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('leave-types.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>اسم نوع الإجازة <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: إجازة سنوية">
                </div>

                <div class="form-group">
                    <label>الكود <span class="required">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="مثال: annual">
                    <div class="hint">يفضل أن يكون بالإنجليزي بدون مسافات مثل annual أو sick.</div>
                </div>

                <div class="form-group">
                    <label>الحد الأقصى بالأيام في السنة</label>
                    <input type="number" name="max_days_per_year" value="{{ old('max_days_per_year') }}" min="0" placeholder="اختياري">
                </div>
            </div>

            <div class="toggle-grid">
                <label class="toggle-box">
                    <input type="checkbox" name="is_paid" value="1" {{ old('is_paid', true) ? 'checked' : '' }}>
                    <span>الإجازة مدفوعة</span>
                </label>

                <label class="toggle-box">
                    <input type="checkbox" name="deduct_from_annual_balance" value="1" {{ old('deduct_from_annual_balance') ? 'checked' : '' }}>
                    <span>تخصم من الرصيد السنوي</span>
                </label>

                <label class="toggle-box">
                    <input type="checkbox" name="requires_attachment" value="1" {{ old('requires_attachment') ? 'checked' : '' }}>
                    <span>تحتاج مرفق</span>
                </label>

                <label class="toggle-box">
                    <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', true) ? 'checked' : '' }}>
                    <span>تحتاج موافقة</span>
                </label>

                <label class="toggle-box">
                    <input type="checkbox" name="auto_approved" value="1" {{ old('auto_approved') ? 'checked' : '' }}>
                    <span>تعتمد تلقائيًا</span>
                </label>

                <label class="toggle-box">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>مفعل</span>
                </label>
            </div>

            <div class="payroll-section">
                <div class="payroll-section-title">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>إعدادات تأثير الإجازة على مسير الرواتب</span>
                </div>

                <div class="toggle-grid">
                    <label class="toggle-box">
                        <input type="checkbox" name="affects_payroll" value="1" {{ old('affects_payroll') ? 'checked' : '' }}>
                        <span>تؤثر على مسير الرواتب</span>
                    </label>
                </div>

                <div class="form-grid" style="margin-top:18px;">
                    <div class="form-group">
                        <label>نسبة الراتب أثناء الإجازة <span class="required">*</span></label>
                        <input type="number"
                               name="salary_percentage"
                               value="{{ old('salary_percentage', 100) }}"
                               min="0"
                               max="100"
                               step="0.01"
                               placeholder="مثال: 100 أو 0 أو 50">
                        <div class="hint">
                            100 = الإجازة مدفوعة بالكامل<br>
                            0 = إجازة بدون راتب<br>
                            50 = إجازة نصف راتب
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ملاحظة سياسة الراتب</label>
                        <textarea name="payroll_policy_note" placeholder="مثال: تخصم هذه الإجازة من مسير الرواتب بنسبة 100% إذا كانت بدون راتب">{{ old('payroll_policy_note') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    حفظ نوع الإجازة
                </button>

                <a href="{{ route('leave-types.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
