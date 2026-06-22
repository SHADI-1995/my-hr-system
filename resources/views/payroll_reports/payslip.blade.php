<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قسيمة راتب</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f4f1fb;
            color: #111827;
            margin: 0;
            padding: 25px;
        }

        .slip {
            max-width: 950px;
            margin: auto;
            background: #fff;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #e9d5ff;
            box-shadow: 0 20px 45px rgba(76, 59, 145, .15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #4c3b91;
            padding-bottom: 15px;
            margin-bottom: 20px;
            gap: 15px;
            align-items: center;
        }

        h1 {
            color: #4c3b91;
            margin: 0 0 8px;
        }

        p {
            margin: 0;
            font-weight: bold;
            color: #374151;
        }

        .info-grid,
        .summary-grid,
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .settings-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .box {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 12px;
            background: #faf9ff;
        }

        .box small {
            display: block;
            color: #6b5aa8;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .box strong {
            color: #111827;
        }

        .section-title {
            color: #4c3b91;
            font-size: 17px;
            font-weight: bold;
            margin: 22px 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #ede9fe;
            color: #4c3b91;
        }

        .number {
            direction: ltr;
            white-space: nowrap;
        }

        .earning-row {
            background: #f0fdf4;
        }

        .deduction-row {
            background: #fff7ed;
        }

        .advance-row {
            background: #eff6ff;
        }

        .suspension-row {
            background: #fef2f2;
        }

        .total-box {
            background: #f5f3ff;
            color: #3b2b80;
            font-weight: bold;
        }

        .net {
            background: #dcfce7;
            color: #166534;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            border-radius: 14px;
            padding: 16px;
            margin-top: 20px;
        }

        .print-btn {
            background: #4c3b91;
            color: #fff;
            border: 0;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .warning-note {
            margin-top: 6px;
            color: #b45309;
            font-size: 12px;
            font-weight: bold;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .slip { box-shadow: none; border: 0; max-width: 100%; }
            .no-print { display: none; }
        }

        @media(max-width: 800px) {
            .info-grid,
            .summary-grid,
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
@php
    $payrollSetting = $payrollSetting ?? \App\Models\PayrollSetting::current();

    $salaryDayCalculationName = match ($payrollSetting->salary_day_calculation ?? 'fixed_30_days') {
        'fixed_30_days' => '30 يوم ثابت',
        'actual_month_days' => 'أيام الشهر الفعلية',
        'working_days' => 'أيام العمل فقط',
        default => $payrollSetting->salary_day_calculation ?? '-',
    };

    $paymentMethodName = match ($payrollSetting->default_payment_method ?? 'bank_transfer') {
        'bank_transfer' => 'تحويل بنكي',
        'cash' => 'نقدي',
        'cheque' => 'شيك',
        'other' => 'أخرى',
        default => $payrollSetting->default_payment_method ?? '-',
    };

    $period = $payrollItem->payrollPeriod;

    $periodStart = $period?->start_date
        ? \Carbon\Carbon::parse($period->start_date)->startOfDay()
        : null;

    $periodEnd = $period?->end_date
        ? \Carbon\Carbon::parse($period->end_date)->startOfDay()
        : null;

    $employeeHireDate = $payrollItem->employee?->hire_date
        ? \Carbon\Carbon::parse($payrollItem->employee->hire_date)->startOfDay()
        : null;

    $employeeTerminationDate = $payrollItem->employee?->termination_date
        ? \Carbon\Carbon::parse($payrollItem->employee->termination_date)->startOfDay()
        : null;

    $eligibleStart = $payrollItem->eligible_start_date
        ? \Carbon\Carbon::parse($payrollItem->eligible_start_date)->startOfDay()
        : ($employeeHireDate && $periodStart && $employeeHireDate->gt($periodStart) ? $employeeHireDate->copy() : ($periodStart?->copy()));

    $eligibleEnd = $payrollItem->eligible_end_date
        ? \Carbon\Carbon::parse($payrollItem->eligible_end_date)->startOfDay()
        : ($employeeTerminationDate && $periodEnd && $employeeTerminationDate->lt($periodEnd) ? $employeeTerminationDate->copy() : ($periodEnd?->copy()));

    $actualEligibleDays = 0;

    if ($eligibleStart && $eligibleEnd && $eligibleEnd->gte($eligibleStart)) {
        $actualEligibleDays = (int) $eligibleStart->diffInDays($eligibleEnd) + 1;
    }

    if (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'working_days') {
        $displayPayableDays = 0;

        if ($eligibleStart && $eligibleEnd && $eligibleEnd->gte($eligibleStart)) {
            $dayCursor = $eligibleStart->copy();

            while ($dayCursor->lte($eligibleEnd)) {
                if (!$dayCursor->isFriday()) {
                    $displayPayableDays++;
                }

                $dayCursor->addDay();
            }
        }

        $displayPeriodDays = (int) ($payrollItem->period_days ?: $displayPayableDays);
    } elseif (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'fixed_30_days') {
        $isFullPeriod = $periodStart && $periodEnd && $eligibleStart && $eligibleEnd
            && $eligibleStart->isSameDay($periodStart)
            && $eligibleEnd->isSameDay($periodEnd);

        $displayPayableDays = $isFullPeriod ? 30 : min($actualEligibleDays, 30);
        $displayPeriodDays = 30;
    } else {
        $displayPayableDays = $actualEligibleDays;
        $displayPeriodDays = (int) ($payrollItem->period_days ?: ($periodStart && $periodEnd ? ((int) $periodStart->diffInDays($periodEnd) + 1) : 0));
    }

    $storedPayableDays = (int) ($payrollItem->payable_days ?? 0);
    $needsRecalculateWarning = $storedPayableDays !== $displayPayableDays;

    $eligibleStartText = $eligibleStart ? $eligibleStart->format('Y-m-d') : '-';
    $eligibleEndText = $eligibleEnd ? $eligibleEnd->format('Y-m-d') : '-';

    $employeeStatusText = $payrollItem->employment_status_note;

    if (!$employeeStatusText || trim((string) $employeeStatusText) === '-') {
        $employeeStatusText = match ((string) ($payrollItem->employee?->status ?? '')) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'terminated' => 'منتهي الخدمة',
            'resigned' => 'مستقيل',
            'suspended' => 'موقوف',
            'on_leave' => 'في إجازة',
            default => null,
        };
    }

    if (!$employeeStatusText) {
        $employeeStatusText = match ((string) ($payrollItem->employee?->payroll_status ?? '')) {
            'included' => 'مدرج في مسير الرواتب',
            'excluded' => 'مستبعد من مسير الرواتب',
            default => 'نشط طوال الفترة',
        };
    }

    $basic = (float) ($payrollItem->basic_salary ?? 0);
    $housing = (float) ($payrollItem->housing_allowance ?? 0);
    $transport = (float) ($payrollItem->transport_allowance ?? 0);
    $food = (float) ($payrollItem->food_allowance ?? 0);
    $other = (float) ($payrollItem->other_allowance ?? 0);
    $totalAllowances = $housing + $transport + $food + $other;

    $unpaid = (float) ($payrollItem->unpaid_leave_deductions ?? 0);
    $suspension = (float) ($payrollItem->suspension_deductions ?? 0);
    $regular = (float) ($payrollItem->regular_deductions ?? 0);
    $advance = (float) ($payrollItem->salary_advance_deductions ?? 0);
    $totalDeductions = (float) ($payrollItem->total_deductions ?? ($unpaid + $suspension + $regular + $advance));

    $componentsByType = $payrollItem->components->groupBy('type');
@endphp

<div class="slip">
    <div class="header">
        <div>
            <h1>قسيمة راتب</h1>
            <p>
                مسير: {{ $payrollItem->payrollPeriod?->period_number }} |
                شهر: {{ $payrollItem->payrollPeriod?->month }}
            </p>
        </div>

        <button class="print-btn no-print" onclick="window.print()">طباعة</button>
    </div>

    <div class="settings-grid">
        <div class="box">
            <small>طريقة احتساب الأيام</small>
            <strong>{{ $salaryDayCalculationName }}</strong>
        </div>
        <div class="box">
            <small>طريقة الصرف</small>
            <strong>{{ $paymentMethodName }}</strong>
        </div>
        <div class="box">
            <small>التقريب</small>
            <strong>{{ $payrollSetting->rounding_decimals ?? 2 }} خانات</strong>
        </div>
        <div class="box">
            <small>الصافي السالب</small>
            <strong>{{ ($payrollSetting->allow_negative_net_salary ?? false) ? 'مسموح' : 'غير مسموح' }}</strong>
        </div>
    </div>

    <div class="info-grid">
        <div class="box">
            <small>رقم الموظف</small>
            <strong>{{ $payrollItem->employee_number }}</strong>
        </div>
        <div class="box">
            <small>اسم الموظف</small>
            <strong>{{ $payrollItem->employee_name }}</strong>
        </div>
        <div class="box">
            <small>القسم</small>
            <strong>{{ $payrollItem->employee?->department?->name ?? '-' }}</strong>
        </div>
        <div class="box">
            <small>الوظيفة</small>
            <strong>{{ $payrollItem->employee?->position?->title ?? '-' }}</strong>
        </div>
        <div class="box">
            <small>تاريخ الاستحقاق</small>
            <strong>{{ $eligibleStartText }} إلى {{ $eligibleEndText }}</strong>
        </div>
        <div class="box">
            <small>أيام الاستحقاق</small>
            <strong>{{ $displayPayableDays }} يوم من أصل {{ $displayPeriodDays }} يوم</strong>

            @if($needsRecalculateWarning)
                <div class="warning-note no-print">
                </div>
            @endif
        </div>
        <div class="box">
            <small>حالة الموظف في المسير</small>
            <strong>{{ $employeeStatusText }}</strong>
        </div>
    </div>

    <div class="summary-grid">
        <div class="box">
            <small>الراتب الأساسي المستحق</small>
            <strong class="number">{{ number_format($basic, 2) }}</strong>
        </div>
        <div class="box">
            <small>إجمالي البدلات</small>
            <strong class="number">{{ number_format($totalAllowances, 2) }}</strong>
        </div>
        <div class="box">
            <small>إجمالي الراتب</small>
            <strong class="number">{{ number_format($payrollItem->gross_salary, 2) }}</strong>
        </div>
        <div class="box">
            <small>إجمالي الاستقطاعات</small>
            <strong class="number">{{ number_format($totalDeductions, 2) }}</strong>
        </div>
    </div>

    <div class="section-title">تفاصيل البدلات والاستحقاقات</div>
    <table>
        <thead>
        <tr>
            <th>البند</th>
            <th>المبلغ</th>
        </tr>
        </thead>
        <tbody>
        <tr class="earning-row">
            <td>الراتب الأساسي المستحق</td>
            <td class="number">{{ number_format($basic, 2) }}</td>
        </tr>
        <tr class="earning-row">
            <td>بدل السكن</td>
            <td class="number">{{ number_format($housing, 2) }}</td>
        </tr>
        <tr class="earning-row">
            <td>بدل النقل</td>
            <td class="number">{{ number_format($transport, 2) }}</td>
        </tr>
        <tr class="earning-row">
            <td>بدل الطعام</td>
            <td class="number">{{ number_format($food, 2) }}</td>
        </tr>
        <tr class="earning-row">
            <td>بدلات أخرى</td>
            <td class="number">{{ number_format($other, 2) }}</td>
        </tr>
        <tr class="total-box">
            <td>إجمالي البدلات</td>
            <td class="number">{{ number_format($totalAllowances, 2) }}</td>
        </tr>
        <tr class="total-box">
            <td>إجمالي الراتب</td>
            <td class="number">{{ number_format($payrollItem->gross_salary, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-title">تفاصيل الاستقطاعات</div>
    <table>
        <thead>
        <tr>
            <th>البند</th>
            <th>المبلغ</th>
        </tr>
        </thead>
        <tbody>
        <tr class="deduction-row">
            <td>إجازات غير مدفوعة</td>
            <td class="number">{{ number_format($unpaid, 2) }}</td>
        </tr>
        <tr class="suspension-row">
            <td>إيقافات</td>
            <td class="number">{{ number_format($suspension, 2) }}</td>
        </tr>
        <tr class="deduction-row">
            <td>استقطاعات الموظف</td>
            <td class="number">{{ number_format($regular, 2) }}</td>
        </tr>
        <tr class="advance-row">
            <td>سلف الموظف</td>
            <td class="number">{{ number_format($advance, 2) }}</td>
        </tr>
        <tr class="total-box">
            <td>إجمالي الاستقطاعات</td>
            <td class="number">{{ number_format($totalDeductions, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-title">تفاصيل البنود المسجلة</div>
    <table>
        <thead>
        <tr>
            <th>البند</th>
            <th>النوع</th>
            <th>المبلغ</th>
            <th>ملاحظات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($payrollItem->components as $component)
            <tr class="{{ in_array($component->type, ['deduction'], true) ? 'deduction-row' : (in_array($component->type, ['salary_advance'], true) ? 'advance-row' : (in_array($component->type, ['suspension'], true) ? 'suspension-row' : 'earning-row')) }}">
                <td>{{ $component->name }}</td>
                <td>{{ $component->type }}</td>
                <td class="number">{{ number_format($component->amount, 2) }}</td>
                <td>{{ $component->notes ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">لا توجد مكونات لهذا الراتب.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="net">
        صافي الراتب: {{ number_format($payrollItem->net_salary, 2) }}
    </div>
</div>
</body>
</html>
