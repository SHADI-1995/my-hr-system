<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
<h2>تقرير مسير الرواتب - {{ $payrollPeriod->period_number }} - {{ $payrollPeriod->month }}</h2>

<table>
    <thead>
    <tr>
        <th>رقم الموظف</th>
        <th>الموظف</th>
        <th>القسم</th>
        <th>أيام الاستحقاق</th>
        <th>الراتب الأساسي</th>
        <th>البدلات</th>
        <th>إجمالي الراتب</th>
        <th>إجازات غير مدفوعة</th>
        <th>إيقافات</th>
        <th>استقطاعات</th>
        <th>سلف</th>
        <th>إجمالي الخصم</th>
        <th>الصافي</th>
    </tr>
    </thead>
    <tbody>
    @foreach($payrollPeriod->items as $item)
        <tr>
            <td>{{ $item->employee_number }}</td>
            <td>{{ $item->employee_name }}</td>
            <td>{{ $item->employee?->department?->name ?? '-' }}</td>
            <td>{{ $item->payable_days ?? '-' }} / {{ $item->period_days }}</td>
            <td>{{ number_format($item->basic_salary, 2) }}</td>
            <td>{{ number_format($item->housing_allowance + $item->transport_allowance + $item->food_allowance + $item->other_allowance, 2) }}</td>
            <td>{{ number_format($item->gross_salary, 2) }}</td>
            <td>{{ number_format($item->unpaid_leave_deductions ?? 0, 2) }}</td>
            <td>{{ number_format($item->suspension_deductions, 2) }}</td>
            <td>{{ number_format($item->regular_deductions, 2) }}</td>
            <td>{{ number_format($item->salary_advance_deductions, 2) }}</td>
            <td>{{ number_format($item->total_deductions, 2) }}</td>
            <td>{{ number_format($item->net_salary, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="6">الإجمالي</th>
        <th>{{ number_format($payrollPeriod->total_gross_salary, 2) }}</th>
        <th colspan="4"></th>
        <th>{{ number_format($payrollPeriod->total_deductions, 2) }}</th>
        <th>{{ number_format($payrollPeriod->total_net_salary, 2) }}</th>
    </tr>
    </tfoot>
</table>
</body>
</html>
