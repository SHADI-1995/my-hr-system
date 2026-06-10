@extends('layouts.hr')

@section('title', 'أرصدة الإجازات')
@section('page-title', 'أرصدة الإجازات')

@section('content')

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 18px;
        }

        .stat-box {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .04);
        }

        .stat-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .stat-value {
            color: #4c3b91;
            font-size: 24px;
            font-weight: bold;
        }

        .filter-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #eee;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
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
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-open {
            background: #dcfce7;
            color: #166534;
        }

        .badge-closed {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-settled {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .remaining-positive {
            color: #166534;
            font-weight: bold;
        }

        .remaining-zero {
            color: #991b1b;
            font-weight: bold;
        }

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 650px) {
            .stats-grid,
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

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

    <div class="card">
        <table>
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
                        <strong>{{ $balance->employee->display_name ?? '-' }}</strong>
                        <br>
                        <small>{{ $balance->employee->employee_number ?? '-' }}</small>
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
                        <a href="{{ route('leave-balances.show', $balance->employee_id) }}" class="btn btn-sm">
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

        <div style="margin-top:15px;">
            {{ $leaveBalances->links() }}
        </div>
    </div>

@endsection

