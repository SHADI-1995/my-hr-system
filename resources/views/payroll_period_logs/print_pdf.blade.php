<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>طباعة سجل حركات مسير الرواتب</title>
    <style>
        *{box-sizing:border-box}
        body{font-family:Tahoma,Arial,sans-serif;direction:rtl;color:#111827;margin:24px}
        .header{text-align:center;margin-bottom:20px}
        .header h1{margin:0 0 8px;color:#4c3b91;font-size:22px}
        .header p{margin:0;color:#6b7280;font-size:12px}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #d1d5db;padding:7px;text-align:center;font-size:10px;vertical-align:middle}
        th{background:#ede9fe;color:#4c3b91;font-weight:900}
        .desc{text-align:right;line-height:1.6}
        @media print{
            @page{size:A4 landscape;margin:10mm}
            body{margin:0}
            .no-print{display:none}
        }
        .no-print{margin-bottom:16px;text-align:left}
        button{background:#4c3b91;color:white;border:0;border-radius:10px;padding:10px 16px;font-weight:900;cursor:pointer}
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">طباعة / حفظ PDF</button>
</div>

<div class="header">
    <h1>سجل حركات مسير الرواتب</h1>
    <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>التاريخ</th>
        <th>العملية</th>
        <th>رقم المسير</th>
        <th>الشهر</th>
        <th>من حالة</th>
        <th>إلى حالة</th>
        <th>المستخدم</th>
        <th>الوصف / التفاصيل</th>
    </tr>
    </thead>
    <tbody>
    @forelse($logs as $log)
        @php
            $meta = is_array($log->meta) ? $log->meta : [];
        @endphp
        <tr>
            <td dir="ltr">{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $log->action_text ?? ($actions[$log->action] ?? $log->action) }}</td>
            <td>{{ $log->payrollPeriod?->period_number ?? '-' }}</td>
            <td>{{ $log->payrollPeriod?->month ?? '-' }}</td>
            <td>{{ $log->status_from ?: '-' }}</td>
            <td>{{ $log->status_to ?: '-' }}</td>
            <td>{{ $log->user?->name ?? '-' }}</td>
            <td class="desc">
                {{ $log->description ?: '-' }}
                @if(!empty($meta))
                    <br>
                    الموظفين: {{ $meta['employees_count'] ?? '-' }}
                    |
                    الإجمالي: {{ isset($meta['total_gross_salary']) ? number_format((float) $meta['total_gross_salary'], 2) : '-' }}
                    |
                    الخصومات: {{ isset($meta['total_deductions']) ? number_format((float) $meta['total_deductions'], 2) : '-' }}
                    |
                    الصافي: {{ isset($meta['total_net_salary']) ? number_format((float) $meta['total_net_salary'], 2) : '-' }}
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8">لا توجد حركات للطباعة.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>

