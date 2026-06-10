@extends('layouts.hr')

@section('title', 'سجل حركات الإجازات')
@section('page-title', 'سجل حركات الإجازات')

@section('content')

    <style>
        .transactions-page {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .transactions-hero {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            border-radius: 24px;
            padding: 28px;
            margin-bottom: 22px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            box-shadow: 0 20px 45px rgba(76, 59, 145, 0.22);
        }

        .transactions-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 900;
        }

        .transactions-hero p {
            margin: 0;
            opacity: 0.88;
            font-weight: 700;
        }

        .transactions-icon {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 14px 35px rgba(76, 59, 145, 0.07);
        }

        .summary-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .summary-value {
            color: #111827;
            font-size: 25px;
            font-weight: 900;
        }

        .filters-card,
        .table-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, 0.08);
        }

        .filters-layout {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .filters-row {
            display: grid;
            gap: 14px;
            align-items: end;
        }

        .filters-row-1 {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }

        .filters-row-2 {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }

        .filter-group label {
            display: block;
            color: #4c3b91;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            height: 46px;
            border-radius: 14px;
            border: 1px solid #ddd6fe;
            background: #fff;
            padding: 0 12px;
            color: #111827;
            font-weight: 700;
            outline: none;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109,91,208,.12);
        }

        .filter-actions-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            height: 46px;
            min-width: 118px;
            border: none;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
        }

        .filter-btn.search {
            background: #6d5bd0;
            color: #fff;
        }

        .filter-btn.clear {
            background: #ef4444;
            color: #fff;
        }

        .filter-btn.export {
            background: #16a34a;
            color: #fff;
        }

        .table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            border: 1px solid #eeeafc;
            border-radius: 18px;
        }

        .transactions-table {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .transactions-table th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 9px;
            font-weight: 900;
            padding: 7px 4px;
            border-bottom: 1px solid #e7e0ff;
            white-space: normal;
            line-height: 1.45;
            text-align: center;
            word-break: break-word;
        }

        .transactions-table td {
            padding: 7px 4px;
            border-bottom: 1px solid #f1f1f5;
            color: #1f2937;
            font-weight: 800;
            font-size: 9px;
            line-height: 1.45;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            text-align: center;
            vertical-align: middle;
        }

        .transactions-table tr:hover {
            background: #fbfaff;
        }

        .type-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 5px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 900;
            line-height: 1.35;
            background: #eef2ff;
            color: #4c3b91;
            max-width: 100%;
            white-space: normal;
        }

        .days-plus {
            color: #15803d;
            font-weight: 900;
        }

        .days-minus {
            color: #b91c1c;
            font-weight: 900;
        }


        .transactions-table th:nth-child(1),
        .transactions-table td:nth-child(1) { width: 4%; }

        .transactions-table th:nth-child(2),
        .transactions-table td:nth-child(2) { width: 13%; }

        .transactions-table th:nth-child(3),
        .transactions-table td:nth-child(3) { width: 8%; }

        .transactions-table th:nth-child(4),
        .transactions-table td:nth-child(4) { width: 14%; }

        .transactions-table th:nth-child(5),
        .transactions-table td:nth-child(5) { width: 6%; }

        .transactions-table th:nth-child(6),
        .transactions-table td:nth-child(6),
        .transactions-table th:nth-child(7),
        .transactions-table td:nth-child(7) { width: 7%; }

        .transactions-table th:nth-child(8),
        .transactions-table td:nth-child(8) { width: 22%; }

        .transactions-table th:nth-child(9),
        .transactions-table td:nth-child(9) { width: 9%; }

        .transactions-table th:nth-child(10),
        .transactions-table td:nth-child(10) { width: 10%; }

        .days-plus,
        .days-minus {
            font-size: 9px;
        }

        .filter-btn {
            font-size: 11px;
            padding: 0 10px;
            min-width: 105px;
        }

        html,
        body,
        .transactions-page {
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        ::-webkit-scrollbar:horizontal {
            height: 0 !important;
            display: none !important;
        }

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters-row-1,
            .filters-row-2 {
                grid-template-columns: 1fr;
            }

            .transactions-table th,
            .transactions-table td {
                font-size: 8px;
                padding: 6px 3px;
            }

            .type-pill {
                font-size: 7px;
                padding: 3px 4px;
            }
        }

        @media (max-width: 650px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .filter-btn {
                flex: 1;
            }
        }
    </style>

    <div class="transactions-page">

        <div class="transactions-hero">
            <div>
                <h1>سجل حركات الإجازات</h1>
                <p>متابعة كل حركة تمت على أرصدة الإجازات: إضافة، خصم، ترحيل، أو إرجاع رصيد</p>
            </div>

            <div class="transactions-icon">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">إجمالي الحركات</div>
                <div class="summary-value">{{ $summary['total_transactions'] ?? 0 }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">إجمالي الأيام المضافة</div>
                <div class="summary-value">{{ number_format($summary['total_positive_days'] ?? 0, 2) }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">إجمالي الأيام المخصومة</div>
                <div class="summary-value">{{ number_format(abs($summary['total_negative_days'] ?? 0), 2) }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">آخر حركة</div>
                <div class="summary-value" style="font-size:18px;">
                    {{ optional($summary['last_transaction_at'] ?? null)->format('Y-m-d H:i') ?? '-' }}
                </div>
            </div>
        </div>

        <div class="filters-card">
            <form method="GET" action="{{ route('leave-transactions.index') }}">
                <div class="filters-layout">

                    <div class="filters-row filters-row-1">
                        <div class="filter-group">
                            <label>الموظف</label>
                            <select name="employee_id">
                                <option value="">كل الموظفين</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->display_name ?? $employee->full_name ?? $employee->name }}
                                        —
                                        {{ $employee->employee_number ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>نوع الحركة</label>
                            <select name="transaction_type">
                                <option value="">كل الحركات</option>
                                <option value="annual_accrual" {{ request('transaction_type') == 'annual_accrual' ? 'selected' : '' }}>إضافة رصيد سنوي</option>
                                <option value="carry_forward" {{ request('transaction_type') == 'carry_forward' ? 'selected' : '' }}>ترحيل رصيد</option>
                                <option value="policy_recalculation" {{ request('transaction_type') == 'policy_recalculation' ? 'selected' : '' }}>إعادة احتساب سياسة</option>
                                <option value="paid_leave_deduction" {{ request('transaction_type') == 'paid_leave_deduction' ? 'selected' : '' }}>خصم إجازة مدفوعة</option>
                                <option value="paid_leave_reversal" {{ request('transaction_type') == 'paid_leave_reversal' ? 'selected' : '' }}>إرجاع رصيد</option>
                                <option value="unpaid_leave_record" {{ request('transaction_type') == 'unpaid_leave_record' ? 'selected' : '' }}>تسجيل إجازة غير مدفوعة</option>
                                <option value="unpaid_leave_reversal" {{ request('transaction_type') == 'unpaid_leave_reversal' ? 'selected' : '' }}>إلغاء إجازة غير مدفوعة</option>
                                <option value="official_leave_record" {{ request('transaction_type') == 'official_leave_record' ? 'selected' : '' }}>تسجيل إجازة رسمية</option>
                                <option value="other_leave_record" {{ request('transaction_type') == 'other_leave_record' ? 'selected' : '' }}>تسجيل إجازة أخرى</option>
                                <option value="other_leave_reversal" {{ request('transaction_type') == 'other_leave_reversal' ? 'selected' : '' }}>إلغاء إجازة أخرى</option>
                            </select>
                        </div>
                    </div>

                    <div class="filters-row filters-row-2">
                        <div class="filter-group">
                            <label>من تاريخ</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="filter-group">
                            <label>إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="filter-actions-row">
                        <button type="submit" class="filter-btn search">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a href="{{ route('leave-transactions.index') }}" class="filter-btn clear">
                            <i class="fas fa-rotate-left"></i>
                            مسح
                        </a>

                        @if(auth()->user()->hasPermission('leave_transactions.export'))
                            <a href="{{ route('leave-transactions.export', request()->query()) }}" class="filter-btn export">
                                <i class="fas fa-file-excel"></i>
                                تصدير Excel
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-wrapper">
                <table class="transactions-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الموظف</th>
                        <th>الرقم الوظيفي</th>
                        <th>نوع الحركة</th>
                        <th>الأيام</th>
                        <th>الرصيد قبل</th>
                        <th>الرصيد بعد</th>
                        <th>الوصف</th>
                        <th>تم بواسطة</th>
                        <th>التاريخ</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $typeName = match($transaction->transaction_type) {
                                'annual_accrual' => 'إضافة رصيد سنوي',
                                'carry_forward' => 'ترحيل رصيد',
                                'policy_recalculation' => 'إعادة احتساب سياسة',
                                'paid_leave_deduction' => 'خصم إجازة مدفوعة',
                                'paid_leave_reversal' => 'إرجاع رصيد',
                                'unpaid_leave_record' => 'تسجيل إجازة غير مدفوعة',
                                'unpaid_leave_reversal' => 'إلغاء إجازة غير مدفوعة',
                                'official_leave_record' => 'تسجيل إجازة رسمية',
                                'other_leave_record' => 'تسجيل إجازة أخرى',
                                'other_leave_reversal' => 'إلغاء إجازة أخرى',
                                default => $transaction->transaction_type ?? '-',
                            };
                        @endphp

                        <tr>
                            <td>{{ $transaction->id }}</td>

                            <td>
                                {{ $transaction->employee->display_name
                                    ?? $transaction->employee->full_name
                                    ?? $transaction->employee->name
                                    ?? '-' }}
                            </td>

                            <td>{{ $transaction->employee->employee_number ?? '-' }}</td>

                            <td>
                                <span class="type-pill">{{ $typeName }}</span>
                            </td>

                            <td>
                                <span class="{{ (float) $transaction->days >= 0 ? 'days-plus' : 'days-minus' }}">
                                    {{ number_format((float) $transaction->days, 2) }}
                                </span>
                            </td>

                            <td>{{ number_format((float) $transaction->before_balance, 2) }}</td>
                            <td>{{ number_format((float) $transaction->after_balance, 2) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($transaction->description ?? '-', 50) }}</td>
                            <td>{{ $transaction->createdBy->name ?? '-' }}</td>
                            <td>{{ optional($transaction->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">لا توجد حركات إجازات حسب الفلاتر المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>

@endsection
