<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قسيمة راتب</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f4f1fb;
            color: #111827;
            margin: 0;
            padding: 25px;
        }

        .slip {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #e9d5ff;
            box-shadow: 0 20px 45px rgba(76, 59, 145, .15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #4c3b91;
            padding-bottom: 15px;
            margin-bottom: 20px;
            gap: 15px;
        }

        h1 {
            color: #4c3b91;
            margin: 0 0 8px;
        }

        .info-grid,
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .box {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 12px;
            background: #faf9ff;
        }

        .box small {
            display: block;
            color: #6b5aa8;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .box strong {
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #ede9fe;
            color: #4c3b91;
        }

        .net {
            background: #dcfce7;
            color: #166534;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            border-radius: 14px;
            padding: 16px;
            margin-top: 20px;
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
            body { background: #fff; padding: 0; }
            .slip { box-shadow: none; border: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="slip">
    <div class="header">
        <div>
            <h1>قسيمة راتب</h1>
            <p>
                مسير: {{ $payrollItem->payrollPeriod?->period_number }} |
                شهر: {{ $payrollItem->payrollPeriod?->month }}
            </p>
        </div>

        <button class="print-btn no-print" onclick="window.print()">طباعة</button>
    </div>

    <div class="info-grid">
        <div class="box">
            <small>رقم الموظف</small>
            <strong>{{ $payrollItem->employee_number }}</strong>
        </div>
        <div class="box">
            <small>اسم الموظف</small>
            <strong>{{ $payrollItem->employee_name }}</strong>
        </div>
        <div class="box">
            <small>القسم</small>
            <strong>{{ $payrollItem->employee?->department?->name ?? '-' }}</strong>
        </div>
        <div class="box">
            <small>الوظيفة</small>
            <strong>{{ $payrollItem->employee?->position?->title ?? '-' }}</strong>
        </div>
        <div class="box">
            <small>أيام الاستحقاق</small>
            <strong>{{ $payrollItem->payable_days ?? '-' }} / {{ $payrollItem->period_days }}</strong>
        </div>
        <div class="box">
            <small>حالة الموظف في المسير</small>
            <strong>{{ $payrollItem->employment_status_note ?? '-' }}</strong>
        </div>
    </div>

    <div class="summary-grid">
        <div class="box">
            <small>إجمالي المستحقات</small>
            <strong>{{ number_format($payrollItem->gross_salary, 2) }}</strong>
        </div>
        <div class="box">
            <small>إجمالي الخصومات</small>
            <strong>{{ number_format($payrollItem->total_deductions, 2) }}</strong>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>البند</th>
            <th>النوع</th>
            <th>المبلغ</th>
            <th>ملاحظات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($payrollItem->components as $component)
            <tr>
                <td>{{ $component->name }}</td>
                <td>{{ $component->type }}</td>
                <td>{{ number_format($component->amount, 2) }}</td>
                <td>{{ $component->notes ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">لا توجد مكونات لهذا الراتب.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="net">
        صافي الراتب: {{ number_format($payrollItem->net_salary, 2) }}
    </div>
</div>
</body>
</html>

