@extends('layouts.hr')

@section('title', 'سلف الموظفين')
@section('page-title', 'سلف الموظفين')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}.hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20)}.hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}.filters{display:grid;grid-template-columns:2fr 1fr auto;gap:12px;align-items:end}.field label{display:block;color:#4c3b91;font-weight:900;font-size:12px;margin-bottom:7px}.field input,.field select{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-weight:800}
        .btn2{border:0;border-radius:13px;padding:10px 13px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.red{background:#dc2626;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}.pill{display:inline-flex;padding:4px 7px;border-radius:999px;font-size:9px;font-weight:900}.pending{background:#fef3c7;color:#92400e}.approved{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}.completed{background:#dbeafe;color:#1d4ed8}@media(max-width:900px){.filters{grid-template-columns:1fr}}
    </style>

    <div class="page">
        <div class="hero">
            <h1>سلف الموظفين</h1>
            <p>إدارة السلف مع اختيار الأشهر التي يتم الخصم فيها.</p>
        </div>

        @if(session('success'))<div class="card" style="background:#ecfdf5;color:#166534">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="card" style="background:#fef2f2;color:#991b1b">{{ session('error') }}</div>@endif

        <div class="card">
            <form method="GET" action="{{ route('salary-advances.index') }}">
                <div class="filters">
                    <div class="field"><label>بحث</label><input name="search" value="{{ request('search') }}" placeholder="رقم السلفة أو اسم الموظف"></div>
                    <div class="field"><label>الحالة</label><select name="status"><option value="">الكل</option><option value="pending" @selected(request('status')==='pending')>بانتظار الاعتماد</option><option value="approved" @selected(request('status')==='approved')>معتمدة</option><option value="cancelled" @selected(request('status')==='cancelled')>ملغية</option><option value="completed" @selected(request('status')==='completed')>مكتملة</option></select></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn2 primary">بحث</button><a class="btn2 soft" href="{{ route('salary-advances.index') }}">مسح</a><a class="btn2 green" href="{{ route('salary-advances.create') }}">إضافة سلفة</a></div>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead><tr><th>رقم السلفة</th><th>الموظف</th><th>مبلغ السلفة</th><th>عدد الأقساط</th><th>الخصم الشهري</th><th>أول شهر خصم</th><th>الحالة</th><th>إجراء</th></tr></thead>
                <tbody>
                @forelse($advances as $advance)
                    <tr>
                        <td>{{ $advance->advance_number }}</td>
                        <td>{{ $advance->employee->display_name ?? '-' }}<br><small>{{ $advance->employee->employee_number ?? '-' }}</small></td>
                        <td>{{ number_format($advance->amount, 2) }}</td>
                        <td>{{ $advance->installments_count }}</td>
                        <td>{{ number_format($advance->installment_amount, 2) }}</td>
                        <td>{{ optional($advance->deduction_start_date)->format('Y-m') }}</td>
                        <td><span class="pill {{ $advance->status }}">{{ $advance->status }}</span></td>
                        <td>
                            <a class="btn2 soft" style="padding:7px 9px" href="{{ route('salary-advances.show', $advance) }}">تفاصيل</a>
                            @if($advance->can_edit_schedule)
                                <a class="btn2 soft" style="padding:7px 9px" href="{{ route('salary-advances.edit', $advance) }}">تعديل</a>
                            @endif
                            @if($advance->status === 'pending')
                                <form method="POST" action="{{ route('salary-advances.approve', $advance) }}" style="display:inline">@csrf <button class="btn2 green" style="padding:7px 9px">اعتماد</button></form>
                            @endif
                            @if(in_array($advance->status, ['pending','approved']))
                                <form method="POST" action="{{ route('salary-advances.cancel', $advance) }}" style="display:inline">@csrf <button class="btn2 red" style="padding:7px 9px">إلغاء</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">لا توجد سلف.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div style="margin-top:14px">{{ $advances->links() }}</div>
        </div>
    </div>
@endsection
