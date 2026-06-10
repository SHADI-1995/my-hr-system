<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #4c3b91;
            margin-bottom: 14px;
        }

        .subtitle {
            color: #555;
            margin-bottom: 18px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
        }

        th {
            background: #4c3b91;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
        }

        td {
            font-size: 11px;
            color: #111827;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            mso-number-format: "\@";
        }

        .desc {
            width: 360px;
            text-align: right;
            white-space: normal;
        }

        .plus {
            color: #15803d;
            font-weight: bold;
        }

        .minus {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="title">سجل حركات الإجازات</div>
<div class="subtitle">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</div>

<table>
    <thead>
    <tr>
        <th>رقم الحركة</th>
        <th>الموظف</th>
        <th>الرقم الوظيفي</th>
        <th>نوع الحركة</th>
        <th>الأيام</th>
        <th>الرصيد قبل</th>
        <th>الرصيد بعد</th>
        <th>الوصف</th>
        <th>تم بواسطة</th>
        <th>التاريخ</th>
    </tr>
    </thead>

    <tbody>
    @foreach($transactions as $transaction)
        @php
            $typeName = match($transaction->transaction_type) {
                'annual_accrual' => 'إضافة رصيد سنوي',
                'carry_forward' => 'ترحيل رصيد',
                'policy_recalculation' => 'إعادة احتساب سياسة',
                'paid_leave_deduction' => 'خصم إجازة مدفوعة',
                'paid_leave_reversal' => 'إرجاع رصيد',
                'unpaid_leave_record' => 'تسجيل إجازة غير مدفوعة',
                'unpaid_leave_reversal' => 'إلغاء إجازة غير مدفوعة',
                'official_leave_record' => 'تسجيل إجازة رسمية',
                'other_leave_record' => 'تسجيل إجازة أخرى',
                'other_leave_reversal' => 'إلغاء إجازة أخرى',
                default => $transaction->transaction_type ?? '-',
            };

            $employeeName = $transaction->employee->display_name
                ?? $transaction->employee->full_name
                ?? $transaction->employee->name
                ?? '-';
        @endphp

        <tr>
            <td>{{ $transaction->id }}</td>
            <td>{{ $employeeName }}</td>
            <td>{{ $transaction->employee->employee_number ?? '-' }}</td>
            <td>{{ $typeName }}</td>
            <td class="{{ (float) $transaction->days >= 0 ? 'plus' : 'minus' }}">
                {{ number_format((float) $transaction->days, 2) }}
            </td>
            <td>{{ number_format((float) $transaction->before_balance, 2) }}</td>
            <td>{{ number_format((float) $transaction->after_balance, 2) }}</td>
            <td class="desc">{{ $transaction->description ?? '-' }}</td>
            <td>{{ $transaction->createdBy->name ?? '-' }}</td>
            <td>{{ optional($transaction->created_at)->format('Y-m-d H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>

