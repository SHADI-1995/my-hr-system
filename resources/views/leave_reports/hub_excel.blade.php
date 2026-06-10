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
            font-size: 20px;
            font-weight: bold;
            color: #4c3b91;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 13px;
            color: #555;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #4c3b91;
            color: #ffffff;
            font-weight: bold;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
            mso-number-format: "\@";
        }
    </style>
</head>
<body>

<div class="title">{{ $report['title'] ?? 'تقرير إدارة الإجازات' }}</div>
<div class="subtitle">{{ $report['description'] ?? '' }}</div>
<div class="subtitle">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</div>

<table>
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{ $header }}</th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @forelse($rows as $row)
        <tr>
            @foreach($row as $cell)
                <td>{{ $cell }}</td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($headers) }}">لا توجد بيانات</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
