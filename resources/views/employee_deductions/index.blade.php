@extends('layouts.hr')

@section('title', 'الاستقطاعات')
@section('page-title', 'الاستقطاعات')

@section('content')
    <style>
        .payroll-page{max-width:100%;overflow-x:hidden}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20)}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .filters{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end}.field label{display:block;color:#4c3b91;font-weight:900;font-size:12px;margin-bottom:7px}.field input,.field select{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-weight:800}
        .btn2{border:0;border-radius:13px;padding:11px 14px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.red{background:#dc2626;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}
        .pill{display:inline-flex;padding:4px 7px;border-radius:999px;font-size:9px;font-weight:900}.pending{background:#fef3c7;color:#92400e}.approved{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}.completed{background:#dbeafe;color:#1d4ed8}
        @media(max-width:900px){.filters{grid-template-columns:1fr}}
    </style>

    <div class="payroll-page">
        <div class="hero">
            <h1>الاستقطاعات</h1>
            <p>إدارة الخصومات العامة غير السلف، مثل الغياب، التأخير، العهد، والتأمين.</p>
        </div>

        @if(session('success'))<div class="card" style="background:#ecfdf5;color:#166534">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="card" style="background:#fef2f2;color:#991b1b">{{ session('error') }}</div>@endif

        <div class="card">
            <form method="GET" action="{{ route('employee-deductions.index') }}">
                <div class="filters">
                    <div class="field"><label>بحث</label><input name="search" value="{{ request('search') }}" placeholder="اسم الموظف أو الرقم الوظيفي"></div>
                    <div class="field"><label>الحالة</label><select name="status"><option value="">الكل</option><option value="pending" @selected(request('status')==='pending')>بانتظار الاعتماد</option><option value="approved" @selected(request('status')==='approved')>معتمد</option><option value="cancelled" @selected(request('status')==='cancelled')>ملغي</option><option value="completed" @selected(request('status')==='completed')>مكتمل</option></select></div>
                    <div class="field"><label>نوع الخصم</label><input name="deduction_type" value="{{ request('deduction_type') }}" placeholder="مثال: تأخير"></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="btn2 primary">بحث</button>
                        <a class="btn2 soft" href="{{ route('employee-deductions.index') }}">مسح</a>
                        <a class="btn2 green" href="{{ route('employee-deductions.create') }}">إضافة</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead><tr><th>#</th><th>الموظف</th><th>نوع الخصم</th><th>المبلغ</th><th>طريقة الخصم</th><th>البداية</th><th>النهاية</th><th>الحالة</th><th>السبب</th><th>إجراء</th></tr></thead>
                <tbody>
                @forelse($deductions as $deduction)
                    <tr>
                        <td>{{ $deduction->deduction_number }}</td>
                        <td>{{ $deduction->employee->display_name ?? '-' }}<br><small>{{ $deduction->employee->employee_number ?? '-' }}</small></td>
                        <td>{{ $deduction->deduction_type }}</td>
                        <td>{{ number_format($deduction->amount, 2) }}</td>
                        <td>{{ $deduction->deduction_mode }}</td>
                        <td>{{ optional($deduction->start_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($deduction->end_date)->format('Y-m-d') ?? '-' }}</td>
                        <td><span class="pill {{ $deduction->status }}">{{ $deduction->status }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($deduction->reason ?? '-', 35) }}</td>
                        <td>
                            @if($deduction->status === 'pending')
                                <form method="POST" action="{{ route('employee-deductions.approve', $deduction) }}" style="display:inline">@csrf <button class="btn2 green" style="padding:7px 9px">اعتماد</button></form>
                            @endif
                            @if(in_array($deduction->status, ['pending','approved']))
                                <form method="POST" action="{{ route('employee-deductions.cancel', $deduction) }}" style="display:inline">@csrf <button class="btn2 red" style="padding:7px 9px">إلغاء</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10">لا توجد استقطاعات.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div style="margin-top:14px">{{ $deductions->links() }}</div>
        </div>
    </div>
@endsection
