<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f4f1fb;
            color: #111827;
            direction: rtl;
        }

        .sheet {
            background: #ffffff;
            border-radius: 22px;
            padding: 26px;
            border: 1px solid #eeeafc;
            box-shadow: 0 20px 60px rgba(76, 59, 145, .10);
        }

        .header {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            color: #ffffff;
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: 900;
        }

        .header p {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            opacity: .92;
        }

        .logo {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: 900;
            border: 1px solid rgba(255,255,255,.25);
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
            gap: 8px;
        }

        .print-btn {
            border: 0;
            background: #111827;
            color: #fff;
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .summary-card {
            background: #faf9ff;
            border: 1px solid #eeeafc;
            border-radius: 16px;
            padding: 12px;
        }

        .summary-card span {
            display: block;
            color: #6b7280;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .summary-card strong {
            display: block;
            color: #4c3b91;
            font-size: 17px;
            font-weight: 900;
            word-break: break-word;
        }

        .section-title {
            color: #4c3b91;
            font-size: 17px;
            font-weight: 900;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .filter-item {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 13px;
            padding: 10px;
            font-size: 11px;
            font-weight: 800;
            color: #9a3412;
        }

        .filter-item strong {
            display: block;
            color: #7c2d12;
            margin-bottom: 4px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #eeeafc;
            border-radius: 18px;
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
            background: #ffffff;
        }

        th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 10px;
            padding: 9px 6px;
            border: 1px solid #e5ddff;
            text-align: center;
            font-weight: 900;
            white-space: nowrap;
        }

        td {
            font-size: 10px;
            padding: 8px 6px;
            border: 1px solid #f0ecff;
            text-align: center;
            font-weight: 700;
            color: #1f2937;
            word-break: break-word;
        }

        .empty {
            padding: 28px !important;
            font-size: 14px;
            color: #6b7280;
            font-weight: 900;
        }

        .footer {
            margin-top: 18px;
            color: #6b7280;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .sheet {
                box-shadow: none;
                border: 0;
                border-radius: 0;
            }

            .toolbar {
                display: none;
            }

            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }

        @media(max-width: 900px) {
            .summary-grid,
            .filters {
                grid-template-columns: 1fr;
            }

            .header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
@php
    $generatedAt = now()->format('Y-m-d H:i');
    $filterLabels = [
        'report_type' => 'نوع التقرير',
        'payroll_period_id' => 'مسير الرواتب',
        'compare_payroll_period_id' => 'مسير المقارنة',
        'status' => 'الحالة',
        'month' => 'الشهر',
        'employee_search' => 'بحث موظف',
        'department' => 'القسم',
        'date_from' => 'من تاريخ',
        'date_to' => 'إلى تاريخ',
    ];

    $activeFilters = collect(request()->only(array_keys($filterLabels)))
        ->filter(fn($value) => filled($value));
@endphp

<div class="toolbar">
    <button type="button" class="print-btn" onclick="window.print()">
        طباعة / حفظ PDF
    </button>
</div>

<div class="sheet">
    <div class="header">
        <div>
            <h1>{{ $reportTitle }}</h1>
            <p>ENG-SHADI HR — تقرير رواتب شامل</p>
            <p>تاريخ الإنشاء: {{ $generatedAt }}</p>
        </div>

        <div class="logo">ES</div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <span>نوع التقرير</span>
            <strong>{{ $reportTitle }}</strong>
        </div>

        <div class="summary-card">
            <span>عدد المسيرات</span>
            <strong>{{ number_format($summary['periods_count'] ?? 0) }}</strong>
        </div>

        <div class="summary-card">
            <span>عدد السجلات</span>
            <strong>{{ number_format($summary['rows_count'] ?? 0) }}</strong>
        </div>

        <div class="summary-card">
            <span>عدد الموظفين / البنود</span>
            <strong>{{ number_format($summary['employees_count'] ?? 0) }}</strong>
        </div>

        <div class="summary-card">
            <span>إجمالي الرواتب</span>
            <strong>{{ $summary['gross_total'] ?? '0.00' }}</strong>
        </div>

        <div class="summary-card">
            <span>إجمالي الخصومات</span>
            <strong>{{ $summary['deductions_total'] ?? '0.00' }}</strong>
        </div>

        <div class="summary-card">
            <span>إجمالي الصافي</span>
            <strong>{{ $summary['net_total'] ?? '0.00' }}</strong>
        </div>

        <div class="summary-card">
            <span>تاريخ التقرير</span>
            <strong>{{ $generatedAt }}</strong>
        </div>
    </div>

    @if($activeFilters->isNotEmpty())
        <div class="section-title">الفلاتر المستخدمة</div>

        <div class="filters">
            @foreach($activeFilters as $key => $value)
                <div class="filter-item">
                    <strong>{{ $filterLabels[$key] ?? $key }}</strong>
                    {{ $value }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="section-title">نتائج التقرير</div>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>#</th>
                @foreach($columns as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach($columns as $key => $label)
                        <td>{{ $row[$key] ?? '-' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="empty">
                        لا توجد بيانات حسب الفلاتر المحددة.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <span>ENG-SHADI HR 2026 ©</span>
        <span>تم إنشاء التقرير بواسطة النظام</span>
    </div>
</div>

</body>
</html>
