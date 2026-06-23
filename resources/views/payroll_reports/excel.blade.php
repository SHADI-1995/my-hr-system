<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; direction: rtl; }
        table { border-collapse: collapse; width: 100%; }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
            mso-number-format: "\@";
        }
        th { background: #eee8ff; color: #2f1d75; font-weight: bold; }
        .title { font-size: 20px; font-weight: bold; text-align: right; background: #ffffff; border: none; }
        .group-title { background: #dcd2ff; color: #2f1d75; font-weight: bold; }
        .total-row { background: #f5f3ff; font-weight: bold; }
        .num { mso-number-format: "#,##0.00"; direction: ltr; }
        .days { mso-number-format: "0"; direction: ltr; }
    </style>
</head>
<body>
<table>
    <tr>
        <td colspan="24" class="title">
            تقرير مسير الرواتب - {{ $payrollPeriod->period_number }} - {{ $payrollPeriod->month }}
        </td>
    </tr>

    <tr>
        <td colspan="24" class="title">
            من {{ optional($payrollPeriod->start_date)->format('Y-m-d') }}
            إلى
            {{ optional($payrollPeriod->end_date)->format('Y-m-d') }}
        </td>
    </tr>

    <tr>
        <th rowspan="2">رقم الموظف</th>
        <th rowspan="2">الموظف</th>
        <th rowspan="2">الجنسية</th>
        <th rowspan="2">الوظيفة</th>
        <th rowspan="2">القسم</th>
        <th rowspan="2">حالة الموظف</th>
        <th rowspan="2">طريقة صرف الراتب</th>
        <th rowspan="2">مجموعة الرواتب</th>
        <th rowspan="2">مركز التكلفة</th>
        <th rowspan="2">تاريخ الاستحقاق</th>
        <th rowspan="2">أيام الاستحقاق</th>
        <th rowspan="2">الراتب الأساسي المستحق</th>

        <th colspan="5" class="group-title">تفاصيل البدلات</th>
        <th rowspan="2">إجمالي الراتب</th>
        <th colspan="5" class="group-title">تفاصيل الاستقطاعات</th>
        <th rowspan="2">الصافي</th>
    </tr>

    <tr>
        <th>بدل السكن</th>
        <th>بدل النقل</th>
        <th>بدل الطعام</th>
        <th>بدلات أخرى</th>
        <th>إجمالي البدلات</th>

        <th>إجازات غير مدفوعة</th>
        <th>إيقافات</th>
        <th>استقطاعات الموظف</th>
        <th>سلف الموظف</th>
        <th>إجمالي الاستقطاعات</th>
    </tr>

    @php
        $payrollSetting = $payrollSetting ?? \App\Models\PayrollSetting::current();

        $totalBasic = 0;
        $totalHousing = 0;
        $totalTransport = 0;
        $totalFood = 0;
        $totalOther = 0;
        $totalAllowances = 0;
        $totalGross = 0;
        $totalUnpaid = 0;
        $totalSuspension = 0;
        $totalRegularDeductions = 0;
        $totalAdvances = 0;
        $totalDeductions = 0;
        $totalNet = 0;
        $totalPayableDays = 0;
    @endphp

    @foreach($payrollPeriod->items as $item)
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

            $paymentMethodName =
                $item->salary_payment_method_name
                ?? $employee?->salary_payment_method_name
                ?? $employee?->salaryPaymentMethod?->name_ar
                ?? $employee?->salaryPaymentMethod?->name
                ?? $employee?->salary_payment_method
                ?? '-';

            $payrollGroupName =
                $item->payroll_group_name
                ?? $employee?->payrollGroup?->name_ar
                ?? $employee?->payrollGroup?->name
                ?? $employee?->payroll_group_name
                ?? $employee?->payroll_group
                ?? '-';

            $costCenterName =
                $item->cost_center_name
                ?? $employee?->costCenter?->name_ar
                ?? $employee?->costCenter?->name
                ?? $employee?->cost_center_name
                ?? $employee?->cost_center
                ?? '-';

            $employeeStatusText = $item->employee_status_text ?? $item->employment_status_note;

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

            $periodStartForExcel = \Carbon\Carbon::parse($payrollPeriod->start_date)->startOfDay();
            $periodEndForExcel = \Carbon\Carbon::parse($payrollPeriod->end_date)->startOfDay();

            $employeeHireDate = $employee?->hire_date
                ? \Carbon\Carbon::parse($employee->hire_date)->startOfDay()
                : null;

            $employeeTerminationDate = $employee?->termination_date
                ? \Carbon\Carbon::parse($employee->termination_date)->startOfDay()
                : null;

            $eligibleStartForExcel = $item->eligible_start_date
                ? \Carbon\Carbon::parse($item->eligible_start_date)->startOfDay()
                : ($employeeHireDate && $employeeHireDate->gt($periodStartForExcel) ? $employeeHireDate->copy() : $periodStartForExcel->copy());

            $eligibleEndForExcel = $item->eligible_end_date
                ? \Carbon\Carbon::parse($item->eligible_end_date)->startOfDay()
                : ($employeeTerminationDate && $employeeTerminationDate->lt($periodEndForExcel) ? $employeeTerminationDate->copy() : $periodEndForExcel->copy());

            if ($employeeHireDate && $employeeHireDate->gt($periodStartForExcel) && $eligibleStartForExcel->lt($employeeHireDate)) {
                $eligibleStartForExcel = $employeeHireDate->copy();
            }

            if ($employeeTerminationDate && $employeeTerminationDate->lt($periodEndForExcel) && $eligibleEndForExcel->gt($employeeTerminationDate)) {
                $eligibleEndForExcel = $employeeTerminationDate->copy();
            }

            $actualEligibleDays = $eligibleEndForExcel->lt($eligibleStartForExcel)
                ? 0
                : ((int) $eligibleStartForExcel->diffInDays($eligibleEndForExcel) + 1);

            if (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'working_days') {
                $payableDaysForExcel = 0;

                if ($eligibleEndForExcel->gte($eligibleStartForExcel)) {
                    $dayCursor = $eligibleStartForExcel->copy();

                    while ($dayCursor->lte($eligibleEndForExcel)) {
                        if (!$dayCursor->isFriday()) {
                            $payableDaysForExcel++;
                        }

                        $dayCursor->addDay();
                    }
                }
            } elseif (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'fixed_30_days') {
                $isFullPeriod = $eligibleStartForExcel->isSameDay($periodStartForExcel)
                    && $eligibleEndForExcel->isSameDay($periodEndForExcel);

                $payableDaysForExcel = $isFullPeriod ? 30 : min($actualEligibleDays, 30);
            } else {
                $payableDaysForExcel = $actualEligibleDays;
            }

            $eligibleDateText = $eligibleStartForExcel->format('Y-m-d') . ' إلى ' . $eligibleEndForExcel->format('Y-m-d');

            $basic = (float) ($item->basic_salary ?? 0);
            $housing = (float) ($item->housing_allowance ?? 0);
            $transport = (float) ($item->transport_allowance ?? 0);
            $food = (float) ($item->food_allowance ?? 0);
            $other = (float) ($item->other_allowance ?? 0);

            $allowancesTotal = $housing + $transport + $food + $other;
            $gross = (float) ($item->gross_salary ?? ($basic + $allowancesTotal));

            $unpaid = (float) ($item->unpaid_leave_deductions ?? 0);
            $suspension = (float) ($item->suspension_deductions ?? 0);
            $regularDeductions = (float) ($item->regular_deductions ?? 0);
            $advances = (float) ($item->salary_advance_deductions ?? 0);
            $deductions = (float) ($item->total_deductions ?? ($unpaid + $suspension + $regularDeductions + $advances));
            $net = (float) ($item->net_salary ?? 0);

            $totalBasic += $basic;
            $totalHousing += $housing;
            $totalTransport += $transport;
            $totalFood += $food;
            $totalOther += $other;
            $totalAllowances += $allowancesTotal;
            $totalGross += $gross;
            $totalUnpaid += $unpaid;
            $totalSuspension += $suspension;
            $totalRegularDeductions += $regularDeductions;
            $totalAdvances += $advances;
            $totalDeductions += $deductions;
            $totalNet += $net;
            $totalPayableDays += $payableDaysForExcel;
        @endphp

        <tr>
            <td>{{ $item->employee_number }}</td>
            <td>{{ $item->employee_name }}</td>
            <td>{{ $nationalityName }}</td>
            <td>{{ $positionName }}</td>
            <td>{{ $departmentName }}</td>
            <td>{{ $employeeStatusText }}</td>
            <td>{{ $paymentMethodName }}</td>
            <td>{{ $payrollGroupName }}</td>
            <td>{{ $costCenterName }}</td>
            <td>{{ $eligibleDateText }}</td>
            <td class="days">{{ $payableDaysForExcel }}</td>
            <td class="num">{{ number_format($basic, 2) }}</td>
            <td class="num">{{ number_format($housing, 2) }}</td>
            <td class="num">{{ number_format($transport, 2) }}</td>
            <td class="num">{{ number_format($food, 2) }}</td>
            <td class="num">{{ number_format($other, 2) }}</td>
            <td class="num">{{ number_format($allowancesTotal, 2) }}</td>
            <td class="num">{{ number_format($gross, 2) }}</td>
            <td class="num">{{ number_format($unpaid, 2) }}</td>
            <td class="num">{{ number_format($suspension, 2) }}</td>
            <td class="num">{{ number_format($regularDeductions, 2) }}</td>
            <td class="num">{{ number_format($advances, 2) }}</td>
            <td class="num">{{ number_format($deductions, 2) }}</td>
            <td class="num"><strong>{{ number_format($net, 2) }}</strong></td>
        </tr>
    @endforeach

    <tr class="total-row">
        <td colspan="10">الإجمالي</td>
        <td class="days">{{ $totalPayableDays }}</td>
        <td class="num">{{ number_format($totalBasic, 2) }}</td>
        <td class="num">{{ number_format($totalHousing, 2) }}</td>
        <td class="num">{{ number_format($totalTransport, 2) }}</td>
        <td class="num">{{ number_format($totalFood, 2) }}</td>
        <td class="num">{{ number_format($totalOther, 2) }}</td>
        <td class="num">{{ number_format($totalAllowances, 2) }}</td>
        <td class="num">{{ number_format($totalGross, 2) }}</td>
        <td class="num">{{ number_format($totalUnpaid, 2) }}</td>
        <td class="num">{{ number_format($totalSuspension, 2) }}</td>
        <td class="num">{{ number_format($totalRegularDeductions, 2) }}</td>
        <td class="num">{{ number_format($totalAdvances, 2) }}</td>
        <td class="num">{{ number_format($totalDeductions, 2) }}</td>
        <td class="num">{{ number_format($totalNet, 2) }}</td>
    </tr>
</table>
</body>
</html>
