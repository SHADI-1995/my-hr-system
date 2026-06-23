@extends('layouts.hr')

@section('title', 'سجل حركات مسير الرواتب')
@section('page-title', 'سجل حركات مسير الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .filters{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:end}
        .field label{display:block;margin-bottom:7px;color:#4c3b91;font-size:12px;font-weight:900}
        .field input,.field select{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-size:12px;font-weight:800;outline:none;background:#fff}
        .actions{display:flex;gap:8px;flex-wrap:wrap}
        .btn{height:42px;border:0;border-radius:14px;padding:0 16px;font-size:12px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.green{background:#16a34a;color:#fff}.red{background:#dc2626;color:#fff}.dark{background:#111827;color:#fff}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}
        th{background:#f1edff;color:#4c3b91;font-size:11px;font-weight:900;padding:10px 7px;text-align:center}
        td{border-top:1px solid #f1eefb;padding:10px 7px;font-size:11px;font-weight:800;text-align:center;word-break:break-word;vertical-align:middle}
        .pill{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:900;white-space:nowrap}
        .created{background:#e0f2fe;color:#075985}.calculated{background:#dbeafe;color:#1d4ed8}.approved{background:#fef3c7;color:#92400e}.approval_cancelled{background:#fee2e2;color:#991b1b}.paid{background:#dcfce7;color:#166534}.deleted{background:#f3f4f6;color:#374151}
        .status{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:900;background:#f3f4f6;color:#374151}
        .meta{color:#6b7280;font-size:10px;font-weight:800;line-height:1.7;text-align:right}
        .empty{text-align:center;color:#6b7280;font-weight:900;padding:28px}
        .pagination{margin-top:15px}
        @media(max-width:1200px){.filters{grid-template-columns:repeat(3,1fr)}}
        @media(max-width:700px){.filters{grid-template-columns:1fr}.hero h1{font-size:22px}}
    </style>

    <div class="page">
        <div class="hero">
            <div>
                <h1>سجل حركات مسير الرواتب</h1>
                <p>متابعة كل العمليات التي تمت على المسيرات: إنشاء، احتساب، اعتماد، إلغاء اعتماد، صرف، حذف.</p>
            </div>

            <div class="actions">
                @if(auth()->user()->hasPermission('payroll_period_logs.export'))
                    <a href="{{ route('payroll-period-logs.export-excel', request()->query()) }}" class="btn green">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>

                    <a href="{{ route('payroll-period-logs.print-pdf', request()->query()) }}" class="btn dark" target="_blank">
                        <i class="fas fa-print"></i>
                        PDF
                    </a>
                @endif

                <a href="{{ route('payroll-periods.index') }}" class="btn soft">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمسيرات
                </a>
            </div>
        </div>

        <div class="card">
            <form method="GET" action="{{ route('payroll-period-logs.index') }}">
                <div class="filters">
                    <div class="field">
                        <label>نوع العملية</label>
                        <select name="action">
                            <option value="">الكل</option>
                            @foreach($actions as $key => $label)
                                <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>رقم المسير</label>
                        <input type="text" name="period_number" value="{{ request('period_number') }}" placeholder="مثال: PAY-000001">
                    </div>

                    <div class="field">
                        <label>المسير</label>
                        <select name="payroll_period_id">
                            <option value="">الكل</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected((string) request('payroll_period_id') === (string) $period->id)>
                                    {{ $period->period_number }} - {{ $period->month }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>المستخدم</label>
                        <select name="user_id">
                            <option value="">الكل</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="field">
                        <label>إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="actions" style="margin-top:14px">
                    <button class="btn primary" type="submit">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>

                    <a href="{{ route('payroll-period-logs.index') }}" class="btn soft">
                        <i class="fas fa-rotate-right"></i>
                        تصفير
                    </a>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                <tr>
                    <th style="width:12%">التاريخ</th>
                    <th style="width:13%">العملية</th>
                    <th style="width:12%">رقم المسير</th>
                    <th style="width:8%">الشهر</th>
                    <th style="width:9%">من حالة</th>
                    <th style="width:9%">إلى حالة</th>
                    <th style="width:12%">المستخدم</th>
                    <th>الوصف / التفاصيل</th>
                </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    @php
                        $statusFromText = match ($log->status_from) {
                            'draft' => 'مسودة',
                            'calculated' => 'محسوب',
                            'approved' => 'معتمد',
                            'paid' => 'مدفوع',
                            'cancelled' => 'ملغي',
                            default => $log->status_from ?: '-',
                        };

                        $statusToText = match ($log->status_to) {
                            'draft' => 'مسودة',
                            'calculated' => 'محسوب',
                            'approved' => 'معتمد',
                            'paid' => 'مدفوع',
                            'cancelled' => 'ملغي',
                            default => $log->status_to ?: '-',
                        };

                        $meta = is_array($log->meta) ? $log->meta : [];
                    @endphp

                    <tr>
                        <td dir="ltr">{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="pill {{ $log->action }}">
                                {{ $log->action_text ?? ($actions[$log->action] ?? $log->action) }}
                            </span>
                        </td>
                        <td>
                            @if($log->payrollPeriod)
                                <a href="{{ route('payroll-periods.show', $log->payrollPeriod) }}" style="font-weight:900;color:#4c3b91;text-decoration:none">
                                    {{ $log->payrollPeriod->period_number }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $log->payrollPeriod?->month ?? '-' }}</td>
                        <td><span class="status">{{ $statusFromText }}</span></td>
                        <td><span class="status">{{ $statusToText }}</span></td>
                        <td>{{ $log->user?->name ?? '-' }}</td>
                        <td>
                            <div class="meta">
                                <div>{{ $log->description ?: '-' }}</div>

                                @if(!empty($meta))
                                    <div>
                                        @if(isset($meta['employees_count']))
                                            الموظفين: {{ $meta['employees_count'] }}
                                        @endif

                                        @if(isset($meta['total_gross_salary']))
                                            | الإجمالي: {{ number_format((float) $meta['total_gross_salary'], 2) }}
                                        @endif

                                        @if(isset($meta['total_deductions']))
                                            | الخصومات: {{ number_format((float) $meta['total_deductions'], 2) }}
                                        @endif

                                        @if(isset($meta['total_net_salary']))
                                            | الصافي: {{ number_format((float) $meta['total_net_salary'], 2) }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            لا توجد حركات مسجلة حسب الفلاتر المحددة.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
