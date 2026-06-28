<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"><title>طباعة دفعة تحويل الرواتب</title>
    <style>*{box-sizing:border-box}body{font-family:Tahoma,Arial,sans-serif;direction:rtl;color:#111827;margin:24px}.header{text-align:center;margin-bottom:20px}.header h1{margin:0 0 8px;color:#4c3b91;font-size:22px}.header p{margin:0;color:#6b7280;font-size:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #d1d5db;padding:7px;text-align:center;font-size:10px}th{background:#ede9fe;color:#4c3b91;font-weight:900}.amount{direction:ltr;font-weight:900}.no-print{margin-bottom:16px;text-align:left}button{background:#4c3b91;color:white;border:0;border-radius:10px;padding:10px 16px;font-weight:900;cursor:pointer}@media print{.no-print{display:none}@page{size:A4 landscape;margin:10mm}body{margin:0}}</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()">طباعة / حفظ PDF</button></div>
<div class="header"><h1>دفعة تحويل الرواتب</h1><p>الدفعة: {{ $batch->batch_number }} | المسير: {{ $batch->payrollPeriod?->period_number }} | الشهر: {{ $batch->payrollPeriod?->month }}</p></div>
<table>
    <thead><tr><th>#</th><th>الرقم الوظيفي</th><th>الموظف</th><th>القسم</th><th>البنك</th><th>IBAN</th><th>طريقة الصرف</th><th>صافي الراتب</th><th>التحقق</th></tr></thead>
    <tbody>
    @foreach($items as $index => $item)
        @php
            $employee = $item->employee;
            $bankName = $employee?->bank_name ?: '-';
            $iban = $employee?->iban ?: '-';
            $method = $item->salary_payment_method_name ?? $employee?->salaryPaymentMethod?->name_ar ?? $employee?->salary_payment_method ?? '-';
            $isReady = !empty($employee?->bank_name) && !empty($employee?->iban);
        @endphp
        <tr><td>{{ $index + 1 }}</td><td>{{ $item->employee_number }}</td><td>{{ $item->employee_name }}</td><td>{{ $item->employee_department ?? $employee?->department?->name ?? '-' }}</td><td>{{ $bankName }}</td><td dir="ltr">{{ $iban }}</td><td>{{ $method }}</td><td class="amount">{{ number_format((float) $item->net_salary, 2) }}</td><td>{{ $isReady ? 'جاهز' : 'ناقص بيانات' }}</td></tr>
    @endforeach
    <tr><th colspan="7">الإجمالي</th><th>{{ number_format((float) $items->sum('net_salary'), 2) }}</th><th></th></tr>
    </tbody>
</table>
</body>
</html>
