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

        .form-group input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            background: #fff;
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
                <p>تحديد قواعد نوع الإجازة وهل تخصم من الرصيد السنوي أم لا</p>
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

