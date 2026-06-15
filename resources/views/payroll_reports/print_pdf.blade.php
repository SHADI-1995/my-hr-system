<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مسير الرواتب</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #fff;
            color: #111827;
            margin: 25px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #4c3b91;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        h1 {
            color: #4c3b91;
            margin: 0 0 8px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat {
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            background: #faf9ff;
        }

        .stat small {
            display: block;
            color: #6b5aa8;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .stat strong {
            color: #3b2b80;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 7px;
            text-align: center;
        }

        th {
            background: #ede9fe;
            color: #4c3b91;
        }

        .print-btn {
            background: #4c3b91;
            color: #fff;
            border: 0;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
<div class="header">
    <div>
        <h1>تقرير مسير الرواتب</h1>
        <p>
            رقم المسير: {{ $payrollPeriod->period_number }} |
            الشهر: {{ $payrollPeriod->month }} |
            الحالة: {{ $payrollPeriod->status }}
        </p>
    </div>

    <button class="print-btn no-print" onclick="window.print()">طباعة / حفظ PDF</button>
</div>

<div class="stats">
    <div class="stat"><small>الموظفين</small><strong>{{ $payrollPeriod->employees_count }}</strong></div>
    <div class="stat"><small>إجمالي الراتب</small><strong>{{ number_format($payrollPeriod->total_gross_salary, 2) }}</strong></div>
    <div class="stat"><small>استقطاعات</small><strong>{{ number_format($payrollPeriod->total_regular_deductions, 2) }}</strong></div>
    <div class="stat"><small>سلف</small><strong>{{ number_format($payrollPeriod->total_salary_advances, 2) }}</strong></div>
    <div class="stat"><small>إيقافات/إجازات</small><strong>{{ number_format($payrollPeriod->total_suspension_deductions, 2) }}</strong></div>
    <div class="stat"><small>الصافي</small><strong>{{ number_format($payrollPeriod->total_net_salary, 2) }}</strong></div>
</div>

<table>
    <thead>
    <tr>
        <th>رقم الموظف</th>
        <th>الموظف</th>
        <th>القسم</th>
        <th>أيام الاستحقاق</th>
        <th>الإجمالي</th>
        <th>إجازات</th>
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
            <td>{{ number_format($item->gross_salary, 2) }}</td>
            <td>{{ number_format($item->unpaid_leave_deductions ?? 0, 2) }}</td>
            <td>{{ number_format($item->suspension_deductions, 2) }}</td>
            <td>{{ number_format($item->regular_deductions, 2) }}</td>
            <td>{{ number_format($item->salary_advance_deductions, 2) }}</td>
            <td>{{ number_format($item->total_deductions, 2) }}</td>
            <td><strong>{{ number_format($item->net_salary, 2) }}</strong></td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
