@extends('layouts.hr')

@section('title', 'طلبات السلف')
@section('page-title', 'متابعة طلبات السلف')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-hand-holding-dollar"></i></div>
            <div>
                <h1>متابعة طلبات السلف</h1>
                <p>عرض جميع طلبات السلف المقدمة من بوابة الموظف ومتابعة مسار الموافقات.</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('salary_advance_requests.manager_approval'))
                <a href="{{ route('manager-salary-advance-approvals.index') }}" class="hero-btn">موافقات المدير</a>
            @endif

            @if(auth()->user()->hasPermission('salary_advance_requests.hr_approval'))
                <a href="{{ route('hr-salary-advance-approvals.index') }}" class="hero-btn white">موافقات HR</a>
            @endif
        </div>
    </div>

    <style>
        .sar-stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:20px}
        .sar-stat-card{background:#fff;border:1px solid #eeeafc;border-radius:18px;padding:18px;box-shadow:0 12px 24px rgba(90,64,160,.07);position:relative;overflow:hidden}
        .sar-stat-card::before{content:"";position:absolute;inset-inline-start:0;top:0;width:6px;height:100%;background:#7b5cc8}
        .sar-stat-card.orange::before{background:#f59e0b}.sar-stat-card.blue::before{background:#3b82f6}.sar-stat-card.green::before{background:#22c55e}.sar-stat-card.red::before{background:#ef4444}.sar-stat-card.gray::before{background:#6b7280}
        .sar-stat-card span{display:block;color:#6b7280;font-weight:800;font-size:13px;margin-bottom:10px}.sar-stat-card strong{display:block;color:#3f3a68;font-size:28px;font-weight:900}
        .sar-filter-card{margin-bottom:20px}.sar-filter-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:12px;align-items:end}
        .status-pill{display:inline-flex;padding:7px 12px;border-radius:999px;font-size:12px;font-weight:900;line-height:1.5}
        .status-pending{background:#fef3c7;color:#92400e}.status-processing{background:#dbeafe;color:#1d4ed8}.status-approved{background:#dcfce7;color:#166534}.status-rejected{background:#fee2e2;color:#991b1b}.status-cancelled{background:#e5e7eb;color:#374151}
        .table-actions{display:flex;gap:8px;flex-wrap:wrap}
        @media(max-width:950px){.sar-filter-grid{grid-template-columns:1fr}}
    </style>

    <div class="sar-stats-grid">
        <div class="sar-stat-card"><span>إجمالي الطلبات</span><strong>{{ $stats['total'] ?? 0 }}</strong></div>
        <div class="sar-stat-card orange"><span>بانتظار المدير</span><strong>{{ $stats['pending_manager'] ?? 0 }}</strong></div>
        <div class="sar-stat-card blue"><span>بانتظار HR</span><strong>{{ $stats['pending_hr'] ?? 0 }}</strong></div>
        <div class="sar-stat-card green"><span>مسجلة ومعتمدة</span><strong>{{ $stats['registered'] ?? 0 }}</strong></div>
        <div class="sar-stat-card red"><span>مرفوضة</span><strong>{{ $stats['rejected'] ?? 0 }}</strong></div>
        <div class="sar-stat-card gray"><span>ملغاة</span><strong>{{ $stats['cancelled'] ?? 0 }}</strong></div>
    </div>

    <div class="card sar-filter-card">
        <form method="GET" action="{{ route('salary-advance-requests.index') }}">
            <div class="sar-filter-grid">
                <div>
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الطلب / اسم الموظف / الرقم الوظيفي / الإقامة">
                </div>

                <div>
                    <label>الحالة</label>
                    <select name="workflow_status">
                        <option value="">كل الحالات</option>
                        <option value="pending_manager" {{ request('workflow_status') === 'pending_manager' ? 'selected' : '' }}>بانتظار المدير</option>
                        <option value="manager_approved_pending_hr" {{ request('workflow_status') === 'manager_approved_pending_hr' ? 'selected' : '' }}>بانتظار HR</option>
                        <option value="registered" {{ request('workflow_status') === 'registered' ? 'selected' : '' }}>مسجلة</option>
                        <option value="rejected_by_manager" {{ request('workflow_status') === 'rejected_by_manager' ? 'selected' : '' }}>مرفوضة من المدير</option>
                        <option value="rejected_by_hr" {{ request('workflow_status') === 'rejected_by_hr' ? 'selected' : '' }}>مرفوضة من HR</option>
                        <option value="cancelled" {{ request('workflow_status') === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>

                <div><label>من تاريخ</label><input type="date" name="from_date" value="{{ request('from_date') }}"></div>
                <div><label>إلى تاريخ</label><input type="date" name="to_date" value="{{ request('to_date') }}"></div>

                <div class="filter-actions">
                    <button type="submit" class="btn">بحث</button>
                    <a href="{{ route('salary-advance-requests.index') }}" class="btn btn-danger">مسح</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>الموظف</th>
                <th>المبلغ</th>
                <th>الأقساط</th>
                <th>بداية الخصم</th>
                <th>الحالة</th>
                <th>تاريخ الطلب</th>
                <th>إجراءات</th>
            </tr>
            </thead>

            <tbody>
            @forelse($salaryAdvanceRequests as $salaryAdvanceRequest)
                @php
                    $workflowStatus = $salaryAdvanceRequest->workflow_status ?? 'pending_manager';

                    $workflowName = match($workflowStatus) {
                        'pending_manager' => 'بانتظار المدير',
                        'manager_approved_pending_hr' => 'بانتظار HR',
                        'registered' => 'مسجلة ومعتمدة',
                        'approved_by_hr' => 'معتمدة من HR',
                        'rejected_by_manager' => 'مرفوضة من المدير',
                        'rejected_by_hr' => 'مرفوضة من HR',
                        'cancelled' => 'ملغاة',
                        default => 'قيد المراجعة',
                    };

                    $workflowClass = match($workflowStatus) {
                        'manager_approved_pending_hr' => 'status-processing',
                        'registered', 'approved_by_hr' => 'status-approved',
                        'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                        'cancelled' => 'status-cancelled',
                        default => 'status-pending',
                    };
                @endphp

                <tr>
                    <td>{{ $salaryAdvanceRequest->request_number ?? '#' . $salaryAdvanceRequest->id }}</td>
                    <td>
                        <strong>{{ $salaryAdvanceRequest->employee->display_name ?? $salaryAdvanceRequest->employee->full_name ?? $salaryAdvanceRequest->employee->name ?? '-' }}</strong>
                        <br><small>{{ $salaryAdvanceRequest->employee->employee_number ?? '-' }}</small>
                    </td>
                    <td>{{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</td>
                    <td>{{ $salaryAdvanceRequest->installments_count }}</td>
                    <td>{{ optional($salaryAdvanceRequest->deduction_start_date)->format('Y-m') }}</td>
                    <td><span class="status-pill {{ $workflowClass }}">{{ $workflowName }}</span></td>
                    <td>{{ optional($salaryAdvanceRequest->created_at)->format('Y-m-d') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('salary-advance-requests.show', $salaryAdvanceRequest) }}" class="btn">تفاصيل</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:#6b7280;font-weight:bold;padding:30px;">لا توجد طلبات سلف.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $salaryAdvanceRequests->links() }}</div>
    </div>
@endsection
