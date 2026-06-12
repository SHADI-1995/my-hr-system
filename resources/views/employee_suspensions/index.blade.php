@extends('layouts.hr')

@section('title', 'إيقافات الموظفين')
@section('page-title', 'إيقافات الموظفين')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}.hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20)}.hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}.filters{display:grid;grid-template-columns:2fr 1fr auto;gap:12px;align-items:end}.field label{display:block;color:#4c3b91;font-weight:900;font-size:12px;margin-bottom:7px}.field input,.field select{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-weight:800}
        .btn2{border:0;border-radius:13px;padding:11px 14px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.red{background:#dc2626;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}
        .pill{display:inline-flex;padding:4px 7px;border-radius:999px;font-size:9px;font-weight:900}.active{background:#fee2e2;color:#991b1b}.resumed{background:#dcfce7;color:#166534}.cancelled{background:#e5e7eb;color:#374151}@media(max-width:900px){.filters{grid-template-columns:1fr}}
    </style>
    <div class="page">
        <div class="hero"><h1>إيقافات الموظفين</h1><p>إدارة إيقاف الموظف واستئناف العمل وتأثير الإيقاف على الراتب.</p></div>
        @if(session('success'))<div class="card" style="background:#ecfdf5;color:#166534">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="card" style="background:#fef2f2;color:#991b1b">{{ session('error') }}</div>@endif
        <div class="card">
            <form method="GET" action="{{ route('employee-suspensions.index') }}"><div class="filters">
                    <div class="field"><label>بحث</label><input name="search" value="{{ request('search') }}" placeholder="اسم الموظف أو الرقم الوظيفي"></div>
                    <div class="field"><label>الحالة</label><select name="status"><option value="">الكل</option><option value="active" @selected(request('status')==='active')>نشط</option><option value="resumed" @selected(request('status')==='resumed')>تم الاستئناف</option><option value="cancelled" @selected(request('status')==='cancelled')>ملغي</option></select></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn2 primary">بحث</button><a class="btn2 soft" href="{{ route('employee-suspensions.index') }}">مسح</a><a class="btn2 green" href="{{ route('employee-suspensions.create') }}">إيقاف موظف</a></div>
                </div></form>
        </div>
        <div class="card">
            <table><thead><tr><th>الموظف</th><th>بداية الإيقاف</th><th>تاريخ العودة</th><th>أيام الإيقاف</th><th>نسبة الراتب</th><th>الحالة</th><th>السبب</th><th>إجراء</th></tr></thead><tbody>
                @forelse($suspensions as $suspension)
                    <tr>
                        <td>{{ $suspension->employee->display_name ?? '-' }}<br><small>{{ $suspension->employee->employee_number ?? '-' }}</small></td>
                        <td>{{ optional($suspension->start_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($suspension->resume_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $suspension->suspension_days }}</td>
                        <td>{{ number_format($suspension->salary_percentage, 2) }}%</td>
                        <td><span class="pill {{ $suspension->status }}">{{ $suspension->status }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($suspension->reason ?? '-', 35) }}</td>
                        <td>
                            @if($suspension->status === 'active')
                                <form method="POST" action="{{ route('employee-suspensions.resume', $suspension) }}" style="display:inline">@csrf <input type="date" name="resume_date" required style="width:120px;border:1px solid #ddd6fe;border-radius:8px;padding:5px"><button class="btn2 green" style="padding:7px 9px">استئناف</button></form>
                                <form method="POST" action="{{ route('employee-suspensions.cancel', $suspension) }}" style="display:inline">@csrf <button class="btn2 red" style="padding:7px 9px">إلغاء</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">لا توجد إيقافات.</td></tr>
                @endforelse
                </tbody></table>
            <div style="margin-top:14px">{{ $suspensions->links() }}</div>
        </div>
    </div>
@endsection
