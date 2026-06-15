@extends('layouts.hr')

@section('title', 'مسير الرواتب')
@section('page-title', 'مسير الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20)}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .filters{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end}.field label{display:block;color:#4c3b91;font-weight:900;font-size:12px;margin-bottom:7px}.field input,.field select{height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-weight:800}
        .btn2{border:0;border-radius:13px;padding:11px 14px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.red{background:#dc2626;color:#fff}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:11px;font-weight:900;padding:10px 6px;text-align:center}td{border-top:1px solid #f1eefb;padding:10px 6px;font-size:11px;font-weight:800;text-align:center;word-break:break-word}
        .pill{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:900}.draft{background:#e5e7eb;color:#374151}.calculated{background:#dbeafe;color:#1d4ed8}.approved{background:#fef3c7;color:#92400e}.paid{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}
        @media(max-width:900px){.filters{grid-template-columns:1fr}}
    </style>

    <div class="page">
        <div class="hero">
            <h1>مسير الرواتب</h1>
            <p>إنشاء فترة راتب، احتساب الرواتب، خصم السلف والاستقطاعات والإيقافات.</p>
        </div>

        <div class="card">
            <form method="GET" action="{{ route('payroll-periods.index') }}">
                <div class="filters">
                    <div class="field">
                        <label>الشهر</label>
                        <input type="month" name="month" value="{{ request('month') }}">
                    </div>
                    <div class="field">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">الكل</option>
                            <option value="draft" @selected(request('status')==='draft')>مسودة</option>
                            <option value="calculated" @selected(request('status')==='calculated')>محسوب</option>
                            <option value="approved" @selected(request('status')==='approved')>معتمد</option>
                            <option value="paid" @selected(request('status')==='paid')>مدفوع</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="btn2 primary"><i class="fas fa-search"></i> بحث</button>
                        <a class="btn2 soft" href="{{ route('payroll-periods.index') }}">مسح</a>
                        @if(auth()->user()->hasPermission('payrolls.create'))
                            <a class="btn2 green" href="{{ route('payroll-periods.create') }}"><i class="fas fa-plus"></i> إنشاء مسير</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                <tr>
                    <th>رقم المسير</th>
                    <th>الشهر</th>
                    <th>الفترة</th>
                    <th>الموظفين</th>
                    <th>إجمالي الرواتب</th>
                    <th>إجمالي الخصومات</th>
                    <th>الصافي</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
                </thead>
                <tbody>
                @forelse($periods as $period)
                    <tr>
                        <td>{{ $period->period_number }}</td>
                        <td>{{ $period->month }}</td>
                        <td>{{ optional($period->start_date)->format('Y-m-d') }}<br>{{ optional($period->end_date)->format('Y-m-d') }}</td>
                        <td>{{ $period->employees_count }}</td>
                        <td>{{ number_format($period->total_gross_salary, 2) }}</td>
                        <td>{{ number_format($period->total_deductions, 2) }}</td>
                        <td>{{ number_format($period->total_net_salary, 2) }}</td>
                        <td><span class="pill {{ $period->status }}">{{ $period->status }}</span></td>
                        <td>
                            <a class="btn2 primary" href="{{ route('payroll-periods.show', $period) }}">فتح</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">لا توجد فترات مسير رواتب.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div style="margin-top:14px">{{ $periods->links() }}</div>
        </div>
    </div>
@endsection
