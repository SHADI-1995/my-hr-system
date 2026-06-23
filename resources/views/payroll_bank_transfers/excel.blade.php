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
        <th colspan="9">كشف تحويل الرواتب - {{ $payrollPeriod->period_number }} - {{ $payrollPeriod->month }}</th>
    </tr>
    <tr>
        <th>#</th>
        <th>الرقم الوظيفي</th>
        <th>الموظف</th>
        <th>القسم</th>
        <th>البنك</th>
        <th>IBAN</th>
        <th>طريقة الصرف</th>
        <th>صافي الراتب</th>
        <th>التحقق</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $index => $item)
        @php
            $employee = $item->employee;
            $bankName = $employee?->bank_name ?: '-';
            $iban = $employee?->iban ?: '-';
            $method = $item->salary_payment_method_name
                ?? $employee?->salaryPaymentMethod?->name_ar
                ?? $employee?->salary_payment_method
                ?? '-';
            $isReady = !empty($employee?->bank_name) && !empty($employee?->iban);
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->employee_number }}</td>
            <td>{{ $item->employee_name }}</td>
            <td>{{ $item->employee_department ?? $employee?->department?->name ?? '-' }}</td>
            <td>{{ $bankName }}</td>
            <td>{{ $iban }}</td>
            <td>{{ $method }}</td>
            <td>{{ number_format((float) $item->net_salary, 2) }}</td>
            <td>{{ $isReady ? 'جاهز' : 'ناقص بيانات' }}</td>
        </tr>
    @endforeach
    <tr>
        <th colspan="7">الإجمالي</th>
        <th>{{ number_format((float) $items->sum('net_salary'), 2) }}</th>
        <th></th>
    </tr>
    </tbody>
</table>
</body>
</html>
