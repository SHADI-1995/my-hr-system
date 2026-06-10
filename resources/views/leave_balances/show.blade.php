@extends('layouts.hr')

@section('title', 'تفاصيل رصيد الإجازات')
@section('page-title', 'تفاصيل رصيد الإجازات')

@section('content')

    <style>
        .employee-summary {
            background: linear-gradient(135deg, #4c3b91, #6d5dfc);
            color: white;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
        }

        .employee-summary h1 {
            margin: 0 0 7px;
        }

        .employee-summary p {
            margin: 0;
            opacity: .9;
        }

        .balance-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .balance-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 16px;
        }

        .balance-card h3 {
            margin: 0 0 12px;
            color: #4c3b91;
        }

        .balance-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eee;
            padding: 9px 0;
            font-size: 14px;
        }

        .balance-row:last-child {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-plus {
            background: #dcfce7;
            color: #166534;
        }

        .badge-minus {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        @media (max-width: 900px) {
            .balance-cards {
                grid-template-columns: 1fr;
            }

            .employee-summary {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="employee-summary">
        <div>
            <h1>{{ $employee->display_name }}</h1>
            <p>
                {{ $employee->employee_number }}
                —
                {{ $employee->department->name ?? 'بدون قسم' }}
                —
                {{ $employee->position->title ?? 'بدون وظيفة' }}
            </p>
        </div>

        <a href="{{ route('leave-balances.index') }}" class="hero-btn white">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
    </div>

    <div class="balance-cards">
        @forelse($balances as $balance)
            <div class="balance-card">
                <h3>{{ $balance->year_label }}</h3>

                <div class="balance-row">
                    <span>من</span>
                    <strong>{{ optional($balance->service_year_start)->format('Y-m-d') }}</strong>
                </div>

                <div class="balance-row">
                    <span>إلى</span>
                    <strong>{{ optional($balance->service_year_end)->format('Y-m-d') }}</strong>
                </div>

                <div class="balance-row">
                    <span>المستحق</span>
                    <strong>{{ number_format($balance->annual_entitled_days, 2) }}</strong>
                </div>

                <div class="balance-row">
                    <span>المرحل</span>
                    <strong>{{ number_format($balance->carried_forward_days, 2) }}</strong>
                </div>

                <div class="balance-row">
                    <span>المستخدم المدفوع</span>
                    <strong>{{ number_format($balance->used_paid_days, 2) }}</strong>
                </div>

                <div class="balance-row">
                    <span>غير المدفوع</span>
                    <strong>{{ number_format($balance->used_unpaid_days, 2) }}</strong>
                </div>

                <div class="balance-row">
                    <span>المتبقي</span>
                    <strong>{{ number_format($balance->remaining_days, 2) }}</strong>
                </div>
            </div>
        @empty
            <div class="balance-card">
                لا توجد أرصدة لهذا الموظف.
            </div>
        @endforelse
    </div>

    <div class="card">
        <div class="section-title">
            <i class="fas fa-clock-rotate-left"></i>
            هستوري حركات الإجازات
        </div>

        <table>
            <thead>
            <tr>
                <th>التاريخ</th>
                <th>نوع الحركة</th>
                <th>الأيام</th>
                <th>الرصيد قبل</th>
                <th>الرصيد بعد</th>
                <th>الوصف</th>
                <th>تم بواسطة</th>
            </tr>
            </thead>

            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ optional($transaction->created_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ $transaction->transaction_type }}</td>
                    <td>
                        @if($transaction->days >= 0)
                            <span class="badge badge-plus">{{ number_format($transaction->days, 2) }}</span>
                        @else
                            <span class="badge badge-minus">{{ number_format($transaction->days, 2) }}</span>
                        @endif
                    </td>
                    <td>{{ number_format($transaction->before_balance, 2) }}</td>
                    <td>{{ number_format($transaction->after_balance, 2) }}</td>
                    <td>{{ $transaction->description ?? '-' }}</td>
                    <td>{{ $transaction->createdBy->name ?? 'النظام' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">لا توجد حركات إجازات.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:15px;">
            {{ $transactions->links() }}
        </div>
    </div>

@endsection

