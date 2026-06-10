@extends('layouts.hr')

@section('title', 'تعديل إجازة رسمية')
@section('page-title', 'تعديل إجازة رسمية')

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
            color: #4c3b91;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            outline: none;
            background: #fff;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
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
            flex-wrap: wrap;
        }

        .required {
            color: #dc2626;
        }

        @media (max-width: 750px) {
            .form-grid {
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
                <h1>تعديل إجازة رسمية</h1>
                <p>تعديل بيانات العطلة الرسمية</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('official-holidays.index') }}" class="hero-btn white">
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

        <form action="{{ route('official-holidays.update', $officialHoliday->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label>اسم الإجازة <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $officialHoliday->name) }}" placeholder="مثال: عيد الفطر">
                </div>

                <div class="form-group">
                    <label>السنة</label>
                    <input type="text" name="year_label" value="{{ old('year_label', $officialHoliday->year_label) }}" placeholder="مثال: 2026">
                </div>

                <div class="form-group">
                    <label>من تاريخ <span class="required">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($officialHoliday->start_date)->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label>إلى تاريخ <span class="required">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($officialHoliday->end_date)->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label>نوع الإجازة</label>
                    <select name="type">
                        <option value="">عام</option>
                        <option value="eid" {{ old('type', $officialHoliday->type) == 'eid' ? 'selected' : '' }}>عيد</option>
                        <option value="national" {{ old('type', $officialHoliday->type) == 'national' ? 'selected' : '' }}>وطني</option>
                        <option value="company" {{ old('type', $officialHoliday->type) == 'company' ? 'selected' : '' }}>خاص بالشركة</option>
                        <option value="other" {{ old('type', $officialHoliday->type) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <label class="toggle-box">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $officialHoliday->is_active) ? 'checked' : '' }}>
                    <span>مفعلة</span>
                </label>

                <div class="form-group full">
                    <label>ملاحظات</label>
                    <textarea name="notes" placeholder="ملاحظات اختيارية">{{ old('notes', $officialHoliday->notes) }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    تحديث
                </button>

                <a href="{{ route('official-holidays.index') }}" class="btn btn-danger">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

