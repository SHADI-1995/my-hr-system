@extends('layouts.hr')

@section('title', 'الإجازات الرسمية')
@section('page-title', 'الإجازات الرسمية')

@section('content')

    <style>
        .filter-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #eee;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            background: #fff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            min-width: 65px;
        }

        .badge-green { background: #dcfce7; color: #166534; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }

        .table-actions {
            display: flex;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .mini-btn {
            border: none;
            border-radius: 9px;
            padding: 7px 9px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .mini-edit { background: #eef2ff; color: #4c3b91; }
        .mini-delete { background: #fee2e2; color: #991b1b; }

        @media (max-width: 900px) {
            .filter-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-calendar-star"></i>
            </div>

            <div>
                <h1>الإجازات الرسمية</h1>
                <p>إدارة العطل الرسمية التي يمكن استبعادها من حساب أيام الإجازة</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('official_holidays.create'))
                <a href="{{ route('official-holidays.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة إجازة رسمية
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('official-holidays.index') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الإجازة أو السنة">
                </div>

                <div class="form-group">
                    <label>السنة</label>
                    <select name="year_label">
                        <option value="">كل السنوات</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year_label') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
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
                <th>اسم الإجازة</th>
                <th>من تاريخ</th>
                <th>إلى تاريخ</th>
                <th>عدد الأيام</th>
                <th>النوع</th>
                <th>السنة</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
            </thead>

            <tbody>
            @forelse($officialHolidays as $holiday)
                <tr>
                    <td>{{ $holiday->id }}</td>
                    <td><strong>{{ $holiday->name }}</strong></td>
                    <td>{{ optional($holiday->start_date)->format('Y-m-d') }}</td>
                    <td>{{ optional($holiday->end_date)->format('Y-m-d') }}</td>
                    <td>{{ $holiday->start_date->diffInDays($holiday->end_date) + 1 }}</td>
                    <td><span class="badge badge-blue">{{ $holiday->type ?? 'عام' }}</span></td>
                    <td>{{ $holiday->year_label ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $holiday->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $holiday->is_active ? 'مفعلة' : 'معطلة' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            @if(auth()->user()->hasPermission('official_holidays.edit'))
                                <a href="{{ route('official-holidays.edit', $holiday->id) }}" class="mini-btn mini-edit">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('official_holidays.delete'))
                                <form action="{{ route('official-holidays.destroy', $holiday->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الإجازة الرسمية؟')">
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
                    <td colspan="9">لا توجد إجازات رسمية.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:15px;">
            {{ $officialHolidays->links() }}
        </div>
    </div>

@endsection

