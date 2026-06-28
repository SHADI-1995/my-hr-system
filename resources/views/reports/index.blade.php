@extends('layouts.hr')

@section('title', 'التقارير')
@section('page-title', 'مركز التقارير')

@section('content')

    @php
        use App\Models\Employee;
        use App\Models\LeaveType;
        use App\Models\LeaveRequest;
        use App\Models\EmployeeLeaveTransaction;
        use App\Models\User;

        $employees = Employee::orderBy('full_name')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $directManagers = User::orderBy('name')->get();
        $selectedReport = request('report_type');

        $workflowStatuses = [
            'pending_manager' => 'بانتظار موافقة المدير المباشر',
            'manager_approved_pending_hr' => 'موافق من المدير - بانتظار الموارد البشرية',
            'approved_by_hr' => 'موافق نهائيًا من الموارد البشرية',
            'rejected_by_manager' => 'مرفوض من المدير المباشر',
            'rejected_by_hr' => 'مرفوض من الموارد البشرية',
            'cancelled' => 'ملغي',
        ];

        $workflowTransactionTypes = [
            'leave_request_created',
            'manager_approved',
            'manager_rejected',
            'hr_approved',
            'hr_rejected',
            'leave_cancelled',
            'leave_cancelled_after_approval',
        ];

        $transactionTypes = [
            'workflow' => [
                'leave_request_created' => 'تم تقديم طلب إجازة',
                'manager_approved' => 'موافقة المدير المباشر',
                'manager_rejected' => 'رفض المدير المباشر',
                'hr_approved' => 'موافقة الموارد البشرية',
                'hr_rejected' => 'رفض الموارد البشرية',
                'leave_cancelled' => 'إلغاء طلب قبل الاعتماد النهائي',
                'leave_cancelled_after_approval' => 'إلغاء إجازة معتمدة',
            ],
            'balance' => [
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
            ],
        ];

        $leaveRequests = null;
        $leaveSummary = [
            'total_requests' => 0,
            'pending_requests' => 0,
            'approved_requests' => 0,
            'rejected_requests' => 0,
            'cancelled_requests' => 0,
            'approved_days' => 0,
            'pending_days' => 0,
            'rejected_days' => 0,
            'cancelled_days' => 0,
            'pending_manager_requests' => 0,
            'pending_hr_requests' => 0,
            'approved_by_hr_requests' => 0,
            'rejected_by_manager_requests' => 0,
            'rejected_by_hr_requests' => 0,
            'workflow_cancelled_requests' => 0,
        ];

        if ($selectedReport === 'leave_report' && auth()->user()->hasPermission('leave_reports.view')) {
            $baseQuery = LeaveRequest::with([
                'employee.department',
                'employee.directManagerUser',
                'leaveType',
                'approvedBy',
                'rejectedBy',
                'directManagerApprovedBy',
                'directManagerRejectedBy',
                'hrApprovedBy',
                'hrRejectedBy',
            ]);

            if (request('employee_id')) {
                $baseQuery->where('employee_id', request('employee_id'));
            }

            if (request('leave_type_id')) {
                $baseQuery->where('leave_type_id', request('leave_type_id'));
            }

            if (request('status')) {
                $baseQuery->where('status', request('status'));
            }

            if (request('workflow_status')) {
                $baseQuery->where('workflow_status', request('workflow_status'));
            }

            if (request('direct_manager_user_id')) {
                $baseQuery->whereHas('employee', function ($employeeQuery) {
                    $employeeQuery->where('direct_manager_user_id', request('direct_manager_user_id'));
                });
            }

            if (request('deduction_status')) {
                if (request('deduction_status') === 'deducted') {
                    $baseQuery->where('workflow_status', 'approved_by_hr');
                }

                if (request('deduction_status') === 'not_deducted') {
                    $baseQuery->whereIn('workflow_status', [
                        'pending_manager',
                        'manager_approved_pending_hr',
                        'rejected_by_manager',
                        'rejected_by_hr',
                        'cancelled',
                    ]);
                }

                if (request('deduction_status') === 'reversed') {
                    $baseQuery->where('workflow_status', 'cancelled')
                        ->where('hr_status', 'cancelled_after_approval');
                }
            }

            if (request('date_from')) {
                $baseQuery->whereDate('start_date', '>=', request('date_from'));
            }

            if (request('date_to')) {
                $baseQuery->whereDate('end_date', '<=', request('date_to'));
            }

            $summaryQuery = clone $baseQuery;

            $leaveSummary = [
                'total_requests' => (clone $summaryQuery)->count(),
                'pending_requests' => (clone $summaryQuery)->where('status', 'pending')->count(),
                'approved_requests' => (clone $summaryQuery)->where('status', 'approved')->count(),
                'rejected_requests' => (clone $summaryQuery)->where('status', 'rejected')->count(),
                'cancelled_requests' => (clone $summaryQuery)->where('status', 'cancelled')->count(),
                'approved_days' => (float) (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->sum('days_count'),
                'pending_days' => (float) (clone $summaryQuery)->where('status', 'pending')->sum('days_count'),
                'rejected_days' => (float) (clone $summaryQuery)->whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])->sum('days_count'),
                'cancelled_days' => (float) (clone $summaryQuery)->where('workflow_status', 'cancelled')->sum('days_count'),
                'pending_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'pending_manager')->count(),
                'pending_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'manager_approved_pending_hr')->count(),
                'approved_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'approved_by_hr')->count(),
                'rejected_by_manager_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_manager')->count(),
                'rejected_by_hr_requests' => (clone $summaryQuery)->where('workflow_status', 'rejected_by_hr')->count(),
                'workflow_cancelled_requests' => (clone $summaryQuery)->where('workflow_status', 'cancelled')->count(),
            ];

            $leaveRequests = $baseQuery
                ->orderByDesc('id')
                ->paginate(15, ['*'], 'leave_page')
                ->withQueryString();
        }

        $leaveTransactions = null;
        $transactionSummary = [
            'total_transactions' => 0,
            'added_days' => 0,
            'deducted_days' => 0,
            'workflow_transactions' => 0,
            'balance_transactions' => 0,
            'last_transaction_at' => null,
        ];

        if ($selectedReport === 'leave_transactions' && auth()->user()->hasPermission('leave_transactions.view')) {
            $transactionQuery = EmployeeLeaveTransaction::with(['employee', 'createdBy']);

            if (request('employee_id')) {
                $transactionQuery->where('employee_id', request('employee_id'));
            }

            if (request('transaction_type')) {
                $transactionQuery->where('transaction_type', request('transaction_type'));
            }

            if (request('transaction_category') === 'workflow') {
                $transactionQuery->whereIn('transaction_type', $workflowTransactionTypes);
            }

            if (request('transaction_category') === 'balance') {
                $transactionQuery->whereNotIn('transaction_type', $workflowTransactionTypes);
            }

            if (request('date_from')) {
                $transactionQuery->whereDate('created_at', '>=', request('date_from'));
            }

            if (request('date_to')) {
                $transactionQuery->whereDate('created_at', '<=', request('date_to'));
            }

            $summaryTransactionQuery = clone $transactionQuery;

            $transactionSummary = [
                'total_transactions' => (clone $summaryTransactionQuery)->count(),
                'added_days' => (float) (clone $summaryTransactionQuery)->where('days', '>', 0)->sum('days'),
                'deducted_days' => (float) (clone $summaryTransactionQuery)->where('days', '<', 0)->sum('days'),
                'workflow_transactions' => (clone $summaryTransactionQuery)->whereIn('transaction_type', $workflowTransactionTypes)->count(),
                'balance_transactions' => (clone $summaryTransactionQuery)->whereNotIn('transaction_type', $workflowTransactionTypes)->count(),
                'last_transaction_at' => optional((clone $summaryTransactionQuery)->latest()->first())->created_at,
            ];

            $leaveTransactions = $transactionQuery
                ->latest()
                ->paginate(15, ['*'], 'transaction_page')
                ->withQueryString();
        }

        if (! function_exists('reportLeaveWorkflowStatusName')) {
            function reportLeaveWorkflowStatusName($status) {
                return match($status) {
                    'pending_manager' => 'بانتظار المدير المباشر',
                    'manager_approved_pending_hr' => 'موافق من المدير - بانتظار HR',
                    'approved_by_hr' => 'موافق نهائيًا من HR',
                    'rejected_by_manager' => 'مرفوض من المدير',
                    'rejected_by_hr' => 'مرفوض من HR',
                    'cancelled' => 'ملغي',
                    default => 'قيد المراجعة',
                };
            }
        }

        if (! function_exists('reportLeaveWorkflowStatusClass')) {
            function reportLeaveWorkflowStatusClass($status) {
                return match($status) {
                    'approved_by_hr' => 'status-approved',
                    'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                    'cancelled' => 'status-cancelled',
                    default => 'status-pending',
                };
            }
        }

        if (! function_exists('reportTransactionIsWorkflow')) {
            function reportTransactionIsWorkflow($type) {
                return in_array($type, [
                    'leave_request_created',
                    'manager_approved',
                    'manager_rejected',
                    'hr_approved',
                    'hr_rejected',
                    'leave_cancelled',
                    'leave_cancelled_after_approval',
                ], true);
            }
        }

        if (! function_exists('reportLeaveTransactionTypeName')) {
            function reportLeaveTransactionTypeName($type) {
                return match($type) {
                    'leave_request_created' => 'تم تقديم طلب إجازة',
                    'manager_approved' => 'موافقة المدير المباشر',
                    'manager_rejected' => 'رفض المدير المباشر',
                    'hr_approved' => 'موافقة الموارد البشرية',
                    'hr_rejected' => 'رفض الموارد البشرية',
                    'leave_cancelled' => 'إلغاء طلب قبل الاعتماد النهائي',
                    'leave_cancelled_after_approval' => 'إلغاء إجازة معتمدة',
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
                    default => $type ?? '-',
                };
            }
        }
    @endphp

    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden !important;
        }

        .reports-center-page,
        .reports-center-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .reports-center-page {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden !important;
        }

        .reports-page-hero {
            background: linear-gradient(135deg, #6675e8, #8b5cf6);
            border-radius: 26px;
            padding: 28px;
            color: #fff;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            box-shadow: 0 22px 55px rgba(102, 117, 232, 0.22);
        }

        .reports-page-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 900;
        }

        .reports-page-hero p {
            margin: 0;
            opacity: .9;
            font-weight: 700;
        }

        .reports-hero-icon {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .reports-box,
        .results-box {
            width: 100%;
            max-width: 100%;
            background: #fff;
            border-radius: 24px;
            border: 1px solid #eeeafc;
            box-shadow: 0 20px 50px rgba(76, 59, 145, .08);
            padding: 24px;
            margin-bottom: 22px;
            overflow: hidden;
        }

        .reports-box-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4c3b91;
            font-weight: 900;
            font-size: 18px;
            margin-bottom: 18px;
        }

        .reports-box-title i {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: #f1edff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6d5bd0;
        }

        .report-select-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 170px;
            gap: 14px;
            align-items: end;
        }

        .form-group {
            min-width: 0;
        }

        .form-group label {
            display: block;
            color: #4c3b91;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            height: 46px;
            border-radius: 15px;
            border: 1px solid #ddd6fe;
            background: #fff;
            padding: 0 13px;
            color: #111827;
            font-weight: 800;
            outline: none;
            transition: .18s ease;
            min-width: 0;
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109,91,208,.12);
        }

        .report-hint {
            margin-top: 12px;
            background: #f8f7ff;
            border: 1px solid #eee7ff;
            border-radius: 14px;
            color: #6b5fb5;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.8;
        }

        .report-filter-panel {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid #f0ecff;
        }

        .filter-panel-header,
        .results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .filter-panel-header h3,
        .results-header h3 {
            margin: 0;
            color: #111827;
            font-size: 20px;
            font-weight: 900;
        }

        .filter-panel-header span,
        .results-header span {
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
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

        .filters-row.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .filters-row.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .actions-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            font-size: 15px;
            line-height: 1;
        }

        .report-btn {
            height: 46px;
            min-width: 125px;
            border: none;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            text-decoration: none;
            font-weight: 900;
            cursor: pointer;
            transition: .18s ease;
            white-space: nowrap;
        }

        .report-btn.primary {
            background: #6d5bd0;
            color: #fff;
            box-shadow: 0 12px 28px rgba(109,91,208,.23);
        }

        .report-btn.success {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 12px 28px rgba(22,163,74,.18);
        }

        .report-btn.pdf {
            background: #dc2626;
            color: #fff;
            box-shadow: 0 12px 28px rgba(220,38,38,.18);
        }

        .report-btn.danger {
            background: #ef4444;
            color: #fff;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #f8f7ff;
            border: 1px solid #eee7ff;
            border-radius: 16px;
            padding: 14px;
            min-width: 0;
        }

        .summary-card .label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .summary-card .value {
            color: #111827;
            font-size: 22px;
            font-weight: 900;
            word-break: break-word;
        }

        .table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden !important;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            background: #fff;
        }

        .report-table {
            width: 100%;
            max-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .report-table th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 12px;
            font-weight: 900;
            padding: 12px 8px;
            border-bottom: 1px solid #e7e0ff;
            white-space: normal;
            line-height: 1.5;
        }

        .report-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f1f5;
            color: #1f2937;
            font-weight: 700;
            font-size: 12px;
            line-height: 1.7;
            vertical-align: middle;
            white-space: normal;
            word-break: break-word;
        }

        .report-table tr:hover td {
            background: #fbfaff;
        }

        .col-id { width: 52px; text-align: center; }
        .col-employee { width: 24%; }
        .col-type { width: 22%; text-align: center; }
        .col-period { width: 19%; text-align: center; }
        .col-days { width: 75px; text-align: center; }
        .col-status { width: 95px; text-align: center; }
        .col-balance { width: 85px; text-align: center; }
        .col-action { width: 88px; text-align: center; }

        .employee-name-cell {
            display: block;
            font-weight: 900;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .employee-sub {
            display: block;
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }

        .status-pill,
        .type-pill,
        .category-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.5;
            white-space: normal;
        }

        .type-pill {
            background: #eef2ff;
            color: #4c3b91;
        }

        .category-pill.workflow {
            background: #fff7ed;
            color: #9a3412;
        }

        .category-pill.balance {
            background: #ecfdf5;
            color: #166534;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e5e7eb; color: #374151; }

        .days-plus {
            color: #15803d;
            font-weight: 900;
        }

        .days-minus {
            color: #b91c1c;
            font-weight: 900;
        }

        .details-btn {
            border: none;
            border-radius: 12px;
            height: 34px;
            padding: 0 11px;
            background: #eef2ff;
            color: #4c3b91;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .details-btn:hover {
            background: #e3ddff;
        }

        .details-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, .55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .details-modal-backdrop.show {
            display: flex;
        }

        .details-modal {
            width: min(780px, 95vw);
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 25px 80px rgba(0,0,0,.25);
            border: 1px solid #eee;
        }

        .details-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid #eee;
            background: #f8f7ff;
            border-radius: 22px 22px 0 0;
        }

        .details-modal-header h3 {
            margin: 0;
            color: #4c3b91;
            font-size: 20px;
            font-weight: 900;
        }

        .details-close-btn {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 50%;
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            font-size: 18px;
        }

        .details-modal-body {
            padding: 22px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .details-item {
            background: #f9fafb;
            border: 1px solid #eee;
            border-radius: 14px;
            padding: 13px;
            min-width: 0;
        }

        .details-item.full {
            grid-column: 1 / -1;
        }

        .details-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .details-value {
            color: #111827;
            font-weight: 900;
            line-height: 1.8;
            word-break: break-word;
        }

        @media (max-width: 950px) {
            .reports-page-hero,
            .report-select-grid,
            .filters-row.three,
            .filters-row.two {
                grid-template-columns: 1fr;
            }

            .reports-page-hero {
                align-items: flex-start;
            }

            .actions-row .report-btn {
                flex: 1 1 150px;
            }

            .report-table th,
            .report-table td {
                font-size: 11px;
                padding: 10px 6px;
            }

            .col-id { width: 42px; }
            .col-days { width: 62px; }
            .col-status { width: 82px; }
            .col-action { width: 74px; }
        }

        @media (max-width: 700px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="reports-center-page">

        <div class="reports-page-hero">
            <div>
                <h1>التقارير</h1>
                <p>مركز تقارير الموارد البشرية — اختر التقرير ثم اضغط عرض الفلاتر</p>
            </div>

            <div class="reports-hero-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <div class="reports-box">
            <div class="reports-box-title">
                <i class="fas fa-file-export"></i>
                <span>اختر التقرير</span>
            </div>

            <form method="GET" action="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}">
                <div class="report-select-grid">
                    <div class="form-group">
                        <label>اسم التقرير</label>
                        <select name="report_type">
                            <option value="">اختر التقرير المراد عرضه أو تصديره</option>
                            @if(auth()->user()->hasPermission('leave_reports.view'))
                                <option value="leave_reports_hub" data-url="{{ route('leave-reports.hub') }}">
                                    تقارير إدارة الإجازات
                                </option>
                            @endif

                            @if(auth()->user()->hasPermission('payroll_reports_hub.view'))
                                <option value="payroll_reports_hub" data-url="{{ route('payroll-reports-hub.index') }}">
                                    تقارير الرواتب الشاملة
                                </option>
                            @endif
                            @if(auth()->user()->hasPermission('leave_reports.view'))
                                <option value="leave_report" {{ $selectedReport === 'leave_report' ? 'selected' : '' }}>تقرير الإجازات</option>
                            @endif

                            @if(auth()->user()->hasPermission('leave_transactions.view'))
                                <option value="leave_transactions" {{ $selectedReport === 'leave_transactions' ? 'selected' : '' }}>سجل حركات الإجازات</option>
                            @endif


                        </select>
                    </div>

                    <button type="submit" class="report-btn primary">
                        <span class="btn-icon">▾</span>
                        عرض الفلاتر
                    </button>
                </div>
            </form>



            @if($selectedReport === 'leave_report' && auth()->user()->hasPermission('leave_reports.view'))
                <div class="report-filter-panel">
                    <div class="filter-panel-header">
                        <div>
                            <h3>فلتر تقرير الإجازات</h3>
                            <span>تقرير طلبات الإجازات حسب الموظف، نوع الإجازة، الحالة والفترة</span>
                        </div>
                    </div>

                    <form method="GET" action="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}">
                        <input type="hidden" name="report_type" value="leave_report">

                        <div class="filters-layout">
                            <div class="filters-row three">
                                <div class="form-group">
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

                                <div class="form-group">
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

                                <div class="form-group">
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

                            <div class="filters-row two">
                                <div class="form-group">
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

                                <div class="form-group">
                                    <label>حالة الخصم من الرصيد</label>
                                    <select name="deduction_status">
                                        <option value="">كل الحالات</option>
                                        <option value="deducted" {{ request('deduction_status') == 'deducted' ? 'selected' : '' }}>تم الخصم</option>
                                        <option value="not_deducted" {{ request('deduction_status') == 'not_deducted' ? 'selected' : '' }}>لم يتم الخصم</option>
                                        <option value="reversed" {{ request('deduction_status') == 'reversed' ? 'selected' : '' }}>تم إرجاع الرصيد</option>
                                    </select>
                                </div>
                            </div>

                            <div class="filters-row two">
                                <div class="form-group">
                                    <label>من تاريخ</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                                </div>

                                <div class="form-group">
                                    <label>إلى تاريخ</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>

                            <div class="actions-row">
                                <button type="submit" class="report-btn primary">
                                    <span class="btn-icon">👁</span>
                                    عرض التقرير
                                </button>

                                @if(auth()->user()->hasPermission('leave_reports.export'))
                                    <button
                                        type="submit"
                                        class="report-btn success"
                                        formaction="{{ \Illuminate\Support\Facades\Route::has('leave-reports.export-excel') ? route('leave-reports.export-excel') : url('/leave-reports/export-excel') }}">
                                        <span class="btn-icon">📊</span>
                                        تحميل Excel
                                    </button>

                                    <button
                                        type="submit"
                                        class="report-btn pdf"
                                        formaction="{{ \Illuminate\Support\Facades\Route::has('leave-reports.print-pdf') ? route('leave-reports.print-pdf') : url('/leave-reports/print-pdf') }}"
                                        formtarget="_blank">
                                        <span class="btn-icon">📄</span>
                                        PDF
                                    </button>
                                @endif

                                <a href="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="report-btn danger">
                                    <span class="btn-icon">↺</span>
                                    مسح
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            @if($selectedReport === 'leave_transactions' && auth()->user()->hasPermission('leave_transactions.view'))
                <div class="report-filter-panel">
                    <div class="filter-panel-header">
                        <div>
                            <h3>فلتر سجل حركات الإجازات</h3>
                            <span>تقرير حركات الأرصدة: إضافة، خصم، ترحيل، أو إرجاع رصيد</span>
                        </div>
                    </div>

                    <form method="GET" action="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}">
                        <input type="hidden" name="report_type" value="leave_transactions">

                        <div class="filters-layout">
                            <div class="filters-row three">
                                <div class="form-group">
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

                                <div class="form-group">
                                    <label>تصنيف الحركة</label>
                                    <select name="transaction_category">
                                        <option value="">كل التصنيفات</option>
                                        <option value="workflow" {{ request('transaction_category') == 'workflow' ? 'selected' : '' }}>مسار الموافقات</option>
                                        <option value="balance" {{ request('transaction_category') == 'balance' ? 'selected' : '' }}>حركات الرصيد</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>نوع الحركة</label>
                                    <select name="transaction_type">
                                        <option value="">كل الحركات</option>
                                        @foreach(($transactionTypes['workflow'] ?? []) as $typeValue => $typeLabel)
                                            <option value="{{ $typeValue }}" {{ request('transaction_type') == $typeValue ? 'selected' : '' }}>
                                                {{ $typeLabel }}
                                            </option>
                                        @endforeach
                                        @foreach(($transactionTypes['balance'] ?? []) as $typeValue => $typeLabel)
                                            <option value="{{ $typeValue }}" {{ request('transaction_type') == $typeValue ? 'selected' : '' }}>
                                                {{ $typeLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="filters-row two">
                                <div class="form-group">
                                    <label>من تاريخ</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                                </div>

                                <div class="form-group">
                                    <label>إلى تاريخ</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>

                            <div class="actions-row">
                                <button type="submit" class="report-btn primary">
                                    <span class="btn-icon">👁</span>
                                    عرض السجل
                                </button>

                                @if(\Illuminate\Support\Facades\Route::has('leave-transactions.export') && auth()->user()->hasPermission('leave_transactions.export'))
                                    <button
                                        type="submit"
                                        class="report-btn success"
                                        formaction="{{ route('leave-transactions.export') }}">
                                        <span class="btn-icon">📊</span>
                                        تحميل Excel
                                    </button>
                                @endif

                                <a href="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="report-btn danger">
                                    <span class="btn-icon">↺</span>
                                    مسح
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        @if($selectedReport === 'leave_report' && auth()->user()->hasPermission('leave_reports.view'))
            <div class="results-box">
                <div class="results-header">
                    <div>
                        <h3>نتائج تقرير الإجازات</h3>
                        <span>النتائج حسب الفلاتر المحددة في الأعلى</span>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">إجمالي الطلبات</div>
                        <div class="value">{{ $leaveSummary['total_requests'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">بانتظار المدير</div>
                        <div class="value">{{ $leaveSummary['pending_manager_requests'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">بانتظار HR</div>
                        <div class="value">{{ $leaveSummary['pending_hr_requests'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">معتمدة نهائيًا</div>
                        <div class="value">{{ $leaveSummary['approved_by_hr_requests'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">الأيام المعتمدة</div>
                        <div class="value">{{ number_format($leaveSummary['approved_days'], 2) }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">مرفوضة / ملغاة</div>
                        <div class="value">{{ $leaveSummary['rejected_by_manager_requests'] + $leaveSummary['rejected_by_hr_requests'] + $leaveSummary['workflow_cancelled_requests'] }}</div>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="report-table">
                        <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th class="col-employee">الموظف</th>
                            <th class="col-type">نوع الإجازة</th>
                            <th class="col-period">الفترة</th>
                            <th class="col-days">الأيام</th>
                            <th class="col-status">حالة المسار</th>
                            <th class="col-status">المدير</th>
                            <th class="col-status">HR</th>
                            <th class="col-action">التفاصيل</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($leaveRequests as $leaveRequest)
                            @php
                                $employeeName = $leaveRequest->employee->display_name
                                    ?? $leaveRequest->employee->full_name
                                    ?? $leaveRequest->employee->name
                                    ?? '-';

                                $employeeNumber = $leaveRequest->employee->employee_number ?? '-';
                                $leaveTypeName = $leaveRequest->leaveType->name ?? '-';
                                $startDate = optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date;
                                $endDate = optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date;
                                $daysCount = number_format((float) $leaveRequest->days_count, 2);
                                $statusName = reportLeaveWorkflowStatusName($leaveRequest->workflow_status);
                                $statusClass = reportLeaveWorkflowStatusClass($leaveRequest->workflow_status);
                                $managerName = $leaveRequest->employee->directManagerUser->name ?? '-';
                                $managerDecisionBy = $leaveRequest->directManagerApprovedBy->name ?? $leaveRequest->directManagerRejectedBy->name ?? '-';
                                $managerDecisionAt = optional($leaveRequest->direct_manager_approved_at ?? $leaveRequest->direct_manager_rejected_at)->format('Y-m-d H:i') ?? '-';
                                $hrDecisionBy = $leaveRequest->hrApprovedBy->name ?? $leaveRequest->hrRejectedBy->name ?? '-';
                                $hrDecisionAt = optional($leaveRequest->hr_approved_at ?? $leaveRequest->hr_rejected_at)->format('Y-m-d H:i') ?? '-';
                                $managerStatus = match($leaveRequest->direct_manager_status) {
                                    'approved' => 'موافق',
                                    'rejected' => 'مرفوض',
                                    default => 'قيد المراجعة',
                                };
                                $hrStatus = match($leaveRequest->hr_status) {
                                    'approved' => 'موافق',
                                    'rejected' => 'مرفوض',
                                    'pending' => 'قيد المعالجة',
                                    'waiting_manager' => 'بانتظار المدير',
                                    'not_required' => 'غير مطلوب',
                                    'cancelled', 'cancelled_after_approval' => 'ملغي',
                                    default => '-',
                                };
                                $reason = $leaveRequest->reason ?? '-';
                                $rejectReason = $leaveRequest->reject_reason ?? '-';
                                $approvedBy = $leaveRequest->approvedBy->name ?? '-';
                                $approvedAt = optional($leaveRequest->approved_at)->format('Y-m-d H:i') ?? '-';
                                $rejectedBy = $leaveRequest->rejectedBy->name ?? '-';
                                $rejectedAt = optional($leaveRequest->rejected_at)->format('Y-m-d H:i') ?? '-';
                            @endphp

                            <tr>
                                <td class="col-id">{{ $leaveRequest->id }}</td>

                                <td class="col-employee">
                                    <span class="employee-name-cell">{{ $employeeName }}</span>
                                    <span class="employee-sub">{{ $employeeNumber }}</span>
                                </td>

                                <td class="col-type">
                                    <span class="type-pill">{{ $leaveTypeName }}</span>
                                </td>

                                <td class="col-period">
                                    {{ $startDate }}<br>
                                    <span class="employee-sub">إلى {{ $endDate }}</span>
                                </td>

                                <td class="col-days">
                                    <strong>{{ $daysCount }}</strong>
                                </td>

                                <td class="col-status">
                                    <span class="status-pill {{ $statusClass }}">{{ $statusName }}</span>
                                </td>

                                <td class="col-status">
                                    <span class="employee-sub">{{ $managerName }}</span>
                                    <br>
                                    <strong>{{ $managerStatus }}</strong>
                                </td>

                                <td class="col-status">
                                    <strong>{{ $hrStatus }}</strong>
                                </td>

                                <td class="col-action">
                                    <button
                                        type="button"
                                        class="details-btn"
                                        onclick="openDetailsModal({
                                            title: 'تفاصيل طلب الإجازة',
                                            items: [
                                                ['رقم الطلب', @js($leaveRequest->id)],
                                                ['الموظف', @js($employeeName)],
                                                ['الرقم الوظيفي', @js($employeeNumber)],
                                                ['نوع الإجازة', @js($leaveTypeName)],
                                                ['من تاريخ', @js($startDate)],
                                                ['إلى تاريخ', @js($endDate)],
                                                ['عدد الأيام', @js($daysCount)],
                                                ['حالة المسار', @js($statusName)],
                                                ['المدير المباشر', @js($managerName)],
                                                ['قرار المدير', @js($managerStatus)],
                                                ['تم قرار المدير بواسطة', @js($managerDecisionBy)],
                                                ['وقت قرار المدير', @js($managerDecisionAt)],
                                                ['قرار الموارد البشرية', @js($hrStatus)],
                                                ['تم قرار HR بواسطة', @js($hrDecisionBy)],
                                                ['وقت قرار HR', @js($hrDecisionAt)],
                                                ['تم القبول بواسطة', @js($approvedBy)],
                                                ['وقت القبول', @js($approvedAt)],
                                                ['تم الرفض / الإلغاء بواسطة', @js($rejectedBy)],
                                                ['وقت الرفض / الإلغاء', @js($rejectedAt)]
                                            ],
                                            descriptionLabel: 'سبب الطلب',
                                            description: @js($reason),
                                            secondDescriptionLabel: 'سبب الرفض / الإلغاء',
                                            secondDescription: @js($rejectReason)
                                        })">
                                        <span class="btn-icon">👁</span>
                                        عرض
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">لا توجد بيانات حسب الفلاتر المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:16px;">
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        @endif

        @if($selectedReport === 'leave_transactions' && auth()->user()->hasPermission('leave_transactions.view'))
            <div class="results-box">
                <div class="results-header">
                    <div>
                        <h3>نتائج سجل حركات الإجازات</h3>
                        <span>النتائج حسب الفلاتر المحددة في الأعلى</span>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">إجمالي الحركات</div>
                        <div class="value">{{ $transactionSummary['total_transactions'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">الأيام المضافة</div>
                        <div class="value">{{ number_format($transactionSummary['added_days'], 2) }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">الأيام المخصومة</div>
                        <div class="value">{{ number_format(abs($transactionSummary['deducted_days']), 2) }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">حركات مسار الموافقات</div>
                        <div class="value">{{ $transactionSummary['workflow_transactions'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">حركات الرصيد</div>
                        <div class="value">{{ $transactionSummary['balance_transactions'] }}</div>
                    </div>

                    <div class="summary-card">
                        <div class="label">آخر حركة</div>
                        <div class="value" style="font-size:16px;">
                            {{ optional($transactionSummary['last_transaction_at'])->format('Y-m-d H:i') ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="report-table">
                        <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th class="col-employee">الموظف</th>
                            <th class="col-type">نوع الحركة</th>
                            <th class="col-status">التصنيف</th>
                            <th class="col-days">الأيام</th>
                            <th class="col-balance">قبل</th>
                            <th class="col-balance">بعد</th>
                            <th class="col-action">التفاصيل</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($leaveTransactions as $transaction)
                            @php
                                $employeeName = $transaction->employee->display_name
                                    ?? $transaction->employee->full_name
                                    ?? $transaction->employee->name
                                    ?? '-';

                                $employeeNumber = $transaction->employee->employee_number ?? '-';
                                $transactionTypeName = reportLeaveTransactionTypeName($transaction->transaction_type);
                                $isWorkflowTransaction = reportTransactionIsWorkflow($transaction->transaction_type);
                                $transactionCategoryName = $isWorkflowTransaction ? 'مسار الموافقات' : 'حركة رصيد';
                                $transactionDays = number_format((float) $transaction->days, 2);
                                $beforeBalance = number_format((float) $transaction->before_balance, 2);
                                $afterBalance = number_format((float) $transaction->after_balance, 2);
                                $description = $transaction->description ?? '-';
                                $createdBy = $transaction->createdBy->name ?? '-';
                                $createdAt = optional($transaction->created_at)->format('Y-m-d H:i') ?? '-';
                            @endphp

                            <tr>
                                <td class="col-id">{{ $transaction->id }}</td>

                                <td class="col-employee">
                                    <span class="employee-name-cell">{{ $employeeName }}</span>
                                    <span class="employee-sub">{{ $employeeNumber }}</span>
                                </td>

                                <td class="col-type">
                                    <span class="type-pill">{{ $transactionTypeName }}</span>
                                </td>

                                <td class="col-status">
                                    <span class="category-pill {{ $isWorkflowTransaction ? 'workflow' : 'balance' }}">
                                        {{ $transactionCategoryName }}
                                    </span>
                                </td>

                                <td class="col-days">
                                    <span class="{{ (float) $transaction->days >= 0 ? 'days-plus' : 'days-minus' }}">
                                        {{ $transactionDays }}
                                    </span>
                                </td>

                                <td class="col-balance">{{ $beforeBalance }}</td>
                                <td class="col-balance">{{ $afterBalance }}</td>

                                <td class="col-action">
                                    <button
                                        type="button"
                                        class="details-btn"
                                        onclick="openDetailsModal({
                                            title: 'تفاصيل حركة الإجازة',
                                            items: [
                                                ['رقم الحركة', @js($transaction->id)],
                                                ['الموظف', @js($employeeName)],
                                                ['الرقم الوظيفي', @js($employeeNumber)],
                                                ['نوع الحركة', @js($transactionTypeName)],
                                                ['تصنيف الحركة', @js($transactionCategoryName)],
                                                ['الأيام', @js($transactionDays)],
                                                ['الرصيد قبل', @js($beforeBalance)],
                                                ['الرصيد بعد', @js($afterBalance)],
                                                ['تم بواسطة', @js($createdBy)],
                                                ['التاريخ', @js($createdAt)]
                                            ],
                                            descriptionLabel: 'الوصف',
                                            description: @js($description),
                                            secondDescriptionLabel: '',
                                            secondDescription: ''
                                        })">
                                        <span class="btn-icon">👁</span>
                                        عرض
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">لا توجد حركات إجازات حسب الفلاتر المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:16px;">
                    {{ $leaveTransactions->links() }}
                </div>
            </div>
        @endif

        <div class="details-modal-backdrop" id="detailsModal">
            <div class="details-modal">
                <div class="details-modal-header">
                    <h3 id="detailsModalTitle">التفاصيل</h3>
                    <button type="button" class="details-close-btn" onclick="closeDetailsModal()">×</button>
                </div>

                <div class="details-modal-body">
                    <div class="details-grid" id="detailsModalGrid"></div>

                    <div class="details-grid" style="margin-top:14px;">
                        <div class="details-item full" id="detailsDescriptionBox">
                            <div class="details-label" id="detailsDescriptionLabel">الوصف</div>
                            <div class="details-value" id="detailsDescriptionValue">-</div>
                        </div>

                        <div class="details-item full" id="detailsSecondDescriptionBox">
                            <div class="details-label" id="detailsSecondDescriptionLabel">-</div>
                            <div class="details-value" id="detailsSecondDescriptionValue">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function openDetailsModal(data) {
            document.getElementById('detailsModalTitle').innerText = data.title || 'التفاصيل';

            const grid = document.getElementById('detailsModalGrid');
            grid.innerHTML = '';

            (data.items || []).forEach(function(item) {
                const div = document.createElement('div');
                div.className = 'details-item';
                div.innerHTML = `
                    <div class="details-label">${item[0] || '-'}</div>
                    <div class="details-value">${item[1] || '-'}</div>
                `;
                grid.appendChild(div);
            });

            document.getElementById('detailsDescriptionLabel').innerText = data.descriptionLabel || 'الوصف';
            document.getElementById('detailsDescriptionValue').innerText = data.description || '-';

            const secondBox = document.getElementById('detailsSecondDescriptionBox');

            if (data.secondDescriptionLabel) {
                secondBox.style.display = 'block';
                document.getElementById('detailsSecondDescriptionLabel').innerText = data.secondDescriptionLabel;
                document.getElementById('detailsSecondDescriptionValue').innerText = data.secondDescription || '-';
            } else {
                secondBox.style.display = 'none';
            }

            document.getElementById('detailsModal').classList.add('show');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.remove('show');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDetailsModal();
            }
        });

        document.getElementById('detailsModal')?.addEventListener('click', function(event) {
            if (event.target.id === 'detailsModal') {
                closeDetailsModal();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportSelect = document.querySelector('select[name="report_type"]');

            if (!reportSelect) {
                return;
            }

            reportSelect.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];

                if (
                    option &&
                    (
                        option.value === 'leave_reports_hub' ||
                        option.value === 'payroll_reports_hub'
                    ) &&
                    option.dataset.url
                ) {
                    window.location.href = option.dataset.url;
                }
            });
        });
    </script>
@endsection
