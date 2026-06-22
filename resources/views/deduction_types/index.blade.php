@extends('layouts.hr')

@section('title', 'أنواع الاستقطاعات')
@section('page-title', 'أنواع الاستقطاعات')

@section('content')
    <style>
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:24px;margin-bottom:18px}
        .filters{display:grid;grid-template-columns:2fr 1fr auto;gap:12px;align-items:end}.field label{display:block;color:#4c3b91;font-weight:900;font-size:12px;margin-bottom:7px}
        .field input,.field select{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-weight:800}
        .btn2{border:0;border-radius:13px;padding:11px 14px;font-weight:900;text-decoration:none;display:inline-flex;gap:6px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.red{background:#dc2626;color:#fff}
        table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}
        th{background:#f1edff;color:#4c3b91;font-size:12px;font-weight:900;padding:12px;text-align:center}
        td{border-top:1px solid #f1eefb;padding:12px;font-size:12px;font-weight:800;text-align:center}
        .pill{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:900}.active{background:#dcfce7;color:#166534}.inactive{background:#fee2e2;color:#991b1b}
        @media(max-width:800px){.filters{grid-template-columns:1fr}}
    </style>

    <div class="hero">
        <h1>أنواع الاستقطاعات</h1>
        <p>إدارة أنواع الاستقطاع التي يتم اختيارها عند إضافة استقطاع للموظف.</p>
    </div>

    @if(session('success'))<div class="card" style="background:#ecfdf5;color:#166534;font-weight:900">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="card" style="background:#fef2f2;color:#991b1b;font-weight:900">{{ session('error') }}</div>@endif

    <div class="card">
        <form method="GET" action="{{ route('deduction-types.index') }}">
            <div class="filters">
                <div class="field"><label>بحث</label><input name="search" value="{{ request('search') }}" placeholder="الاسم أو الكود"></div>
                <div class="field"><label>الحالة</label><select name="is_active"><option value="">الكل</option><option value="1" @selected(request('is_active') === '1')>نشط</option><option value="0" @selected(request('is_active') === '0')>غير نشط</option></select></div>
                <div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn2 primary" type="submit">بحث</button><a class="btn2 soft" href="{{ route('deduction-types.index') }}">مسح</a><a class="btn2 green" href="{{ route('deduction-types.create') }}">إضافة</a></div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead><tr><th>الكود</th><th>الاسم العربي</th><th>الاسم الإنجليزي</th><th>الترتيب</th><th>عدد الاستقطاعات</th><th>الحالة</th><th>الإجراء</th></tr></thead>
            <tbody>
            @forelse($types as $type)
                <tr>
                    <td>{{ $type->code }}</td>
                    <td>{{ $type->name_ar }}</td>
                    <td>{{ $type->name_en ?: '-' }}</td>
                    <td>{{ $type->sort_order }}</td>
                    <td>{{ $type->deductions_count }}</td>
                    <td><span class="pill {{ $type->is_active ? 'active' : 'inactive' }}">{{ $type->is_active ? 'نشط' : 'غير نشط' }}</span></td>
                    <td>
                        <a class="btn2 soft" href="{{ route('deduction-types.edit', $type) }}">تعديل</a>
                        <form method="POST" action="{{ route('deduction-types.destroy', $type) }}" style="display:inline" onsubmit="return confirm('هل تريد حذف نوع الاستقطاع؟')">@csrf @method('DELETE')<button class="btn2 red" type="submit">حذف</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">لا توجد أنواع استقطاعات.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:14px">{{ $types->links() }}</div>
    </div>
@endsection
