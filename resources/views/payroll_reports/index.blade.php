@extends('layouts.hr')

@section('title', 'تقارير الرواتب')
@section('page-title', 'تقارير الرواتب')

@section('content')
    <style>
        .payroll-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        .report-hero {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            color: #fff;
            border-radius: 24px;
            padding: 26px;
            margin-bottom: 18px;
            box-shadow: 0 20px 45px rgba(76, 59, 145, .20);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }

        .report-hero h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 900;
        }

        .report-hero p {
            margin: 0;
            opacity: .9;
            font-weight: 700;
        }

        .panel {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 16px 40px rgba(76, 59, 145, .07);
        }

        .filters {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .field label {
            display: block;
            color: #4c3b91;
            font-weight: 900;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .field input,
        .field select {
            height: 42px;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            padding: 0 12px;
            font-weight: 800;
            width: 100%;
        }

        .btn2 {
            border: 0;
            border-radius: 13px;
            padding: 11px 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
        }

        .primary { background: #6d5bd0; color: #fff; }
        .green { background: #16a34a; color: #fff; }
        .soft { background: #ede9fe; color: #4c3b91; }
        .orange { background: #f59e0b; color: #fff; }

        .stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat {
            background: #f8f6ff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 15px;
            text-align: center;
        }

        .stat small {
            display: block;
            color: #6b5aa8;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .stat strong {
            font-size: 18px;
            color: #3b2b80;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            overflow: hidden;
        }

        th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 10px;
            font-weight: 900;
            padding: 9px 5px;
            text-align: center;
        }

        td {
            border-top: 1px solid #f1eefb;
            padding: 9px 5px;
            font-size: 10px;
            font-weight: 800;
            text-align: center;
            word-break: break-word;
        }

        .pill {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
        }

        .draft { background: #e5e7eb; color: #374151; }
        .calculated { background: #dbeafe; color: #1d4ed8; }
        .approved { background: #fef3c7; color: #92400e; }
        .paid { background: #dcfce7; color: #166534; }
        .cancelled { background: #fee2e2; color: #991b1b; }


        .settings-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .setting-chip {
            background: #faf9ff;
            border: 1px solid #eeeafc;
            border-radius: 16px;
            padding: 13px;
        }

        .setting-chip small {
            display: block;
            color: #6b5aa8;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .setting-chip strong {
            color: #3b2b80;
            font-size: 13px;
            font-weight: 900;
        }

        .mini-list {
            display: grid;
            gap: 5px;
            text-align: right;
        }

        .mini-list div {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            border-bottom: 1px dashed #e8e1ff;
            padding-bottom: 4px;
        }

        .mini-list div:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .mini-list span {
            color: #6b5aa8;
            font-weight: 900;
        }

        .mini-list strong {
            color: #111827;
            font-weight: 900;
            direction: ltr;
        }

        .mini-total {
            margin-top: 6px;
            background: #f1edff;
            color: #3b2b80;
            border-radius: 10px;
            padding: 6px;
            font-weight: 900;
        }

        .muted-note {
            display: block;
            margin-top: 4px;
            color: #6b7280;
            font-size: 9px;
            font-weight: 800;
        }

        .warning-note {
            display: block;
            margin-top: 4px;
            color: #b45309;
            font-size: 9px;
            font-weight: 900;
        }

        @media(max-width: 1100px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr; }
            .settings-summary { grid-template-columns: 1fr; }
        }
    </style>

    <div class="payroll-page">
        <div class="report-hero">
            <div>
                <h1>تقارير الرواتب</h1>
                <p>عرض ملخص المسيرات، تفاصيل الموظفين، التصدير إلى Excel، وطباعة القسائم.</p>
            </div>

            @if($selectedPeriod)
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @if(auth()->user()->hasPermission('payroll_reports.export'))
                        <a class="btn2 green" href="{{ route('payroll-reports.export-excel', $selectedPeriod) }}">
                            <i class="fas fa-file-excel"></i>
                            Excel
                        </a>

                        <a class="btn2 orange" href="{{ route('payroll-reports.print-pdf', $selectedPeriod) }}" target="_blank">
                            <i class="fas fa-print"></i>
                            طباعة / PDF
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="panel">
            <form method="GET" action="{{ route('payroll-reports.index') }}">
                <div class="filters">
                    <div class="field">
                        <label>الشهر</label>
                        <input type="month" name="month" value="{{ request('month') }}">
                    </div>

                    <div class="field">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">الكل</option>
                            <option value="draft" @selected(request('status')==='draft')>مسودة</option>
                            <option value="calculated" @selected(request('status')==='calculated')>محسوب</option>
                            <option value="approved" @selected(request('status')==='approved')>معتمد</option>
                            <option value="paid" @selected(request('status')==='paid')>مدفوع</option>
                        </select>
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="btn2 primary">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>
                        <a class="btn2 soft" href="{{ route('payroll-reports.index') }}">مسح</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="panel">
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
            @endphp

            <h3 style="color:#4c3b91;margin-bottom:14px;">إعدادات التقرير الحالية</h3>

            <div class="settings-summary">
                <div class="setting-chip">
                    <small>طريقة احتساب الأيام</small>
                    <strong>{{ $salaryDayCalculationName }}</strong>
                </div>

                <div class="setting-chip">
                    <small>طريقة الصرف الافتراضية</small>
                    <strong>{{ $paymentMethodName }}</strong>
                </div>

                <div class="setting-chip">
                    <small>التقريب</small>
                    <strong>{{ $payrollSetting->rounding_decimals ?? 2 }} خانات عشرية</strong>
                </div>

                <div class="setting-chip">
                    <small>الصافي السالب</small>
                    <strong>{{ ($payrollSetting->allow_negative_net_salary ?? false) ? 'مسموح' : 'غير مسموح' }}</strong>
                </div>
            </div>
        </div>

        <div class="panel">
            <h3 style="color:#4c3b91;margin-bottom:14px;">فترات مسير الرواتب</h3>

            <table>
                <thead>
                <tr>
                    <th>رقم المسير</th>
                    <th>الشهر</th>
                    <th>الموظفين</th>
                    <th>الإجمالي</th>
                    <th>الخصومات</th>
                    <th>الصافي</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
                </thead>
                <tbody>
                @forelse($periods as $period)
                    <tr>
                        <td>{{ $period->period_number }}</td>
                        <td>{{ $period->month }}</td>
                        <td>{{ $period->employees_count }}</td>
                        <td>{{ number_format($period->total_gross_salary, 2) }}</td>
                        <td>{{ number_format($period->total_deductions, 2) }}</td>
                        <td><strong>{{ number_format($period->total_net_salary, 2) }}</strong></td>
                        <td><span class="pill {{ $period->status }}">{{ $period->status }}</span></td>
                        <td>
                            <a class="btn2 primary" href="{{ route('payroll-reports.show', $period) }}">
                                عرض التقرير
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">لا توجد مسيرات رواتب.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div style="margin-top:14px">{{ $periods->links() }}</div>
        </div>

        @if($selectedPeriod)
            <div class="stats">
                <div class="stat"><small>الشهر</small><strong>{{ $selectedPeriod->month }}</strong></div>
                <div class="stat"><small>الموظفين</small><strong>{{ $selectedPeriod->employees_count }}</strong></div>
                <div class="stat"><small>إجمالي الرواتب</small><strong>{{ number_format($selectedPeriod->total_gross_salary, 2) }}</strong></div>
                <div class="stat"><small>الاستقطاعات</small><strong>{{ number_format($selectedPeriod->total_regular_deductions, 2) }}</strong></div>
                <div class="stat"><small>السلف والإيقافات</small><strong>{{ number_format($selectedPeriod->total_salary_advances + $selectedPeriod->total_suspension_deductions, 2) }}</strong></div>
                <div class="stat"><small>الصافي</small><strong>{{ number_format($selectedPeriod->total_net_salary, 2) }}</strong></div>
            </div>

            <div class="panel">
                <h3 style="color:#4c3b91;margin-bottom:14px;">
                    تفاصيل المسير: {{ $selectedPeriod->period_number }}
                </h3>

                <table>
                    <thead>
                    <tr>
                        <th style="width:11%">الموظف</th>
                        <th>القسم</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>أيام الاستحقاق</th>
                        <th>الراتب الأساسي</th>
                        <th style="width:14%">تفاصيل البدلات</th>
                        <th>إجمالي البدلات</th>
                        <th>إجمالي الراتب</th>
                        <th style="width:15%">تفاصيل الاستقطاعات</th>
                        <th>إجمالي الاستقطاعات</th>
                        <th>الصافي</th>
                        <th>قسيمة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($selectedPeriod->items as $item)
                        @php
                            /*
                             * تصحيح تاريخ وأيام الاستحقاق في صفحة التقرير:
                             * نحسبها للعرض من تاريخ المسير + تاريخ مباشرة الموظف + تاريخ نهاية الخدمة.
                             * هذا يمنع ظهور تواريخ أو أيام قديمة إذا كانت payroll_items محفوظة بقيم غير محدثة.
                             */
                            $periodStart = $selectedPeriod->start_date
                                ? \Carbon\Carbon::parse($selectedPeriod->start_date)->startOfDay()
                                : null;

                            $periodEnd = $selectedPeriod->end_date
                                ? \Carbon\Carbon::parse($selectedPeriod->end_date)->startOfDay()
                                : null;

                            $employeeHireDate = $item->employee?->hire_date
                                ? \Carbon\Carbon::parse($item->employee->hire_date)->startOfDay()
                                : null;

                            $employeeTerminationDate = $item->employee?->termination_date
                                ? \Carbon\Carbon::parse($item->employee->termination_date)->startOfDay()
                                : null;

                            $eligibleStart = $item->eligible_start_date
                                ? \Carbon\Carbon::parse($item->eligible_start_date)->startOfDay()
                                : ($employeeHireDate && $periodStart && $employeeHireDate->gt($periodStart) ? $employeeHireDate->copy() : ($periodStart?->copy()));

                            $eligibleEnd = $item->eligible_end_date
                                ? \Carbon\Carbon::parse($item->eligible_end_date)->startOfDay()
                                : ($employeeTerminationDate && $periodEnd && $employeeTerminationDate->lt($periodEnd) ? $employeeTerminationDate->copy() : ($periodEnd?->copy()));

                            if ($employeeHireDate && $periodStart && $eligibleStart && $employeeHireDate->gt($periodStart) && $eligibleStart->lt($employeeHireDate)) {
                                $eligibleStart = $employeeHireDate->copy();
                            }

                            if ($employeeTerminationDate && $periodEnd && $eligibleEnd && $employeeTerminationDate->lt($periodEnd) && $eligibleEnd->gt($employeeTerminationDate)) {
                                $eligibleEnd = $employeeTerminationDate->copy();
                            }

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

                                $displayPeriodDays = (int) ($item->period_days ?: $displayPayableDays);
                            } elseif (($payrollSetting->salary_day_calculation ?? 'fixed_30_days') === 'fixed_30_days') {
                                $isFullPeriod = $periodStart && $periodEnd && $eligibleStart && $eligibleEnd
                                    && $eligibleStart->isSameDay($periodStart)
                                    && $eligibleEnd->isSameDay($periodEnd);

                                $displayPayableDays = $isFullPeriod ? 30 : min($actualEligibleDays, 30);
                                $displayPeriodDays = 30;
                            } else {
                                $displayPayableDays = $actualEligibleDays;
                                $displayPeriodDays = (int) ($item->period_days ?: ($periodStart && $periodEnd ? ((int) $periodStart->diffInDays($periodEnd) + 1) : 0));
                            }

                            $storedPayableDays = (int) ($item->payable_days ?? 0);
                            $needsRecalculateWarning = $storedPayableDays !== $displayPayableDays;

                            $eligibleStartText = $eligibleStart ? $eligibleStart->format('Y-m-d') : '-';
                            $eligibleEndText = $eligibleEnd ? $eligibleEnd->format('Y-m-d') : '-';

                            $housing = (float) ($item->housing_allowance ?? 0);
                            $transport = (float) ($item->transport_allowance ?? 0);
                            $food = (float) ($item->food_allowance ?? 0);
                            $other = (float) ($item->other_allowance ?? 0);
                            $totalAllowances = $housing + $transport + $food + $other;

                            $unpaid = (float) ($item->unpaid_leave_deductions ?? 0);
                            $suspension = (float) ($item->suspension_deductions ?? 0);
                            $regular = (float) ($item->regular_deductions ?? 0);
                            $advance = (float) ($item->salary_advance_deductions ?? 0);
                            $totalDeductions = (float) ($item->total_deductions ?? ($unpaid + $suspension + $regular + $advance));
                        @endphp

                        <tr>
                            <td>{{ $item->employee_name }}<br><small>{{ $item->employee_number }}</small></td>
                            <td>{{ $item->employee?->department?->name ?? '-' }}</td>
                            <td>
                                {{ $eligibleStartText }}
                                <br>
                                <small>إلى {{ $eligibleEndText }}</small>
                            </td>
                            <td>
                                <strong>{{ $displayPayableDays }}</strong>
                                <span class="muted-note">من أصل {{ $displayPeriodDays }} يوم</span>

                                @if($needsRecalculateWarning)
                                    <span class="warning-note">
                                    </span>
                                @endif
                            </td>
                            <td>{{ number_format($item->basic_salary ?? 0, 2) }}</td>

                            <td>
                                <div class="mini-list">
                                    <div><span>سكن</span><strong>{{ number_format($housing, 2) }}</strong></div>
                                    <div><span>نقل</span><strong>{{ number_format($transport, 2) }}</strong></div>
                                    <div><span>طعام</span><strong>{{ number_format($food, 2) }}</strong></div>
                                    <div><span>أخرى</span><strong>{{ number_format($other, 2) }}</strong></div>
                                </div>
                            </td>

                            <td>
                                <div class="mini-total">{{ number_format($totalAllowances, 2) }}</div>
                            </td>

                            <td>{{ number_format($item->gross_salary, 2) }}</td>

                            <td>
                                <div class="mini-list">
                                    <div><span>إجازات</span><strong>{{ number_format($unpaid, 2) }}</strong></div>
                                    <div><span>إيقاف</span><strong>{{ number_format($suspension, 2) }}</strong></div>
                                    <div><span>استقطاع</span><strong>{{ number_format($regular, 2) }}</strong></div>
                                    <div><span>سلف</span><strong>{{ number_format($advance, 2) }}</strong></div>
                                </div>
                            </td>

                            <td>
                                <div class="mini-total">{{ number_format($totalDeductions, 2) }}</div>
                            </td>

                            <td><strong>{{ number_format($item->net_salary, 2) }}</strong></td>
                            <td>
                                @if(auth()->user()->hasPermission('payroll_reports.payslip'))
                                    <a class="btn2 soft" target="_blank" href="{{ route('payroll-reports.payslip', $item) }}">
                                        قسيمة
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12">لا توجد تفاصيل لهذا المسير.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
