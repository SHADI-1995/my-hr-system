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

        @media(max-width: 1100px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr; }
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
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>أيام الاستحقاق</th>
                        <th>الإجمالي</th>
                        <th>إجازات</th>
                        <th>إيقاف</th>
                        <th>استقطاعات</th>
                        <th>سلف</th>
                        <th>الصافي</th>
                        <th>قسيمة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($selectedPeriod->items as $item)
                        <tr>
                            <td>{{ $item->employee_name }}<br><small>{{ $item->employee_number }}</small></td>
                            <td>{{ $item->employee?->department?->name ?? '-' }}</td>
                            <td>{{ $item->payable_days ?? '-' }} / {{ $item->period_days }}</td>
                            <td>{{ number_format($item->gross_salary, 2) }}</td>
                            <td>{{ number_format($item->unpaid_leave_deductions ?? 0, 2) }}</td>
                            <td>{{ number_format($item->suspension_deductions, 2) }}</td>
                            <td>{{ number_format($item->regular_deductions, 2) }}</td>
                            <td>{{ number_format($item->salary_advance_deductions, 2) }}</td>
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
                            <td colspan="10">لا توجد تفاصيل لهذا المسير.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
