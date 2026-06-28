@php
    echo "\xEF\xBB\xBF";

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

    $numericColumnKeywords = [
        'salary',
        'gross',
        'net',
        'deduction',
        'deductions',
        'amount',
        'total',
        'count',
        'employees_count',
        'periods_count',
        'rows_count',
        'days',
        'average',
        'basic',
        'allowances',
        'advances',
        'suspension',
        'صافي',
        'إجمالي',
        'الراتب',
        'الرواتب',
        'الخصومات',
        'المبلغ',
        'عدد',
        'الأيام',
        'متوسط',
        'السلف',
        'البدلات',
        'الإيقافات',
    ];

    $isNumericColumn = function ($key, $label) use ($numericColumnKeywords) {
        $text = mb_strtolower((string) $key . ' ' . (string) $label);

        foreach ($numericColumnKeywords as $keyword) {
            if (str_contains($text, mb_strtolower($keyword))) {
                return true;
            }
        }

        return false;
    };

    $cleanNumber = function ($value) {
        if ($value === null || $value === '' || $value === '-') {
            return '';
        }

        $clean = str_replace([',', 'SAR', 'ريال', 'ر.س', ' '], '', (string) $value);

        return is_numeric($clean) ? $clean : $value;
    };
@endphp
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .title {
            background: #4c3b91;
            color: #ffffff;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .subtitle {
            background: #ede9fe;
            color: #4c3b91;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .summary-title {
            background: #f1edff;
            color: #4c3b91;
            font-weight: bold;
            text-align: center;
        }

        th {
            background: #4c3b91;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #d8d2f0;
            text-align: center;
            white-space: nowrap;
        }

        td {
            border: 1px solid #d8d2f0;
            text-align: center;
            vertical-align: middle;
        }

        .number-cell {
            mso-number-format: "#,##0.00";
        }

        .integer-cell {
            mso-number-format: "0";
        }

        .text-cell {
            mso-number-format: "\@";
        }

        .note {
            background: #fff7ed;
            color: #9a3412;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

<table>
    <tr>
        <td colspan="{{ max(count($columns) + 1, 7) }}" class="title">
            ENG-SHADI HR - {{ $reportTitle }}
        </td>
    </tr>

    <tr>
        <td colspan="{{ max(count($columns) + 1, 7) }}" class="subtitle">
            تاريخ التصدير: {{ $generatedAt }}
        </td>
    </tr>

    <tr>
        <td colspan="{{ max(count($columns) + 1, 7) }}"></td>
    </tr>
</table>

<table>
    <tr>
        <td class="summary-title">نوع التقرير</td>
        <td>{{ $reportTitle }}</td>
        <td class="summary-title">عدد المسيرات</td>
        <td class="integer-cell">{{ $cleanNumber($summary['periods_count'] ?? 0) }}</td>
        <td class="summary-title">عدد السجلات</td>
        <td class="integer-cell">{{ $cleanNumber($summary['rows_count'] ?? 0) }}</td>
    </tr>

    <tr>
        <td class="summary-title">إجمالي الصافي</td>
        <td class="number-cell">{{ $cleanNumber($summary['net_total'] ?? 0) }}</td>
        <td class="summary-title">إجمالي الخصومات</td>
        <td class="number-cell">{{ $cleanNumber($summary['deductions_total'] ?? 0) }}</td>
        <td class="summary-title">إجمالي الرواتب</td>
        <td class="number-cell">{{ $cleanNumber($summary['gross_total'] ?? 0) }}</td>
    </tr>
</table>

@if($activeFilters->isNotEmpty())
    <br>

    <table>
        <tr>
            <td colspan="2" class="subtitle">الفلاتر المستخدمة</td>
        </tr>

        @foreach($activeFilters as $key => $value)
            <tr>
                <td class="summary-title">{{ $filterLabels[$key] ?? $key }}</td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>
@endif

<br>

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
            <td class="integer-cell">{{ $index + 1 }}</td>

            @foreach($columns as $key => $label)
                @php
                    $value = $row[$key] ?? '-';
                    $numericColumn = $isNumericColumn($key, $label);
                    $cleanedValue = $numericColumn ? $cleanNumber($value) : $value;
                    $isIntegerColumn = str_contains(mb_strtolower((string) $key . ' ' . (string) $label), 'count')
                        || str_contains((string) $label, 'عدد');
                @endphp

                @if($numericColumn && is_numeric($cleanedValue))
                    <td class="{{ $isIntegerColumn ? 'integer-cell' : 'number-cell' }}">{{ $cleanedValue }}</td>
                @else
                    <td class="text-cell">{{ $value }}</td>
                @endif
            @endforeach
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($columns) + 1 }}" class="note">
                لا توجد بيانات حسب الفلاتر المحددة.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
