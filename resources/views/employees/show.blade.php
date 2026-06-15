@extends('layouts.hr')

@section('title', 'تفاصيل الموظف')
@section('page-title', 'تفاصيل الموظف')

@section('content')

    <style>
        .employee-profile-header {
            background: linear-gradient(135deg, #4c3b91, #6d5dfc);
            color: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .employee-main-info {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .employee-avatar {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .employee-main-info h2 {
            margin: 0 0 8px 0;
            font-size: 26px;
        }

        .employee-main-info p {
            margin: 0;
            opacity: 0.9;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #4c3b91;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .info-box {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 15px;
        }

        .info-box label {
            display: block;
            color: #666;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .info-box strong {
            color: #222;
            font-size: 15px;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .document-card {
            border: 1px solid #eee;
            border-radius: 14px;
            padding: 15px;
            background: #fff;
        }

        .document-card h4 {
            margin: 0 0 12px 0;
            color: #4c3b91;
        }

        .document-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px dashed #eee;
            padding: 8px 0;
            font-size: 14px;
        }

        .document-row:last-child {
            border-bottom: none;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-danger-soft {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-muted {
            background: #f3f4f6;
            color: #6b7280;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            display: inline-block;
        }

        .document-photo-box {
            margin-top: 14px;
            border-top: 1px dashed #eee;
            padding-top: 14px;
        }

        .document-photo-title {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .document-photo-preview {
            background: #f9fafb;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
        }

        .document-photo-preview img {
            width: 100%;
            max-height: 160px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
        }

        .document-file-icon {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 42px;
            margin-bottom: 10px;
        }

        .document-photo-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .document-photo-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #f2edff;
            color: #4c3b91;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .document-photo-empty {
            background: #f9fafb;
            color: #6b7280;
            border: 1px dashed #ddd;
            border-radius: 12px;
            padding: 12px;
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 1000px) {
            .info-grid,
            .documents-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .employee-profile-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 650px) {
            .info-grid,
            .documents-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        function documentStatusBadge($document) {
            if (!$document) {
                return '<span class="badge-muted">غير مضاف</span>';
            }

            if ($document->status === 'expired') {
                return '<span class="badge-danger-soft">منتهي</span>';
            }

            if ($document->status === 'near_expiry') {
                return '<span class="badge-warning">قريب الانتهاء</span>';
            }

            return '<span class="badge badge-active">ساري</span>';
        }

        function employeeStatusText($status) {
            return match($status) {
                'active' => 'على رأس العمل',
                'inactive' => 'غير نشط',
                'terminated' => 'منتهي الخدمة',
                default => $status,
            };
        }

        function salaryPaymentMethodText($method) {
            return match($method) {
                'bank_transfer' => 'تحويل بنكي',
                'cash' => 'نقدي',
                'cheque' => 'شيك',
                default => $method ?: '-',
            };
        }

        function payrollStatusText($status) {
            return match($status) {
                'included' => 'يدخل في مسير الرواتب',
                'excluded' => 'مستبعد من مسير الرواتب',
                default => $status ?: '-',
            };
        }

        function payrollStatusBadge($status) {
            if ($status === 'included' || empty($status)) {
                return '<span class="badge badge-active">يدخل في المسير</span>';
            }

            if ($status === 'excluded') {
                return '<span class="badge-danger-soft">مستبعد من المسير</span>';
            }

            return '<span class="badge-muted">' . $status . '</span>';
        }

        function documentPhotoBox($photo, $title) {
            if (!$photo) {
                return '
                    <div class="document-photo-box">
                        <div class="document-photo-title">' . $title . '</div>
                        <div class="document-photo-empty">
                            لا توجد صورة محفوظة
                        </div>
                    </div>
                ';
            }

            $url = asset('storage/' . $photo);
            $extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);

            $preview = $isImage
                ? '<img src="' . $url . '" alt="' . $title . '">'
                : '<div class="document-file-icon"><i class="fas fa-file-pdf"></i></div>';

            return '
                <div class="document-photo-box">
                    <div class="document-photo-title">' . $title . '</div>

                    <div class="document-photo-preview">
                        ' . $preview . '

                        <div class="document-photo-actions">
                            <a class="document-photo-link" href="' . $url . '" target="_blank">
                                <i class="fas fa-eye"></i>
                                عرض الملف
                            </a>
                        </div>
                    </div>
                </div>
            ';
        }
    @endphp

    <div class="employee-profile-header">
        <div class="employee-main-info">
            <div class="employee-avatar">
                <i class="fas fa-user"></i>
            </div>

            <div>
                <h2>{{ $employee->display_name }}</h2>
                <p>
                    {{ $employee->employee_number }}
                    —
                    {{ $employee->position->title ?? 'بدون وظيفة' }}
                    —
                    {{ $employee->department->name ?? 'بدون قسم' }}
                </p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="{{ route('employees.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>

            @if(auth()->user()->hasPermission('employees.edit'))
                <a href="{{ route('employees.edit', $employee->id) }}" class="hero-btn white">
                    <i class="fas fa-pen"></i>
                    تعديل
                </a>
            @endif
        </div>
    </div>

    <div class="card info-section">
        <div class="section-title">
            <i class="fas fa-id-card"></i>
            البيانات الأساسية
        </div>

        <div class="info-grid">
            <div class="info-box">
                <label>الرقم الوظيفي</label>
                <strong>{{ $employee->employee_number ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>الاسم الكامل</label>
                <strong>{{ $employee->display_name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>الجنسية</label>
                <strong>{{ $employee->nationality->name_ar ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>البريد الإلكتروني</label>
                <strong>{{ $employee->email ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>رقم الجوال</label>
                <strong>{{ $employee->phone ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>الحالة</label>
                <strong>{{ employeeStatusText($employee->status) }}</strong>
            </div>
        </div>
    </div>

    <div class="card info-section">
        <div class="section-title">
            <i class="fas fa-briefcase"></i>
            البيانات الوظيفية
        </div>

        <div class="info-grid">
            <div class="info-box">
                <label>القسم</label>
                <strong>{{ $employee->department->name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>الوظيفة</label>
                <strong>{{ $employee->position->title ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>تاريخ المباشرة</label>
                <strong>{{ optional($employee->hire_date)->format('Y-m-d') ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>تاريخ انتهاء الخدمة</label>
                <strong>{{ optional($employee->termination_date)->format('Y-m-d') ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>سبب انتهاء الخدمة</label>
                <strong>{{ $employee->termination_reason ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="card info-section">
        <div class="section-title">
            <i class="fas fa-money-bill-wave"></i>
            بيانات الراتب والبدلات
        </div>

        <div class="info-grid">
            <div class="info-box">
                <label>الراتب الأساسي</label>
                <strong>{{ number_format($employee->basic_salary, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>بدل السكن</label>
                <strong>{{ number_format($employee->housing_allowance, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>بدل النقل</label>
                <strong>{{ number_format($employee->transport_allowance, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>بدل الطعام</label>
                <strong>{{ number_format($employee->food_allowance, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>بدلات أخرى</label>
                <strong>{{ number_format($employee->other_allowance, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>إجمالي الراتب والبدلات</label>
                <strong>
                    {{ number_format(
                        $employee->basic_salary +
                        $employee->housing_allowance +
                        $employee->transport_allowance +
                        $employee->food_allowance +
                        $employee->other_allowance,
                        2
                    ) }}
                </strong>
            </div>
        </div>
    </div>

    <div class="card info-section">
        <div class="section-title">
            <i class="fas fa-building-columns"></i>
            بيانات البنك
        </div>

        <div class="info-grid">
            <div class="info-box">
                <label>اسم البنك</label>
                <strong>{{ $employee->bank_name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>IBAN</label>
                <strong>{{ $employee->iban ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="card info-section">
        <div class="section-title">
            <i class="fas fa-file-invoice-dollar"></i>
            بيانات مسير الرواتب
        </div>

        <div class="info-grid">
            <div class="info-box">
                <label>طريقة صرف الراتب</label>
                <strong>{{ $employee->salaryPaymentMethod->name_ar ?? $employee->salary_payment_method_name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>حالة الموظف في مسير الرواتب</label>
                <strong>{!! payrollStatusBadge($employee->payroll_status) !!}</strong>
            </div>

            <div class="info-box">
                <label>تاريخ سريان الراتب</label>
                <strong>{{ optional($employee->salary_effective_date)->format('Y-m-d') ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>اسم صاحب الحساب البنكي</label>
                <strong>{{ $employee->bank_account_name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>مجموعة الرواتب</label>
                <strong>{{ $employee->payrollGroup->name_ar ?? $employee->payroll_group_name ?? '-' }}</strong>
            </div>

            <div class="info-box">
                <label>مركز التكلفة</label>
                <strong>
                    @if($employee->costCenter)
                        {{ $employee->costCenter->code }} - {{ $employee->costCenter->name_ar }}
                    @else
                        {{ $employee->cost_center_name ?? '-' }}
                    @endif
                </strong>
            </div>

            <div class="info-box">
                <label>إجمالي الراتب الحالي</label>
                <strong>{{ number_format($employee->current_total_salary, 2) }}</strong>
            </div>

            <div class="info-box">
                <label>آخر مسير راتب</label>
                <strong>
                    @if($employee->latestPayrollItem && $employee->latestPayrollItem->payrollPeriod)
                        {{ $employee->latestPayrollItem->payrollPeriod->period_number }}
                        -
                        {{ $employee->latestPayrollItem->payrollPeriod->month }}
                    @else
                        -
                    @endif
                </strong>
            </div>

            <div class="info-box">
                <label>صافي آخر راتب محتسب</label>
                <strong>
                    @if($employee->latestPayrollItem)
                        {{ number_format($employee->latestPayrollItem->net_salary, 2) }}
                    @else
                        -
                    @endif
                </strong>
            </div>
        </div>
    </div>

    @if(
        auth()->user()->hasPermission('employee_iqamas.view') ||
        auth()->user()->hasPermission('employee_passports.view') ||
        auth()->user()->hasPermission('employee_health_cards.view') ||
        auth()->user()->hasPermission('employee_iqamas.photo.view') ||
        auth()->user()->hasPermission('employee_passports.photo.view') ||
        auth()->user()->hasPermission('employee_health_cards.photo.view')
    )
        <div class="card info-section">
            <div class="section-title">
                <i class="fas fa-passport"></i>
                الوثائق والمتابعات
            </div>

            <div class="documents-grid">

                @if(auth()->user()->hasPermission('employee_iqamas.view') || auth()->user()->hasPermission('employee_iqamas.photo.view'))
                    <div class="document-card">
                        <h4>
                            <i class="fas fa-id-card"></i>
                            الإقامة
                        </h4>

                        @if(auth()->user()->hasPermission('employee_iqamas.view'))
                            <div class="document-row">
                                <span>الحالة</span>
                                <strong>{!! documentStatusBadge($employee->latestIqama) !!}</strong>
                            </div>

                            <div class="document-row">
                                <span>رقم الإقامة</span>
                                <strong>{{ $employee->latestIqama->iqama_number ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>تاريخ الانتهاء</span>
                                <strong>{{ optional($employee->latestIqama?->expiry_date)->format('Y-m-d') ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>الأيام المتبقية</span>
                                <strong>{{ $employee->latestIqama->remaining_days ?? '-' }}</strong>
                            </div>
                        @endif

                        @if(auth()->user()->hasPermission('employee_iqamas.photo.view'))
                            {!! documentPhotoBox($employee->latestIqama->photo ?? null, 'صورة الإقامة') !!}
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasPermission('employee_passports.view') || auth()->user()->hasPermission('employee_passports.photo.view'))
                    <div class="document-card">
                        <h4>
                            <i class="fas fa-passport"></i>
                            الجواز
                        </h4>

                        @if(auth()->user()->hasPermission('employee_passports.view'))
                            <div class="document-row">
                                <span>الحالة</span>
                                <strong>{!! documentStatusBadge($employee->latestPassport) !!}</strong>
                            </div>

                            <div class="document-row">
                                <span>رقم الجواز</span>
                                <strong>{{ $employee->latestPassport->passport_number ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>تاريخ الانتهاء</span>
                                <strong>{{ optional($employee->latestPassport?->expiry_date)->format('Y-m-d') ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>الأيام المتبقية</span>
                                <strong>{{ $employee->latestPassport->remaining_days ?? '-' }}</strong>
                            </div>
                        @endif

                        @if(auth()->user()->hasPermission('employee_passports.photo.view'))
                            {!! documentPhotoBox($employee->latestPassport->photo ?? null, 'صورة الجواز') !!}
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasPermission('employee_health_cards.view') || auth()->user()->hasPermission('employee_health_cards.photo.view'))
                    <div class="document-card">
                        <h4>
                            <i class="fas fa-notes-medical"></i>
                            الكرت الصحي
                        </h4>

                        @if(auth()->user()->hasPermission('employee_health_cards.view'))
                            <div class="document-row">
                                <span>الحالة</span>
                                <strong>{!! documentStatusBadge($employee->latestHealthCard) !!}</strong>
                            </div>

                            <div class="document-row">
                                <span>رقم الكرت</span>
                                <strong>{{ $employee->latestHealthCard->card_number ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>تاريخ الانتهاء</span>
                                <strong>{{ optional($employee->latestHealthCard?->expiry_date)->format('Y-m-d') ?? '-' }}</strong>
                            </div>

                            <div class="document-row">
                                <span>الأيام المتبقية</span>
                                <strong>{{ $employee->latestHealthCard->remaining_days ?? '-' }}</strong>
                            </div>
                        @endif

                        @if(auth()->user()->hasPermission('employee_health_cards.photo.view'))
                            {!! documentPhotoBox($employee->latestHealthCard->photo ?? null, 'صورة الكرت الصحي') !!}
                        @endif
                    </div>
                @endif

            </div>
        </div>
    @endif

    @if(auth()->user()->hasPermission('employees.salary_history.view'))
        <div class="card info-section">
            <div class="section-title">
                <i class="fas fa-chart-line"></i>
                سجل تغييرات الراتب الأساسي
            </div>

            <table>
                <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الراتب السابق</th>
                    <th>الراتب الجديد</th>
                    <th>فرق التغيير</th>
                    <th>النسبة</th>
                    <th>تم التعديل بواسطة</th>
                    <th>السبب</th>
                </tr>
                </thead>

                <tbody>
                @forelse($employee->salaryHistories as $history)
                    <tr>
                        <td>{{ optional($history->effective_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ number_format($history->old_basic_salary, 2) }}</td>
                        <td>{{ number_format($history->new_basic_salary, 2) }}</td>
                        <td>{{ number_format($history->change_amount, 2) }}</td>
                        <td>{{ number_format($history->change_percentage, 2) }}%</td>
                        <td>{{ $history->changedBy->name ?? '-' }}</td>
                        <td>{{ $history->reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">لا يوجد سجل تغييرات للراتب</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($employee->notes)
        <div class="card info-section">
            <div class="section-title">
                <i class="fas fa-note-sticky"></i>
                ملاحظات
            </div>

            <p>{{ $employee->notes }}</p>
        </div>
    @endif

@endsection
