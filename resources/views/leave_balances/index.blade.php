@extends('layouts.hr')

@section('title', 'أرصدة الإجازات')
@section('page-title', 'أرصدة الإجازات')

@section('content')

    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden !important;
        }

        .leave-balance-page,
        .leave-balance-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .leave-balance-page {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .page-hero {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            border-radius: 26px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 22px 55px rgba(76, 59, 145, .22);
        }

        .page-hero h1 {
            color: #fff;
            font-size: 30px;
            font-weight: 900;
            margin: 0 0 8px;
        }

        .page-hero p {
            color: rgba(255,255,255,.9);
            font-weight: 800;
            margin: 0;
        }

        .hero-icon {
            width: 68px;
            height: 68px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .hero-actions form {
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 16px 36px rgba(76, 59, 145, .07);
            min-width: 0;
            position: relative;
            overflow: hidden;
        }

        .stat-box::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #6d5bd0, #8b5cf6);
        }

        .stat-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .stat-value {
            color: #4c3b91;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.2;
            word-break: break-word;
        }

        .filter-card,
        .leave-balance-table-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, .08);
            overflow: hidden;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: minmax(220px, 2fr) minmax(135px, 1fr) minmax(120px, 1fr) minmax(120px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .form-group {
            min-width: 0;
        }

        .form-group label {
            display: block;
            color: #4c3b91;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            height: 44px;
            padding: 0 12px;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            outline: none;
            background: #fff;
            color: #111827;
            font-weight: 800;
            font-size: 12px;
            transition: .18s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109, 91, 208, .12);
        }

        .filter-grid .btn,
        .hero-btn {
            height: 44px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            white-space: nowrap;
        }

        .balance-table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            background: #fff;
        }

        .balance-table {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .balance-table th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 10px;
            font-weight: 900;
            padding: 9px 5px;
            border-bottom: 1px solid #e7e0ff;
            text-align: center;
            vertical-align: middle;
            line-height: 1.45;
            white-space: normal;
            word-break: break-word;
        }

        .balance-table td {
            padding: 9px 5px;
            border-bottom: 1px solid #f1f1f5;
            color: #111827;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.55;
            text-align: center;
            vertical-align: middle;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .balance-table tr:hover td {
            background: #fbfaff;
        }

        .balance-table th:nth-child(1),
        .balance-table td:nth-child(1) { width: 15%; }

        .balance-table th:nth-child(2),
        .balance-table td:nth-child(2) { width: 10%; }

        .balance-table th:nth-child(3),
        .balance-table td:nth-child(3) { width: 13%; }

        .balance-table th:nth-child(4),
        .balance-table td:nth-child(4),
        .balance-table th:nth-child(5),
        .balance-table td:nth-child(5),
        .balance-table th:nth-child(6),
        .balance-table td:nth-child(6),
        .balance-table th:nth-child(7),
        .balance-table td:nth-child(7),
        .balance-table th:nth-child(8),
        .balance-table td:nth-child(8) { width: 8%; }

        .balance-table th:nth-child(9),
        .balance-table td:nth-child(9) { width: 9%; }

        .balance-table th:nth-child(10),
        .balance-table td:nth-child(10) { width: 7%; }

        .employee-main-name {
            display: block;
            color: #111827;
            font-weight: 900;
            font-size: 11px;
            line-height: 1.5;
        }

        .employee-sub-number {
            display: block;
            color: #6b7280;
            font-size: 9px;
            font-weight: 800;
            margin-top: 2px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
            line-height: 1.35;
            max-width: 100%;
            white-space: normal;
        }

        .badge-open { background: #dcfce7; color: #166534; }
        .badge-closed { background: #fef3c7; color: #92400e; }
        .badge-settled { background: #dbeafe; color: #1d4ed8; }

        .remaining-positive { color: #166534; font-weight: 900; }
        .remaining-zero { color: #991b1b; font-weight: 900; }

        .details-btn {
            height: 32px;
            padding: 0 10px;
            border-radius: 11px;
            font-size: 10px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #6d5bd0;
            color: #fff;
            text-decoration: none;
            white-space: nowrap;
        }

        ::-webkit-scrollbar:horizontal {
            height: 0 !important;
            display: none !important;
        }

        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            .balance-table th,
            .balance-table td {
                font-size: 9px;
                padding: 7px 4px;
            }

            .badge {
                font-size: 8px;
                padding: 3px 5px;
            }
        }

        @media (max-width: 650px) {
            .stats-grid,
            .filter-grid { grid-template-columns: 1fr; }

            .page-hero { padding: 22px; }

            .filter-grid .btn,
            .hero-btn { width: 100%; }
        }
    </style>

    <div class="leave-balance-page">

        <div class="page-hero">
            <div class="hero-info">
                <div class="hero-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div>
                    <h1>أرصدة الإجازات</h1>
                    <p>متابعة الرصيد المستحق والمستخدم والمتبقي لكل موظف</p>
                </div>
            </div>

            <div class="hero-actions">
                <form action="{{ route('leave-balances.recalculate') }}" method="POST">
                    @csrf
                    <button type="submit" class="hero-btn white">
                        <i class="fas fa-rotate"></i>
                        إعادة احتساب الأرصدة
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">إجمالي الرصيد السنوي</div>
                <div class="stat-value">{{ number_format($totals['annual_entitled_days'], 2) }}</div>
            </div>

            <div class="stat-box">
                <div class="stat-label">إجمالي المرحل</div>
                <div class="stat-value">{{ number_format($totals['carried_forward_days'], 2) }}</div>
            </div>

            <div class="stat-box">
                <div class="stat-label">إجمالي المستخدم المدفوع</div>
                <div class="stat-value">{{ number_format($totals['used_paid_days'], 2) }}</div>
            </div>

            <div class="stat-box">
                <div class="stat-label">إجمالي غير المدفوع</div>
                <div class="stat-value">{{ number_format($totals['used_unpaid_days'], 2) }}</div>
            </div>

            <div class="stat-box">
                <div class="stat-label">إجمالي المتبقي</div>
                <div class="stat-value">{{ number_format($totals['remaining_days'], 2) }}</div>
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('leave-balances.index') }}">
                <div class="filter-grid">
                    <div class="form-group">
                        <label>بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الموظف أو الرقم الوظيفي">
                    </div>

                    <div class="form-group">
                        <label>القسم</label>
                        <select name="department_id">
                            <option value="">الكل</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>سنة الرصيد</label>
                        <select name="year_label">
                            <option value="">الكل</option>
                            @foreach($yearLabels as $year)
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
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                            <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>تمت تسويته</option>
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

        <div class="leave-balance-table-card">
            <div class="balance-table-wrapper">
                <table class="balance-table">
                    <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>دورة الإجازة</th>
                        <th>المستحق</th>
                        <th>المرحل</th>
                        <th>المستخدم المدفوع</th>
                        <th>غير المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th>التفاصيل</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($leaveBalances as $balance)
                        <tr>
                            <td>
                                <span class="employee-main-name">{{ $balance->employee->display_name ?? '-' }}</span>
                                <span class="employee-sub-number">{{ $balance->employee->employee_number ?? '-' }}</span>
                            </td>
                            <td>{{ $balance->employee->department->name ?? '-' }}</td>
                            <td>
                                {{ optional($balance->service_year_start)->format('Y-m-d') }}
                                <br>
                                إلى
                                {{ optional($balance->service_year_end)->format('Y-m-d') }}
                            </td>
                            <td>{{ number_format($balance->annual_entitled_days, 2) }}</td>
                            <td>{{ number_format($balance->carried_forward_days, 2) }}</td>
                            <td>{{ number_format($balance->used_paid_days, 2) }}</td>
                            <td>{{ number_format($balance->used_unpaid_days, 2) }}</td>
                            <td>
                        <span class="{{ $balance->remaining_days > 0 ? 'remaining-positive' : 'remaining-zero' }}">
                            {{ number_format($balance->remaining_days, 2) }}
                        </span>
                            </td>
                            <td>
                                @if($balance->status === 'open')
                                    <span class="badge badge-open">مفتوح</span>
                                @elseif($balance->status === 'closed')
                                    <span class="badge badge-closed">مغلق</span>
                                @else
                                    <span class="badge badge-settled">تمت تسويته</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('leave-balances.show', $balance->employee_id) }}" class="details-btn">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">لا توجد أرصدة إجازات. اضغط إعادة احتساب الأرصدة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:15px;">
                {{ $leaveBalances->links() }}
            </div>
        </div>

    </div>

@endsection

