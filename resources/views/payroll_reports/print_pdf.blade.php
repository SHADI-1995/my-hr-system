<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مسير الرواتب</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #fff;
            color: #111827;
            margin: 14px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #4c3b91;
            padding-bottom: 12px;
            margin-bottom: 14px;
            gap: 12px;
            align-items: center;
        }

        h1 { color: #4c3b91; margin: 0 0 8px; font-size: 22px; }
        p { margin: 0; font-weight: bold; color: #374151; }

        .stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 7px;
            margin-bottom: 12px;
        }

        .stat {
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            padding: 8px;
            text-align: center;
            background: #faf9ff;
        }

        .stat small {
            display: block;
            color: #6b5aa8;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 9px;
        }

        .stat strong { color: #3b2b80; font-size: 11px; }

        .settings-box {
            border: 1px solid #ddd6fe;
            border-radius: 12px;
            background: #faf9ff;
            padding: 8px;
            margin-bottom: 12px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px;
        }

        .settings-box div {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 10px;
            padding: 7px;
            text-align: center;
        }

        .settings-box small {
            display: block;
            color: #6b5aa8;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .settings-box strong { color: #3b2b80; font-size: 10px; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.7px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #ede9fe;
            color: #4c3b91;
            font-weight: bold;
        }

        .group-title { background: #dcd2ff; color: #2f1d75; }
        .number { direction: ltr; white-space: nowrap; }
        .total-cell { background: #f5f3ff; color: #3b2b80; font-weight: bold; direction: ltr; }
        .print-btn { background: #4c3b91; color: #fff; border: 0; padding: 9px 14px; border-radius: 8px; cursor: pointer; font-weight: bold; white-space: nowrap; }
        .total-row { background: #f5f3ff; font-weight: bold; }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
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

    $totalBasic = 0;
    $totalHousing = 0;
    $totalTransport = 0;
    $totalFood = 0;
    $totalOther = 0;
    $totalAllowances = 0;
    $totalGross = 0;
    $totalUnpaid = 0;
    $totalSuspension = 0;
    $totalRegular = 0;
    $totalAdvance = 0;
    $totalDeductions = 0;
    $totalNet = 0;
    $totalPayableDays = 0;
@endphp

<div class="header">
    <div>
        <h1>تقرير مسير الرواتب</h1>
        <p>
            رقم المسير: {{ $payrollPeriod->period_number }} |
            الشهر: {{ $payrollPeriod->month }} |
            الحالة: {{ $payrollPeriod->status }}
        </p>
    </div>

    <button class="print-btn no-print" onclick="window.print()">طباعة / حفظ PDF</button>
</div>

<div class="settings-box">
    <div><small>طريقة احتساب الأيام</small><strong>{{ $salaryDayCalculationName }}</strong></div>
    <div><small>التقريب</small><strong>{{ $payrollSetting->rounding_decimals ?? 2 }} خانات</strong></div>
</div>

<div class="stats">
    <div class="stat"><small>الموظفين</small><strong>{{ $payrollPeriod->employees_count }}</strong></div>
    <div class="stat"><small>إجمالي الراتب</small><strong>{{ number_format($payrollPeriod->total_gross_salary, 2) }}</strong></div>
    <div class="stat"><small>استقطاعات</small><strong>{{ number_format($payrollPeriod->total_regular_deductions, 2) }}</strong></div>
    <div class="stat"><small>سلف</small><strong>{{ number_format($payrollPeriod->total_salary_advances, 2) }}</strong></div>
    <div class="stat"><small>إيقافات/إجازات</small><strong>{{ number_format($payrollPeriod->total_suspension_deductions, 2) }}</strong></div>
    <div class="stat"><small>الصافي</small><strong>{{ number_format($payrollPeriod->total_net_salary, 2) }}</strong></div>
</div>

<table>
    <thead>
    <tr>
        <th rowspan="2">رقم الموظف</th>
        <th rowspan="2">الموظف</th>
        <th rowspan="2">الجنسية</th>
        <th rowspan="2">الوظيفة</th>
        <th rowspan="2">القسم</th>
        <th rowspan="2">حالة الموظف</th>
        <th rowspan="2">طريقة الصرف</th>
        <th rowspan="2">مجموعة الرواتب</th>
        <th rowspan="2">مركز التكلفة</th>
        <th rowspan="2">تاريخ الاستحقاق</th>
        <th rowspan="2">أيام الاستحقاق</th>
        <th rowspan="2">الراتب الأساسي</th>
        <th colspan="5" class="group-title">تفاصيل البدلات</th>
        <th rowspan="2">إجمالي الراتب</th>
        <th colspan="5" class="group-title">تفاصيل الاستقطاعات</th>
        <th rowspan="2">الصافي</th>
    </tr>
    <tr>
        <th>سكن</th>
        <th>نقل</th>
        <th>طعام</th>
        <th>أخرى</th>
        <th>إجمالي البدلات</th>

        <th>إجازات</th>
        <th>إيقافات</th>
        <th>استقطاعات</th>
        <th>سلف</th>
        <th>إجمالي الخصم</th>
    </tr>
    </thead>
    <tbody>
    @forelse($payrollPeriod->items as $item)
        @php
            $employee = $item->employee;

            $nationalityName =
                $item->employee_nationality
                ?? $employee?->nationality?->name_ar
                ?? $employee?->nationality?->name
                ?? $employee?->nationality_name
                ?? $employee?->nationality
                ?? '-';

            $positionName =
                $item->employee_position
                ?? $employee?->position?->title
                ?? $employee?->position?->name_ar
                ?? $employee?->position?->name
                ?? $employee?->position_name
                ?? $employee?->job_title
                ?? '-';

            $departmentName =
                $item->employee_department
                ?? $employee?->department?->name
                ?? $employee?->department?->name_ar
                ?? $employee?->department_name
                ?? '-';

            $paymentMethodEmployee =
                $item->salary_payment_method_name
                ?? $employee?->salary_payment_method_name
                ?? $employee?->salaryPaymentMethod?->name_ar
                ?? $employee?->salaryPaymentMethod?->name
                ?? $employee?->paymentMethod?->name_ar
                ?? $employee?->paymentMethod?->name
                ?? $employee?->salary_payment_method
                ?? $paymentMethodName
                ?? '-';

            $payrollGroupName =
                $item->payroll_group_name
                ?? $employee?->payroll_group_name
                ?? $employee?->payrollGroup?->name_ar
                ?? $employee?->payrollGroup?->name
                ?? $employee?->payroll_group
                ?? '-';

            $costCenterName =
                $item->cost_center_name
                ?? $employee?->cost_center_name
                ?? $employee?->costCenter?->name_ar
                ?? $employee?->costCenter?->name
                ?? $employee?->cost_center
                ?? '-';

            $employeeStatusText =
                $item->employee_status_text
                ?? $item->employment_status_note
                ?? null;

            if (!$employeeStatusText || trim((string) $employeeStatusText) === '-') {
                $employeeStatusText = match ((string) ($employee?->status ?? '')) {
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
                $employeeStatusText = match ((string) ($employee?->payroll_status ?? '')) {
                    'included' => 'مدرج في مسير الرواتب',
                    'excluded' => 'مستبعد من مسير الرواتب',
                    default => 'نشط طوال الفترة',
                };
            }

            $periodStart = \Carbon\Carbon::parse($payrollPeriod->start_date)->startOfDay();
            $periodEnd = \Carbon\Carbon::parse($payrollPeriod->end_date)->startOfDay();

            $employeeHireDate = $employee?->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->startOfDay() : null;
            $employeeTerminationDate = $employee?->termination_date ? \Carbon\Carbon::parse($employee->termination_date)->startOfDay() : null;

            $eligibleStart = $item->eligible_start_date
                ? \Carbon\Carbon::parse($item->eligible_start_date)->startOfDay()
                : ($employeeHireDate && $employeeHireDate->gt($periodStart) ? $employeeHireDate->copy() : $periodStart->copy());

            $eligibleEnd = $item->eligible_end_date
                ? \Carbon\Carbon::parse($item->eligible_end_date)->startOfDay()
                : ($employeeTerminationDate && $employeeTerminationDate->lt($periodEnd) ? $employeeTerminationDate->copy() : $periodEnd->copy());

            if ($employeeHireDate && $employeeHireDate->gt($periodStart) && $eligibleStart->lt($employeeHireDate)) {
                $eligibleStart = $employeeHireDate->copy();
            }

            if ($employeeTerminationDate && $employeeTerminationDate->lt($periodEnd) && $eligibleEnd->gt($employeeTerminationDate)) {
                $eligibleEnd = $employeeTerminationDate->copy();
            }

            $actualEligibleDays = $eligibleEnd->lt($eligibleStart)
                ? 0
                : ((int) $eligibleStart->diffInDays($eligibleEnd) + 1);

            if (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'working_days') {
                $payableDays = 0;

                if ($eligibleEnd->gte($eligibleStart)) {
                    $cursor = $eligibleStart->copy();

                    while ($cursor->lte($eligibleEnd)) {
                        if (!$cursor->isFriday()) {
                            $payableDays++;
                        }

                        $cursor->addDay();
                    }
                }
            } elseif (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'fixed_30_days') {
                $isFullPeriod = $eligibleStart->isSameDay($periodStart) && $eligibleEnd->isSameDay($periodEnd);
                $payableDays = $isFullPeriod ? 30 : min($actualEligibleDays, 30);
            } else {
                $payableDays = $actualEligibleDays;
            }

            $eligibleDateText = $eligibleStart->format('Y-m-d') . ' إلى ' . $eligibleEnd->format('Y-m-d');

            $basic = (float) ($item->basic_salary ?? 0);
            $housing = (float) ($item->housing_allowance ?? 0);
            $transport = (float) ($item->transport_allowance ?? 0);
            $food = (float) ($item->food_allowance ?? 0);
            $other = (float) ($item->other_allowance ?? 0);
            $allowances = $housing + $transport + $food + $other;

            $gross = (float) ($item->gross_salary ?? 0);
            $unpaid = (float) ($item->unpaid_leave_deductions ?? 0);
            $suspension = (float) ($item->suspension_deductions ?? 0);
            $regular = (float) ($item->regular_deductions ?? 0);
            $advance = (float) ($item->salary_advance_deductions ?? 0);
            $deductions = (float) ($item->total_deductions ?? ($unpaid + $suspension + $regular + $advance));
            $net = (float) ($item->net_salary ?? 0);

            $totalBasic += $basic;
            $totalHousing += $housing;
            $totalTransport += $transport;
            $totalFood += $food;
            $totalOther += $other;
            $totalAllowances += $allowances;
            $totalGross += $gross;
            $totalUnpaid += $unpaid;
            $totalSuspension += $suspension;
            $totalRegular += $regular;
            $totalAdvance += $advance;
            $totalDeductions += $deductions;
            $totalNet += $net;
            $totalPayableDays += $payableDays;
        @endphp

        <tr>
            <td>{{ $item->employee_number }}</td>
            <td>{{ $item->employee_name }}</td>
            <td>{{ $nationalityName }}</td>
            <td>{{ $positionName }}</td>
            <td>{{ $departmentName }}</td>
            <td>{{ $employeeStatusText }}</td>
            <td>{{ $paymentMethodEmployee }}</td>
            <td>{{ $payrollGroupName }}</td>
            <td>{{ $costCenterName }}</td>
            <td>{{ $eligibleDateText }}</td>
            <td class="number">{{ $payableDays }}</td>
            <td class="number">{{ number_format($basic, 2) }}</td>

            <td class="number">{{ number_format($housing, 2) }}</td>
            <td class="number">{{ number_format($transport, 2) }}</td>
            <td class="number">{{ number_format($food, 2) }}</td>
            <td class="number">{{ number_format($other, 2) }}</td>
            <td class="total-cell">{{ number_format($allowances, 2) }}</td>

            <td class="number">{{ number_format($gross, 2) }}</td>

            <td class="number">{{ number_format($unpaid, 2) }}</td>
            <td class="number">{{ number_format($suspension, 2) }}</td>
            <td class="number">{{ number_format($regular, 2) }}</td>
            <td class="number">{{ number_format($advance, 2) }}</td>
            <td class="total-cell">{{ number_format($deductions, 2) }}</td>

            <td class="number"><strong>{{ number_format($net, 2) }}</strong></td>
        </tr>
    @empty
        <tr>
            <td colspan="24">لا توجد تفاصيل لهذا المسير.</td>
        </tr>
    @endforelse

    <tr class="total-row">
        <td colspan="10">الإجمالي</td>
        <td class="number">{{ $totalPayableDays }}</td>
        <td class="number">{{ number_format($totalBasic, 2) }}</td>

        <td class="number">{{ number_format($totalHousing, 2) }}</td>
        <td class="number">{{ number_format($totalTransport, 2) }}</td>
        <td class="number">{{ number_format($totalFood, 2) }}</td>
        <td class="number">{{ number_format($totalOther, 2) }}</td>
        <td class="total-cell">{{ number_format($totalAllowances, 2) }}</td>

        <td class="number">{{ number_format($totalGross, 2) }}</td>

        <td class="number">{{ number_format($totalUnpaid, 2) }}</td>
        <td class="number">{{ number_format($totalSuspension, 2) }}</td>
        <td class="number">{{ number_format($totalRegular, 2) }}</td>
        <td class="number">{{ number_format($totalAdvance, 2) }}</td>
        <td class="total-cell">{{ number_format($totalDeductions, 2) }}</td>

        <td class="number">{{ number_format($totalNet, 2) }}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
