@extends('layouts.hr')

@section('title', 'الجنسيات')
@section('page-title', 'الجنسيات')

@section('content')
    <style>
        .filter-card { background:#fff; border-radius:16px; padding:18px; margin-bottom:18px; border:1px solid #eee; }
        .filter-grid { display:grid; grid-template-columns:2fr 1fr auto; gap:12px; align-items:end; }
        .form-group label { display:block; font-weight:bold; margin-bottom:7px; color:#333; }
        .form-group input, .form-group select { width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:10px; outline:none; background:#fff; }
        .status-badge { display:inline-flex; align-items:center; padding:6px 11px; border-radius:999px; font-size:12px; font-weight:bold; }
        .status-active { background:#dcfce7; color:#166534; }
        .status-inactive { background:#f3f4f6; color:#6b7280; }
        .table-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .mini-btn { border:none; border-radius:9px; padding:7px 10px; cursor:pointer; text-decoration:none; font-size:12px; font-weight:bold; display:inline-flex; align-items:center; gap:5px; }
        .mini-edit { background:#eef2ff; color:#4c3b91; }
        .mini-toggle { background:#fef3c7; color:#92400e; }
        .mini-delete { background:#fee2e2; color:#991b1b; }
        @media (max-width:800px) { .filter-grid { grid-template-columns:1fr; } }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-globe"></i></div>
            <div>
                <h1>إدارة الجنسيات</h1>
                <p>إضافة وتعديل الجنسيات المستخدمة في ملف الموظف</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('nationalities.create'))
                <a href="{{ route('nationalities.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة جنسية
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="background:#fef2f2; color:#991b1b; padding:14px; border-radius:12px; margin-bottom:15px;">{{ session('error') }}</div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('nationalities.index') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الجنسية أو الرمز">
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status">
                        <option value="">الكل</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>مفعلة</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>معطلة</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>اسم الجنسية بالعربي</th>
                <th>اسم الجنسية بالإنجليزي</th>
                <th>الرمز</th>
                <th>عدد الموظفين</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
            </thead>

            <tbody>
            @forelse($nationalities as $nationality)
                <tr>
                    <td>{{ $nationality->id }}</td>
                    <td>{{ $nationality->name_ar }}</td>
                    <td>{{ $nationality->name_en ?? '-' }}</td>
                    <td>{{ $nationality->code ?? '-' }}</td>
                    <td>{{ $nationality->employees_count }}</td>
                    <td>
                        @if($nationality->is_active)
                            <span class="status-badge status-active">مفعلة</span>
                        @else
                            <span class="status-badge status-inactive">معطلة</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            @if(auth()->user()->hasPermission('nationalities.edit'))
                                <a href="{{ route('nationalities.edit', $nationality->id) }}" class="mini-btn mini-edit">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>

                                <form action="{{ route('nationalities.toggle-status', $nationality->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="mini-btn mini-toggle">
                                        <i class="fas fa-toggle-on"></i>
                                        {{ $nationality->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                            @endif

                            @if(auth()->user()->hasPermission('nationalities.delete') && $nationality->employees_count == 0)
                                <form action="{{ route('nationalities.destroy', $nationality->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الجنسية؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mini-btn mini-delete">
                                        <i class="fas fa-trash"></i>
                                        حذف
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">لا توجد جنسيات</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:15px;">
            {{ $nationalities->links() }}
        </div>
    </div>
@endsection
