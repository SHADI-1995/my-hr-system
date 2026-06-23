<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body{font-family:Tahoma,Arial,sans-serif;direction:rtl}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #999;padding:8px;text-align:center;font-size:12px}
        th{background:#ddd;font-weight:bold}
    </style>
</head>
<body>
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
        <th>الوصف</th>
        <th>عدد الموظفين</th>
        <th>إجمالي الرواتب</th>
        <th>إجمالي الخصومات</th>
        <th>صافي الرواتب</th>
    </tr>
    </thead>
    <tbody>
    @foreach($logs as $log)
        @php
            $meta = is_array($log->meta) ? $log->meta : [];
        @endphp
        <tr>
            <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $log->action_text ?? ($actions[$log->action] ?? $log->action) }}</td>
            <td>{{ $log->payrollPeriod?->period_number ?? '-' }}</td>
            <td>{{ $log->payrollPeriod?->month ?? '-' }}</td>
            <td>{{ $log->status_from ?: '-' }}</td>
            <td>{{ $log->status_to ?: '-' }}</td>
            <td>{{ $log->user?->name ?? '-' }}</td>
            <td>{{ $log->description ?: '-' }}</td>
            <td>{{ $meta['employees_count'] ?? '-' }}</td>
            <td>{{ isset($meta['total_gross_salary']) ? number_format((float) $meta['total_gross_salary'], 2) : '-' }}</td>
            <td>{{ isset($meta['total_deductions']) ? number_format((float) $meta['total_deductions'], 2) : '-' }}</td>
            <td>{{ isset($meta['total_net_salary']) ? number_format((float) $meta['total_net_salary'], 2) : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
