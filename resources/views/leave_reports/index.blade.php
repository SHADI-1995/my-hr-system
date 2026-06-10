@extends('layouts.hr')

@section('title', 'تقارير الإجازات')
@section('page-title', 'تقارير الإجازات')

@section('content')

    <style>
        .leave-report-page {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .report-hero {
            background: linear-gradient(135deg, #6d5bd0, #8b5cf6);
            border-radius: 24px;
            padding: 28px;
            margin-bottom: 22px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            box-shadow: 0 20px 45px rgba(109, 91, 208, 0.22);
        }

        .report-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 900;
        }

        .report-hero p {
            margin: 0;
            opacity: 0.88;
            font-weight: 700;
        }

        .report-hero-icon {
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
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
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
            font-size: 27px;
            font-weight: 900;
        }

        .summary-sub {
            color: #8b8fa3;
            font-size: 12px;
            margin-top: 5px;
            font-weight: 700;
        }

        .filters-card,
        .report-table-card {
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
            grid-template-columns: repeat(3, minmax(200px, 1fr));
        }

        .filters-row-2 {
            grid-template-columns: repeat(2, minmax(200px, 1fr));
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

        .report-table {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .report-table th {
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

        .report-table td {
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

        .report-table tr:hover {
            background: #fbfaff;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 5px;
            min-width: 0;
            max-width: 100%;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 900;
            line-height: 1.35;
            white-space: normal;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e5e7eb; color: #374151; }


        .report-table th:nth-child(1),
        .report-table td:nth-child(1) { width: 3.5%; }

        .report-table th:nth-child(2),
        .report-table td:nth-child(2) { width: 12%; }

        .report-table th:nth-child(3),
        .report-table td:nth-child(3) { width: 7.5%; }

        .report-table th:nth-child(4),
        .report-table td:nth-child(4) { width: 9%; }

        .report-table th:nth-child(5),
        .report-table td:nth-child(5),
        .report-table th:nth-child(6),
        .report-table td:nth-child(6) { width: 7.5%; }

        .report-table th:nth-child(7),
        .report-table td:nth-child(7) { width: 5%; }

        .report-table th:nth-child(8),
        .report-table td:nth-child(8) { width: 10%; }

        .report-table th:nth-child(9),
        .report-table td:nth-child(9) { width: 9%; }

        .report-table th:nth-child(10),
        .report-table td:nth-child(10) { width: 8%; }

        .report-table th:nth-child(11),
        .report-table td:nth-child(11) { width: 8%; }

        .report-table th:nth-child(12),
        .report-table td:nth-child(12) { width: 7%; }

        .report-table th:nth-child(13),
        .report-table td:nth-child(13),
        .report-table th:nth-child(14),
        .report-table td:nth-child(14) { width: 6.5%; }

        .report-table small {
            font-size: 8px !important;
            line-height: 1.25;
        }

        .filter-btn,
        .report-btn {
            font-size: 11px;
            padding: 0 10px;
            min-width: 105px;
        }

        html,
        body,
        .leave-report-page {
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
                grid-template-columns: repeat(2, 1fr);
            }

            .report-table th,
            .report-table td {
                font-size: 8px;
                padding: 6px 3px;
            }

            .status-pill {
                font-size: 7px;
                padding: 3px 4px;
            }
        }

        @media (max-width: 650px) {
            .summary-grid,
            .filters-row-1,
            .filters-row-2 {
                grid-template-columns: 1fr;
            }

            .filter-btn {
                flex: 1;
            }
        }
    </style>

    <div class="leave-report-page">

        <div class="report-hero">
            <div>
                <h1>تقارير الإجازات</h1>
                <p>تحليل طلبات الإجازات حسب الموظف، النوع، الحالة، والفترة الزمنية</p>
            </div>

            <div class="report-hero-icon">
                <i class="fas fa-chart-column"></i>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">إجمالي الطلبات</div>
                <div class="summary-value">{{ $summary['total_requests'] }}</div>
                <div class="summary-sub">كل الحالات ضمن الفلتر الحالي</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">بانتظار المدير</div>
                <div class="summary-value">{{ $summary['pending_manager_requests'] ?? 0 }}</div>
                <div class="summary-sub">طلبات لم يقررها المدير بعد</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">بانتظار HR</div>
                <div class="summary-value">{{ $summary['pending_hr_requests'] ?? 0 }}</div>
                <div class="summary-sub">وافق المدير وتنتظر الموارد البشرية</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">معتمدة نهائيًا</div>
                <div class="summary-value">{{ $summary['approved_by_hr_requests'] ?? 0 }}</div>
                <div class="summary-sub">{{ number_format($summary['approved_days'] ?? 0, 2) }} يوم معتمد</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">رفض المدير</div>
                <div class="summary-value">{{ $summary['rejected_by_manager_requests'] ?? 0 }}</div>
                <div class="summary-sub">لم يتم تحويلها إلى HR</div>
            </div>

            <div class="summary-card">
                <div class="summary-label">رفض HR / ملغاة</div>
                <div class="summary-value">{{ ($summary['rejected_by_hr_requests'] ?? 0) + ($summary['workflow_cancelled_requests'] ?? 0) }}</div>
                <div class="summary-sub">
                    مرفوض HR: {{ $summary['rejected_by_hr_requests'] ?? 0 }} —
                    ملغي: {{ $summary['workflow_cancelled_requests'] ?? 0 }}
                </div>
            </div>
        </div>

        <div class="filters-card">
            <form method="GET" action="{{ route('leave-reports.index') }}">
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
                            <label>نوع الإجازة</label>
                            <select name="leave_type_id">
                                <option value="">كل الأنواع</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ request('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>حالة مسار الموافقة</label>
                            <select name="workflow_status">
                                <option value="">كل حالات المسار</option>
                                @foreach($workflowStatuses as $workflowValue => $workflowLabel)
                                    <option value="{{ $workflowValue }}" {{ request('workflow_status') == $workflowValue ? 'selected' : '' }}>
                                        {{ $workflowLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="filters-row filters-row-2">
                        <div class="filter-group">
                            <label>المدير المباشر</label>
                            <select name="direct_manager_user_id">
                                <option value="">كل المديرين</option>
                                @foreach($directManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('direct_manager_user_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>حالة الخصم من الرصيد</label>
                            <select name="deduction_status">
                                <option value="">كل الحالات</option>
                                <option value="deducted" {{ request('deduction_status') == 'deducted' ? 'selected' : '' }}>تم الخصم</option>
                                <option value="not_deducted" {{ request('deduction_status') == 'not_deducted' ? 'selected' : '' }}>لم يتم الخصم</option>
                                <option value="reversed" {{ request('deduction_status') == 'reversed' ? 'selected' : '' }}>تم إرجاع الرصيد</option>
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

                        <a href="{{ route('leave-reports.index') }}" class="filter-btn clear">
                            <i class="fas fa-rotate-left"></i>
                            مسح
                        </a>

                        @if(auth()->user()->hasPermission('leave_reports.export'))
                            <a href="{{ route('leave-reports.export-excel', request()->query()) }}" class="report-btn success">
                                <i class="fas fa-file-excel"></i>
                                تصدير Excel
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        <div class="report-table-card">
            <div class="table-wrapper">
                <table class="report-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الموظف</th>
                        <th>الرقم الوظيفي</th>
                        <th>نوع الإجازة</th>
                        <th>من تاريخ</th>
                        <th>إلى تاريخ</th>
                        <th>الأيام</th>
                        <th>حالة المسار</th>
                        <th>المدير المباشر</th>
                        <th>قرار المدير</th>
                        <th>قرار HR</th>
                        <th>حالة الخصم</th>
                        <th>سبب الطلب</th>
                        <th>سبب الرفض / الإلغاء</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($leaveRequests as $leaveRequest)
                        @php
                            $statusClass = match($leaveRequest->workflow_status) {
                                'approved_by_hr' => 'status-approved',
                                'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending',
                            };

                            $statusName = match($leaveRequest->workflow_status) {
                                'pending_manager' => 'بانتظار المدير المباشر',
                                'manager_approved_pending_hr' => 'موافق من المدير - بانتظار HR',
                                'approved_by_hr' => 'موافق نهائيًا من HR',
                                'rejected_by_manager' => 'مرفوض من المدير',
                                'rejected_by_hr' => 'مرفوض من HR',
                                'cancelled' => 'ملغي',
                                default => 'قيد المراجعة',
                            };

                            $managerName = $leaveRequest->employee->directManagerUser->name ?? '-';

                            $managerDecision = match($leaveRequest->direct_manager_status) {
                                'approved' => 'موافق',
                                'rejected' => 'مرفوض',
                                default => 'قيد المراجعة',
                            };

                            $managerDecisionAt = optional($leaveRequest->direct_manager_approved_at ?? $leaveRequest->direct_manager_rejected_at)->format('Y-m-d H:i') ?? '-';

                            $hrDecision = match($leaveRequest->hr_status) {
                                'approved' => 'موافق',
                                'rejected' => 'مرفوض',
                                'pending' => 'قيد المعالجة',
                                'waiting_manager' => 'بانتظار المدير',
                                'not_required' => 'غير مطلوب',
                                'cancelled', 'cancelled_after_approval' => 'ملغي',
                                default => '-',
                            };

                            $hrDecisionAt = optional($leaveRequest->hr_approved_at ?? $leaveRequest->hr_rejected_at)->format('Y-m-d H:i') ?? '-';

                            $deductionStatus = match(true) {
                                $leaveRequest->workflow_status === 'approved_by_hr' => 'تم الخصم',
                                $leaveRequest->workflow_status === 'cancelled' && $leaveRequest->hr_status === 'cancelled_after_approval' => 'تم إرجاع الرصيد',
                                default => 'لم يتم الخصم',
                            };
                        @endphp

                        <tr>
                            <td>{{ $leaveRequest->id }}</td>
                            <td>
                                {{ $leaveRequest->employee->display_name
                                    ?? $leaveRequest->employee->full_name
                                    ?? $leaveRequest->employee->name
                                    ?? '-' }}
                            </td>
                            <td>{{ $leaveRequest->employee->employee_number ?? '-' }}</td>
                            <td>{{ $leaveRequest->leaveType->name ?? '-' }}</td>
                            <td>{{ optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date }}</td>
                            <td>{{ optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date }}</td>
                            <td><strong>{{ number_format((float) $leaveRequest->days_count, 2) }}</strong></td>
                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td>{{ $managerName }}</td>
                            <td>
                                {{ $managerDecision }}
                                <br>
                                <small style="color:#6b7280;">{{ $managerDecisionAt }}</small>
                            </td>
                            <td>
                                {{ $hrDecision }}
                                <br>
                                <small style="color:#6b7280;">{{ $hrDecisionAt }}</small>
                            </td>
                            <td>{{ $deductionStatus }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($leaveRequest->reason ?? '-', 40) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($leaveRequest->reject_reason ?? $leaveRequest->direct_manager_reject_reason ?? $leaveRequest->hr_reject_reason ?? '-', 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">لا توجد بيانات حسب الفلاتر المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $leaveRequests->links() }}
            </div>
        </div>

    </div>

@endsection
