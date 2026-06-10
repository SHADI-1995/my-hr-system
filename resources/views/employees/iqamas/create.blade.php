@extends('layouts.hr')

@section('title', 'إضافة إقامة')
@section('page-title', 'إضافة إقامة')

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

        .form-group.full {
            grid-column: 1 / -1;
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
            font-size: 14px;
            background: #fff;
        }

        .form-group textarea {
            min-height: 90px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .required {
            color: #dc2626;
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-id-card"></i>
            </div>

            <div>
                <h1>إضافة إقامة</h1>
                <p>إضافة إقامة للموظف: {{ $employee->display_name }}</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('employees.show', $employee->id) }}" class="hero-btn white">
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

        <form action="{{ route('employees.iqamas.store', $employee->id) }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>رقم الإقامة <span class="required">*</span></label>
                    <input type="text" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="مثال: 245xxxxxxxx">
                </div>

                <div class="form-group">
                    <label>اسم الكفيل</label>
                    <input type="text" name="sponsor_name" value="{{ old('sponsor_name') }}" placeholder="اسم الكفيل">
                </div>

                <div class="form-group">
                    <label>تاريخ الإصدار</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date') }}">
                </div>

                <div class="form-group">
                    <label>تاريخ الانتهاء <span class="required">*</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                </div>

                <div class="form-group full">
                    <label>ملاحظات</label>
                    <textarea name="notes" placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    حفظ الإقامة
                </button>

                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-danger">
                    إلغاء
                </a>
            </div>

        </form>

    </div>

@endsection
