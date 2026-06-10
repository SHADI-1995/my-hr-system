@extends('layouts.hr')

@section('title', 'الإجازات')
@section('page-title', 'إدارة طلبات الإجازة')

@section('content')

    <div class="leave-page-fix">

        <style>

            /* منع الصفحة من الدخول تحت القائمة الجانبية عند السحب الأفقي */
            html,
            body {
                max-width: 100%;
                overflow-x: hidden !important;
            }

            body {
                position: relative;
            }

            .leave-page-fix {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden !important;
                position: relative;
            }

            .leave-page-fix,
            .leave-page-fix * {
                box-sizing: border-box;
            }

            /* هذه الأسماء تغطي أغلب أسماء حاويات المحتوى في قالب hr */
            main,
            .main,
            .main-content,
            .content,
            .content-wrapper,
            .page-content,
            .app-content,
            .dashboard-content {
                max-width: 100% !important;
                overflow-x: hidden !important;
            }

            /* لا تجعل الكروت تكبر الصفحة كلها */
            .leave-page-fix .page-hero,
            .leave-page-fix .stats-grid,
            .leave-page-fix .card {
                width: 100%;
                max-width: 100%;
                overflow: hidden;
            }

            /* التمرير الأفقي يكون داخل الجدول فقط وليس في الصفحة كاملة */
            .leave-page-fix .leave-table-wrapper {
                width: 100%;
                max-width: 100%;
                overflow-x: auto !important;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
            }

            .leave-page-fix .leave-table-wrapper::-webkit-scrollbar {
                height: 8px;
            }

            .leave-page-fix .leave-table-wrapper::-webkit-scrollbar-thumb {
                background: #c4b5fd;
                border-radius: 999px;
            }

            .leave-table-wrapper {
                width: 100%;
                max-width: 100%;
                overflow-x: auto !important;
                overflow-y: hidden;
            }

            .leave-table {
                width: max-content;
                min-width: 900px;
                max-width: none;
                table-layout: fixed;
            }

            .leave-table th,
            .leave-table td {
                white-space: nowrap;
                vertical-align: middle;
                font-size: 13px;
                padding: 11px 10px;
            }

            .leave-table th {
                font-weight: 800;
            }

            .id-col { width: 50px; text-align: center; }
            .employee-col { width: 150px; }
            .type-col { width: 130px; }
            .date-col { width: 105px; text-align: center; }
            .days-col { width: 75px; text-align: center; }
            .attachment-col { width: 90px; text-align: center; }
            .status-col { width: 105px; text-align: center; }
            .actions-col { width: 260px; min-width: 260px; }

            .status-pill,
            .type-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 11px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 800;
                min-width: 72px;
            }

            .status-pending { background: #fef3c7; color: #92400e; }
            .status-approved { background: #dcfce7; color: #166534; }
            .status-rejected { background: #fee2e2; color: #991b1b; }
            .status-cancelled { background: #e5e7eb; color: #374151; }
            .status-processing { background: #dbeafe; color: #1d4ed8; }

            .workflow-col { width: 230px; text-align: center; }

            .workflow-box {
                display: grid;
                gap: 8px;
                min-width: 210px;
            }

            .workflow-mini-progress {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
                align-items: center;
            }

            .workflow-step {
                height: 6px;
                border-radius: 999px;
                background: #e5e7eb;
            }

            .workflow-step.done {
                background: #16a34a;
            }

            .workflow-step.current {
                background: #f59e0b;
            }

            .workflow-step.rejected {
                background: #dc2626;
            }

            .workflow-text {
                color: #4b5563;
                font-size: 11px;
                font-weight: 900;
                line-height: 1.6;
                white-space: normal;
            }

            .workflow-actions-note {
                display: flex;
                flex-direction: column;
                gap: 6px;
                align-items: flex-start;
            }

            .workflow-actions-note a {
                text-decoration: none;
            }

            .mini-manager { background: #fef3c7; color: #92400e; }
            .mini-hr { background: #ccfbf1; color: #0f766e; }

            .type-paid { background: #dbeafe; color: #1d4ed8; }
            .type-unpaid { background: #f3f4f6; color: #374151; }

            .table-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-start;
                min-width: 250px;
            }

            .table-actions form {
                margin: 0;
                display: inline-flex;
                gap: 6px;
                align-items: center;
            }

            .mini-btn {
                border: none;
                border-radius: 12px;
                padding: 8px 12px;
                cursor: pointer;
                text-decoration: none;
                font-size: 12px;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                white-space: nowrap;
                min-height: 36px;
                line-height: 1;
                transition: .18s ease;
                box-shadow: 0 8px 18px rgba(17, 24, 39, 0.06);
            }

            .mini-btn:hover {
                transform: translateY(-1px);
            }

            .mini-view { background: #eef2ff; color: #4c3b91; }
            .mini-edit { background: #ede9fe; color: #5b21b6; border: 1px solid #c4b5fd; }
            .mini-approve { background: #dcfce7; color: #166534; }
            .mini-reject { background: #fee2e2; color: #991b1b; }
            .mini-delete { background: #f3f4f6; color: #374151; }
            .mini-attachment { background: #f0f9ff; color: #0369a1; }
            .mini-cancel { background: #ffedd5; color: #c2410c; border: 1px solid #fdba74; }

            .action-disabled {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                min-height: 34px;
                padding: 7px 10px;
                border-radius: 12px;
                background: #f3f4f6;
                color: #9ca3af;
                font-size: 11px;
                font-weight: 900;
                white-space: nowrap;
            }

            .filters-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
                gap: 12px;
                align-items: end;
                width: 100%;
                max-width: 100%;
            }

            .filter-actions {
                display: flex;
                gap: 10px;
                align-items: end;
                flex-wrap: wrap;
            }

            .filters-row label {
                display: block;
                font-weight: 800;
                color: #4b5563;
                margin-bottom: 7px;
                font-size: 13px;
            }

            .filters-row select,
            .filters-row input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd6fe;
                border-radius: 10px;
                background: #fff;
            }

            .leave-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(17, 24, 39, 0.55);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                padding: 20px;
            }

            .leave-modal-backdrop.show {
                display: flex;
            }

            .leave-modal {
                width: min(760px, 96vw);
                max-height: 90vh;
                overflow-y: auto;
                background: #fff;
                border-radius: 22px;
                box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
                border: 1px solid #eee;
            }

            .leave-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 18px 22px;
                border-bottom: 1px solid #eee;
                background: #f8f7ff;
                border-radius: 22px 22px 0 0;
            }

            .leave-modal-header h3 {
                margin: 0;
                color: #4c3b91;
                font-size: 20px;
            }

            .modal-close-btn {
                width: 34px;
                height: 34px;
                border: none;
                border-radius: 50%;
                background: #fff;
                color: #6b7280;
                cursor: pointer;
                font-size: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .leave-modal-body {
                padding: 22px;
            }

            .details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .details-item {
                background: #f9fafb;
                border: 1px solid #eee;
                border-radius: 14px;
                padding: 13px;
            }

            .details-item.full {
                grid-column: 1 / -1;
            }

            .details-label {
                color: #6b7280;
                font-size: 12px;
                font-weight: 800;
                margin-bottom: 6px;
            }

            .details-value {
                color: #111827;
                font-weight: 800;
                line-height: 1.8;
            }

            .modal-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                padding-top: 18px;
                margin-top: 18px;
                border-top: 1px solid #eee;
            }

            .modal-action-btn {
                border: none;
                border-radius: 12px;
                padding: 11px 15px;
                font-weight: 800;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                text-decoration: none;
            }

            .modal-approve { background: #dcfce7; color: #166534; }
            .modal-reject { background: #fee2e2; color: #991b1b; }
            .modal-cancel { background: #f3f4f6; color: #374151; }
            .modal-return { background: #fff7ed; color: #9a3412; }

            .reject-box {
                margin-top: 16px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 14px;
                padding: 14px;
                display: none;
            }

            .reject-box.show {
                display: block;
            }

            .reject-box label {
                display: block;
                color: #991b1b;
                font-weight: 800;
                margin-bottom: 8px;
            }

            .reject-box textarea,
            .reject-box input {
                width: 100%;
                border: 1px solid #fecaca;
                border-radius: 10px;
                padding: 10px;
                min-height: 80px;
                outline: none;
                resize: vertical;
                background: #fff;
            }

            .success-modal .leave-modal-header {
                background: #ecfdf5;
            }

            .success-icon {
                width: 54px;
                height: 54px;
                border-radius: 50%;
                background: #dcfce7;
                color: #166534;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                margin-bottom: 12px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
                gap: 14px;
                margin-bottom: 20px;
                width: 100%;
                max-width: 100%;
            }

            .stat-card {
                background: #fff;
                border: 1px solid #eee;
                border-radius: 16px;
                padding: 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 10px 24px rgba(17, 24, 39, 0.04);
            }

            .stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 13px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
            }

            .stat-icon.total { background:#eef2ff; color:#4c3b91; }
            .stat-icon.pending { background:#fef3c7; color:#92400e; }
            .stat-icon.approved { background:#dcfce7; color:#166534; }
            .stat-icon.rejected { background:#fee2e2; color:#991b1b; }
            .stat-icon.cancelled { background:#e5e7eb; color:#374151; }

            .stat-title {
                color:#6b7280;
                font-size:12px;
                font-weight:800;
                margin-bottom:4px;
            }

            .stat-value {
                color:#111827;
                font-size:22px;
                font-weight:900;
            }

            @media (max-width: 1100px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }


            .page-hero {
                max-width: 100%;
                overflow: hidden;
            }

            .hero-actions {
                flex-wrap: wrap;
            }

            .hero-btn {
                white-space: nowrap;
            }

            .card {
                max-width: 100%;
                overflow: hidden;
            }

            .leave-table th,
            .leave-table td {
                font-size: 12px;
                padding: 10px 8px;
            }

            .type-pill,
            .status-pill {
                padding: 5px 9px;
                min-width: 62px;
                font-size: 11px;
            }

            .mini-btn {
                padding: 6px 8px;
                font-size: 11px;
            }


            @media (max-width: 800px) {
                .filters-row {
                    grid-template-columns: 1fr !important;
                }

                .details-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* تحسين تصميم البحث والجدول */
            .leave-page-fix {
                padding-bottom: 30px;
            }

            .leave-page-fix .card {
                border-radius: 22px;
                border: 1px solid #eeeafc;
                background: rgba(255, 255, 255, 0.96);
                box-shadow: 0 18px 45px rgba(76, 59, 145, 0.08);
            }

            .leave-page-fix .filters-card {
                padding: 22px !important;
            }

            .filters-row {
                grid-template-columns: 1.5fr 1.2fr 1fr 1fr 1fr auto !important;
                gap: 16px !important;
                align-items: end;
            }

            .filter-search,
            .filter-status {
                min-width: 0;
            }

            .filters-row label {
                color: #4c3b91 !important;
                font-size: 13px !important;
                font-weight: 900 !important;
                margin-bottom: 9px !important;
            }

            .filters-row select,
            .filters-row input {
                height: 46px;
                border-radius: 14px !important;
                border: 1px solid #ddd6fe !important;
                background: #fff !important;
                color: #111827;
                font-weight: 700;
                box-shadow: 0 8px 18px rgba(76, 59, 145, 0.04);
                transition: 0.2s ease;
            }

            .filters-row select:focus,
            .filters-row input:focus {
                border-color: #6d5bd0 !important;
                box-shadow: 0 0 0 4px rgba(109, 91, 208, 0.12);
                outline: none;
            }

            .filter-actions {
                display: flex;
                gap: 10px;
                align-items: end;
                justify-content: flex-start;
            }

            .filter-actions .btn {
                height: 46px;
                min-width: 96px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                font-weight: 900;
            }

            .filter-actions .btn:first-child {
                background: #6d5bd0;
                color: #fff;
                box-shadow: 0 12px 24px rgba(109, 91, 208, 0.25);
            }

            .filter-actions .btn-danger {
                background: #ef4444 !important;
                color: #fff !important;
                box-shadow: 0 12px 24px rgba(239, 68, 68, 0.18);
            }

            .leave-table-card {
                padding: 26px !important;
            }

            .leave-table-wrapper {
                border-radius: 18px;
                border: 1px solid #eeeafc;
                background: #fff;
            }

            .leave-table {
                width: 100%;
                min-width: 820px;
                border-collapse: separate;
                border-spacing: 0;
                overflow: hidden;
            }

            .leave-table thead th {
                background: #f1edff !important;
                color: #4c3b91;
                font-size: 13px;
                font-weight: 900;
                padding: 15px 12px !important;
                border-bottom: 1px solid #e7e0ff;
            }

            .leave-table thead th:first-child {
                border-top-right-radius: 16px;
            }

            .leave-table thead th:last-child {
                border-top-left-radius: 16px;
            }

            .leave-table tbody td {
                padding: 15px 12px !important;
                border-bottom: 1px solid #f1f1f5;
                color: #1f2937;
                font-size: 13px;
            }

            .leave-table tbody tr {
                transition: 0.18s ease;
            }

            .leave-table tbody tr:hover {
                background: #fbfaff;
                transform: translateY(-1px);
            }

            .employee-col strong {
                font-size: 14px;
                color: #111827;
            }

            .employee-sub {
                color: #8b8fa3 !important;
                font-size: 11px !important;
            }

            .type-pill,
            .status-pill {
                min-width: 84px !important;
                height: 30px;
                border-radius: 999px;
                font-size: 12px !important;
                font-weight: 900 !important;
            }

            .type-paid {
                background: #e8f1ff !important;
                color: #2563eb !important;
            }

            .type-unpaid {
                background: #f3f4f6 !important;
                color: #374151 !important;
            }

            .status-approved {
                background: #dcfce7 !important;
                color: #15803d !important;
            }

            .status-pending {
                background: #fef3c7 !important;
                color: #b45309 !important;
            }

            .status-rejected {
                background: #fee2e2 !important;
                color: #b91c1c !important;
            }

            .status-cancelled {
                background: #e5e7eb !important;
                color: #374151 !important;
            }

            .mini-btn {
                height: 34px !important;
                border-radius: 12px !important;
                padding: 7px 11px !important;
                font-size: 12px !important;
                font-weight: 900 !important;
                box-shadow: none;
            }

            .mini-view {
                background: #f0edff !important;
                color: #4c3b91 !important;
            }

            .mini-view:hover {
                background: #e4dcff !important;
            }

            .mini-attachment {
                background: #ecf8ff !important;
                color: #0369a1 !important;
            }

            .pagination-wrapper {
                margin-top: 18px;
            }

            @media (max-width: 1200px) {
                .filters-row {
                    grid-template-columns: repeat(3, 1fr) !important;
                }

                .filter-actions {
                    grid-column: 1 / -1;
                }
            }

            @media (max-width: 760px) {
                .filters-row {
                    grid-template-columns: 1fr !important;
                }

                .filter-actions {
                    width: 100%;
                }

                .filter-actions .btn {
                    flex: 1;
                }

                .leave-table-card {
                    padding: 16px !important;
                }
            }


            /* توزيع فلاتر البحث على سطرين والأزرار في سطر ثالث */
            .filters-layout-3rows {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .filters-layout-3rows .filters-row {
                display: grid;
                gap: 14px;
                align-items: end;
                width: 100%;
            }

            .filters-row-1 {
                grid-template-columns: repeat(3, minmax(220px, 1fr));
            }

            .filters-row-2 {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }

            .filter-actions-row {
                display: flex;
                justify-content: flex-start;
                align-items: center;
                padding-top: 4px;
            }

            .filter-actions-row .filter-actions {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .filter-actions-row .btn {
                min-width: 110px;
                height: 46px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-weight: 900;
            }

            @media (max-width: 992px) {
                .filters-row-1,
                .filters-row-2 {
                    grid-template-columns: repeat(2, minmax(180px, 1fr));
                }
            }

            @media (max-width: 640px) {
                .filters-row-1,
                .filters-row-2 {
                    grid-template-columns: 1fr;
                }

                .filter-actions-row .filter-actions {
                    width: 100%;
                }

                .filter-actions-row .btn {
                    flex: 1;
                }
            }

        </style>

        <div class="page-hero">
            <div class="hero-info">
                <div class="hero-icon">
                    <i class="fas fa-calendar-days"></i>
                </div>

                <div>
                    <h1>الإجازات</h1>
                    <p>إدارة طلبات الإجازات واعتمادها وربطها بالأرصدة</p>
                </div>
            </div>

            <div class="hero-actions">
                <a href="#" onclick="exportTableToExcel()" class="hero-btn">
                    <i class="fas fa-file-excel"></i>
                    تصدير إكسل
                </a>

                <a href="#" onclick="exportTableToWord()" class="hero-btn">
                    <i class="fas fa-file-word"></i>
                    تصدير وورد
                </a>

                @if(auth()->user()->hasPermission('leave_requests.manager_approval') && \Illuminate\Support\Facades\Route::has('manager-leave-approvals.index'))
                    <a href="{{ route('manager-leave-approvals.index') }}" class="hero-btn white">
                        <i class="fas fa-user-check"></i>
                        موافقات المدير
                    </a>
                @endif

                @if(auth()->user()->hasPermission('leave_requests.hr_approval') && \Illuminate\Support\Facades\Route::has('hr-leave-approvals.index'))
                    <a href="{{ route('hr-leave-approvals.index') }}" class="hero-btn white">
                        <i class="fas fa-user-shield"></i>
                        موافقات HR
                    </a>
                @endif

                @if(auth()->user()->hasPermission('leave_requests.create'))
                    <a href="{{ route('leave-requests.create') }}" class="hero-btn white">
                        <i class="fas fa-plus"></i>
                        طلب إجازة
                    </a>
                @endif
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stat-title">إجمالي الطلبات</div>
                    <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-title">قيد المراجعة</div>
                    <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon approved">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <div class="stat-title">مقبولة</div>
                    <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon rejected">
                    <i class="fas fa-xmark"></i>
                </div>
                <div>
                    <div class="stat-title">مرفوضة</div>
                    <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <div>
                    <div class="stat-title">ملغاة</div>
                    <div class="stat-value">{{ $stats['cancelled'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fef2f2; color:#991b1b; padding:14px; border-radius:12px; margin-bottom:15px;">
                {{ session('error') }}
            </div>
        @endif

        @if(auth()->user()->hasPermission('leave_requests.search'))
            <div class="card filters-card" style="margin-bottom:25px;">
                <form method="GET" action="{{ route('leave-requests.index') }}">
                    <div class="filters-layout-3rows">

                        <div class="filters-row filters-row-1">
                            <div class="filter-search">
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

                            <div class="filter-status">
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

                            <div class="filter-status">
                                <label>الحالة</label>
                                <select name="status">
                                    <option value="">كل الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                            </div>

                            <div class="filter-status">
                                <label>مرحلة الموافقة</label>
                                <select name="workflow_status">
                                    <option value="">كل المراحل</option>
                                    <option value="pending_manager" {{ request('workflow_status') == 'pending_manager' ? 'selected' : '' }}>قيد المدير المباشر</option>
                                    <option value="manager_approved_pending_hr" {{ request('workflow_status') == 'manager_approved_pending_hr' ? 'selected' : '' }}>قيد الموارد البشرية</option>
                                    <option value="rejected_by_manager" {{ request('workflow_status') == 'rejected_by_manager' ? 'selected' : '' }}>مرفوضة من المدير</option>
                                    <option value="approved_by_hr" {{ request('workflow_status') == 'approved_by_hr' ? 'selected' : '' }}>مقبولة من الموارد البشرية</option>
                                    <option value="rejected_by_hr" {{ request('workflow_status') == 'rejected_by_hr' ? 'selected' : '' }}>مرفوضة من الموارد البشرية</option>
                                </select>
                            </div>
                        </div>

                        <div class="filters-row filters-row-2">
                            <div class="filter-status">
                                <label>من تاريخ</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}">
                            </div>

                            <div class="filter-status">
                                <label>إلى تاريخ</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>

                        <div class="filter-actions-row">
                            <div class="filter-actions">
                                <button type="submit" class="btn">
                                    <i class="fas fa-search"></i>
                                    بحث
                                </button>

                                <a href="{{ route('leave-requests.index') }}" class="btn btn-danger">
                                    مسح
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        @endif

        <div class="card leave-table-card">
            <div class="leave-table-wrapper">
                <table class="leave-table" id="exportTable">
                    <thead>
                    <tr>
                        <th class="id-col">#</th>
                        <th class="employee-col">الموظف</th>
                        <th class="type-col">نوع الإجازة</th>
                        <th class="days-col">الأيام</th>
                        <th class="attachment-col">المرفق</th>
                        <th class="status-col">الحالة العامة</th>
                        <th class="workflow-col">مسار الموافقة</th>

                        @if(
                            auth()->user()->hasPermission('leave_requests.edit') ||
                            auth()->user()->hasPermission('leave_requests.delete') ||
                            auth()->user()->hasPermission('leave_requests.approve') ||
                            auth()->user()->hasPermission('leave_requests.reject') ||
                            auth()->user()->hasPermission('leave_requests.cancel')
                        )
                            <th class="actions-col">الإجراءات</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($leaveRequests as $leaveRequest)
                        @php
                            $employeeName = $leaveRequest->employee->display_name
                                ?? $leaveRequest->employee->full_name
                                ?? $leaveRequest->employee->name
                                ?? '-';

                            $leaveTypeName = $leaveRequest->leaveType->name ?? '-';
                            $isDeducted = optional($leaveRequest->leaveType)->deduct_from_annual_balance;

                            $statusClass = match($leaveRequest->status) {
                                'approved' => 'status-approved',
                                'rejected' => 'status-rejected',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending',
                            };

                            $statusName = match($leaveRequest->status) {
                                'approved' => 'مقبولة',
                                'rejected' => 'مرفوضة',
                                'cancelled' => 'ملغاة',
                                default => 'قيد المراجعة',
                            };

                            $workflowStatus = $leaveRequest->workflow_status ?? 'pending_manager';
                            $workflowName = $leaveRequest->workflow_status_name ?? 'قيد المراجعة';

                            $workflowClass = match($workflowStatus) {
                                'manager_approved_pending_hr' => 'status-processing',
                                'approved_by_hr' => 'status-approved',
                                'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending',
                            };

                            $wf1 = 'done';
                            $wf2 = 'current';
                            $wf3 = '';

                            if ($workflowStatus === 'manager_approved_pending_hr') {
                                $wf1 = 'done';
                                $wf2 = 'done';
                                $wf3 = 'current';
                            } elseif ($workflowStatus === 'approved_by_hr') {
                                $wf1 = 'done';
                                $wf2 = 'done';
                                $wf3 = 'done';
                            } elseif ($workflowStatus === 'rejected_by_manager') {
                                $wf1 = 'done';
                                $wf2 = 'rejected';
                                $wf3 = '';
                            } elseif ($workflowStatus === 'rejected_by_hr') {
                                $wf1 = 'done';
                                $wf2 = 'done';
                                $wf3 = 'rejected';
                            } elseif ($workflowStatus === 'cancelled') {
                                $wf1 = 'rejected';
                                $wf2 = '';
                                $wf3 = '';
                            }

                            $attachmentUrl = $leaveRequest->attachment ? asset('storage/' . $leaveRequest->attachment) : '';

                            $canEditRequest = $leaveRequest->status === 'pending'
                                && ($leaveRequest->workflow_status ?? 'pending_manager') === 'pending_manager'
                                && auth()->user()->hasPermission('leave_requests.edit');

                            $canDeleteRequest = $leaveRequest->status === 'pending'
                                && ($leaveRequest->workflow_status ?? 'pending_manager') === 'pending_manager'
                                && auth()->user()->hasPermission('leave_requests.delete');

                            $canCancelRequest = (
                                    in_array($leaveRequest->workflow_status, ['pending_manager', 'manager_approved_pending_hr', 'approved_by_hr'], true)
                                    || in_array($leaveRequest->status, ['pending', 'approved'], true)
                                )
                                && !in_array($leaveRequest->workflow_status, ['rejected_by_manager', 'rejected_by_hr', 'cancelled'], true)
                                && auth()->user()->hasPermission('leave_requests.cancel');
                        @endphp

                        <tr>
                            <td class="id-col">{{ $leaveRequest->id }}</td>

                            <td class="employee-col">
                                <strong>{{ $employeeName }}</strong>
                                <span class="employee-sub">
                                {{ $leaveRequest->employee->employee_number ?? '' }}
                            </span>
                            </td>

                            <td class="type-col">
                            <span class="type-pill {{ $isDeducted ? 'type-paid' : 'type-unpaid' }}">
                                {{ $leaveTypeName }}
                            </span>
                            </td>

                            <td class="days-col">
                                <strong>{{ number_format((float) $leaveRequest->days_count, 2) }}</strong>
                            </td>

                            <td class="attachment-col">
                                @if($leaveRequest->attachment)
                                    <a href="{{ $attachmentUrl }}" target="_blank" class="mini-btn mini-attachment">
                                        <i class="fas fa-paperclip"></i>
                                        عرض
                                    </a>
                                @else
                                    <span style="color:#9ca3af; font-size:12px;">لا يوجد</span>
                                @endif
                            </td>

                            <td class="status-col">
                            <span class="status-pill {{ $statusClass }}">
                                {{ $statusName }}
                            </span>
                            </td>

                            <td class="workflow-col">
                                <div class="workflow-box">
                                    <span class="status-pill {{ $workflowClass }}">
                                        {{ $workflowName }}
                                    </span>

                                    <div class="workflow-mini-progress" title="{{ $workflowName }}">
                                        <span class="workflow-step {{ $wf1 }}"></span>
                                        <span class="workflow-step {{ $wf2 }}"></span>
                                        <span class="workflow-step {{ $wf3 }}"></span>
                                    </div>

                                    <div class="workflow-text">
                                        المدير:
                                        {{ $leaveRequest->directManagerApprovedBy->name ?? $leaveRequest->directManagerRejectedBy->name ?? '-' }}
                                        <br>
                                        HR:
                                        {{ $leaveRequest->hrApprovedBy->name ?? $leaveRequest->hrRejectedBy->name ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            @if(
                                auth()->user()->hasPermission('leave_requests.edit') ||
                                auth()->user()->hasPermission('leave_requests.delete') ||
                                auth()->user()->hasPermission('leave_requests.approve') ||
                                auth()->user()->hasPermission('leave_requests.reject') ||
                                auth()->user()->hasPermission('leave_requests.cancel')
                            )
                                <td class="actions-col">
                                    <div class="table-actions">

                                        <button
                                            type="button"
                                            class="mini-btn mini-view"
                                            onclick="openLeaveDetailsModal({
                                            id: '{{ $leaveRequest->id }}',
                                            employee: @js($employeeName),
                                            employeeNumber: @js($leaveRequest->employee->employee_number ?? '-'),
                                            leaveType: @js($leaveTypeName),
                                            startDate: @js(optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date),
                                            endDate: @js(optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date),
                                            daysCount: @js(number_format((float) $leaveRequest->days_count, 2)),
                                            remainingBalance: @js(optional($leaveRequest->employee->currentLeaveBalance)->remaining_days !== null ? number_format((float) $leaveRequest->employee->currentLeaveBalance->remaining_days, 2) : '-'),
                                            usedPaidDays: @js(optional($leaveRequest->employee->currentLeaveBalance)->used_paid_days !== null ? number_format((float) $leaveRequest->employee->currentLeaveBalance->used_paid_days, 2) : '-'),
                                            annualEntitledDays: @js(optional($leaveRequest->employee->currentLeaveBalance)->annual_entitled_days !== null ? number_format((float) $leaveRequest->employee->currentLeaveBalance->annual_entitled_days, 2) : '-'),
                                            status: '{{ $leaveRequest->status }}',
                                            statusName: @js($workflowName),
                                            workflowStatus: @js($workflowStatus),
                                            workflowName: @js($workflowName),
                                            directManagerStatus: @js($leaveRequest->direct_manager_status ?? '-'),
                                            directManagerBy: @js($leaveRequest->directManagerApprovedBy->name ?? $leaveRequest->directManagerRejectedBy->name ?? '-'),
                                            directManagerAt: @js(optional($leaveRequest->direct_manager_approved_at ?? $leaveRequest->direct_manager_rejected_at)->format('Y-m-d H:i') ?? '-'),
                                            hrStatus: @js($leaveRequest->hr_status ?? '-'),
                                            hrBy: @js($leaveRequest->hrApprovedBy->name ?? $leaveRequest->hrRejectedBy->name ?? '-'),
                                            hrAt: @js(optional($leaveRequest->hr_approved_at ?? $leaveRequest->hr_rejected_at)->format('Y-m-d H:i') ?? '-'),
                                            reason: @js($leaveRequest->reason ?? '-'),
                                            approvedBy: @js($leaveRequest->approvedBy->name ?? '-'),
                                            approvedAt: @js(optional($leaveRequest->approved_at)->format('Y-m-d H:i') ?? '-'),
                                            rejectedBy: @js($leaveRequest->rejectedBy->name ?? '-'),
                                            rejectedAt: @js(optional($leaveRequest->rejected_at)->format('Y-m-d H:i') ?? '-'),
                                            rejectReason: @js($leaveRequest->reject_reason ?? '-'),
                                            attachmentUrl: @js($attachmentUrl),
                                            attachmentText: @js($leaveRequest->attachment ? 'عرض / تحميل المرفق' : 'لا يوجد مرفق'),
                                            approveUrl: '{{ route('leave-requests.approve', $leaveRequest->id) }}',
                                            rejectUrl: '{{ route('leave-requests.reject', $leaveRequest->id) }}',
                                            cancelUrl: '{{ route('leave-requests.cancel-approved', $leaveRequest->id) }}',
                                            canApprove: {{ false ? 'true' : 'false' }},
                                            canReject: {{ false ? 'true' : 'false' }},
                                            canCancel: {{ $canCancelRequest ? 'true' : 'false' }}
                                        })">
                                            <i class="fas fa-eye"></i>
                                            عرض التفاصيل
                                        </button>

                                        @if($leaveRequest->status === 'pending' && $leaveRequest->workflow_status === 'pending_manager' && auth()->user()->hasPermission('leave_requests.edit'))
                                            <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="mini-btn mini-edit">
                                                <i class="fas fa-pen"></i>
                                                تعديل
                                            </a>
                                        @endif

                                        @if($leaveRequest->status === 'pending' && $leaveRequest->workflow_status === 'pending_manager' && auth()->user()->hasPermission('leave_requests.delete'))
                                            <form action="{{ route('leave-requests.destroy', $leaveRequest->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="mini-btn mini-delete"
                                                    onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="fas fa-trash"></i>
                                                    حذف
                                                </button>
                                            </form>
                                        @endif

                                        @if($canCancelRequest)
                                            <form action="{{ route('leave-requests.cancel-approved', $leaveRequest->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="cancel_reason" value="تم إلغاء الطلب من جدول طلبات الإجازة">

                                                <button
                                                    type="submit"
                                                    class="mini-btn mini-cancel"
                                                    onclick="return confirm('هل تريد إلغاء طلب الإجازة؟ إذا كان معتمدًا من HR سيتم إرجاع الرصيد، وإذا لم يعتمد من HR سيتم الإلغاء فقط.')">
                                                    <i class="fas fa-ban"></i>
                                                    إلغاء الطلب
                                                </button>
                                            </form>
                                        @elseif(!in_array($leaveRequest->workflow_status, ['rejected_by_manager', 'rejected_by_hr', 'cancelled'], true))
                                            <span class="action-disabled">
                                                <i class="fas fa-ban"></i>
                                                الإلغاء غير متاح
                                            </span>
                                        @endif

                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">لا توجد طلبات إجازة</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $leaveRequests->appends(request()->query())->links() }}
            </div>
        </div>

        <div class="leave-modal-backdrop" id="leaveDetailsModal">
            <div class="leave-modal">
                <div class="leave-modal-header">
                    <h3>تفاصيل طلب الإجازة</h3>
                    <button type="button" class="modal-close-btn" onclick="closeLeaveDetailsModal()">×</button>
                </div>

                <div class="leave-modal-body">
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">رقم الطلب</div>
                            <div class="details-value" id="modalRequestId">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">مرحلة الموافقة</div>
                            <div class="details-value" id="modalStatus">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">قرار المدير المباشر</div>
                            <div class="details-value" id="modalManagerDecision">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">بواسطة المدير / التاريخ</div>
                            <div class="details-value" id="modalManagerBy">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">قرار الموارد البشرية</div>
                            <div class="details-value" id="modalHrDecision">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">بواسطة HR / التاريخ</div>
                            <div class="details-value" id="modalHrBy">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">تم القبول بواسطة</div>
                            <div class="details-value" id="modalApprovedBy">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">وقت القبول</div>
                            <div class="details-value" id="modalApprovedAt">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">تم الرفض / الإلغاء بواسطة</div>
                            <div class="details-value" id="modalRejectedBy">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">وقت الرفض / الإلغاء</div>
                            <div class="details-value" id="modalRejectedAt">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">الموظف</div>
                            <div class="details-value" id="modalEmployee">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">الرقم الوظيفي</div>
                            <div class="details-value" id="modalEmployeeNumber">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">نوع الإجازة</div>
                            <div class="details-value" id="modalLeaveType">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">عدد الأيام</div>
                            <div class="details-value" id="modalDays">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">الرصيد المتاح للموظف</div>
                            <div class="details-value" id="modalRemainingBalance">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">الرصيد المستخدم</div>
                            <div class="details-value" id="modalUsedBalance">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">من تاريخ</div>
                            <div class="details-value" id="modalStartDate">-</div>
                        </div>

                        <div class="details-item">
                            <div class="details-label">إلى تاريخ</div>
                            <div class="details-value" id="modalEndDate">-</div>
                        </div>

                        <div class="details-item full">
                            <div class="details-label">سبب الطلب</div>
                            <div class="details-value" id="modalReason">-</div>
                        </div>

                        <div class="details-item full">
                            <div class="details-label">سبب الرفض / الإلغاء</div>
                            <div class="details-value" id="modalRejectReason">-</div>
                        </div>

                        <div class="details-item full">
                            <div class="details-label">المرفق</div>
                            <div class="details-value" id="modalAttachment">-</div>
                        </div>
                    </div>

                    <div class="modal-actions" id="modalActions">
                        <form method="POST" id="approveForm" style="display:none;" onsubmit="return showApproveConfirm()">
                            @csrf
                            <button type="submit" class="modal-action-btn modal-approve">
                                <i class="fas fa-check"></i>
                                قبول الإجازة
                            </button>
                        </form>

                        <button type="button" class="modal-action-btn modal-reject" id="showRejectBoxBtn" style="display:none;" onclick="showRejectBox()">
                            <i class="fas fa-xmark"></i>
                            رفض الإجازة
                        </button>

                        <form method="POST" id="cancelForm" style="display:none;" onsubmit="return confirm('هل تريد إلغاء طلب الإجازة؟ إذا كان معتمدًا من HR سيتم إرجاع الرصيد، وإذا لم يعتمد من HR سيتم الإلغاء فقط.')">
                            @csrf
                            <input type="hidden" name="cancel_reason" value="تم إلغاء الطلب من صفحة تفاصيل الإجازة">
                            <button type="submit" class="modal-action-btn modal-return">
                                <i class="fas fa-rotate-left"></i>
                                إلغاء الطلب
                            </button>
                        </form>

                        <button type="button" class="modal-action-btn modal-cancel" onclick="closeLeaveDetailsModal()">
                            إغلاق
                        </button>
                    </div>

                    <div class="reject-box" id="rejectBox">
                        <form method="POST" id="rejectForm" onsubmit="return showRejectConfirm()">
                            @csrf

                            <label>سبب الرفض</label>
                            <textarea name="reject_reason" id="rejectReasonInput" placeholder="اكتب سبب رفض طلب الإجازة"></textarea>

                            <div class="modal-actions">
                                <button type="submit" class="modal-action-btn modal-reject">
                                    <i class="fas fa-check"></i>
                                    نعم، رفض الطلب
                                </button>

                                <button type="button" class="modal-action-btn modal-cancel" onclick="hideRejectBox()">
                                    إلغاء
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="leave-modal-backdrop show success-modal" id="successModal">
                <div class="leave-modal" style="width:min(440px, 94vw);">
                    <div class="leave-modal-body" style="text-align:center;">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>

                        <h3 style="color:#166534; margin:0 0 10px;">تمت العملية بنجاح</h3>
                        <p style="color:#374151; margin-bottom:18px;">{{ session('success') }}</p>

                        <button type="button" class="modal-action-btn modal-approve" onclick="closeSuccessModal()">
                            حسناً
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <script>
            function openLeaveDetailsModal(data) {
                document.getElementById('modalRequestId').innerText = data.id;
                document.getElementById('modalEmployee').innerText = data.employee;
                document.getElementById('modalEmployeeNumber').innerText = data.employeeNumber;
                document.getElementById('modalLeaveType').innerText = data.leaveType;
                document.getElementById('modalStartDate').innerText = data.startDate;
                document.getElementById('modalEndDate').innerText = data.endDate;
                document.getElementById('modalDays').innerText = data.daysCount;
                document.getElementById('modalRemainingBalance').innerText = data.remainingBalance === '-' ? '-' : `${data.remainingBalance} يوم`;
                document.getElementById('modalUsedBalance').innerText = data.usedPaidDays === '-' ? '-' : `${data.usedPaidDays} يوم من أصل ${data.annualEntitledDays} يوم`;
                document.getElementById('modalStatus').innerText = data.statusName;
                document.getElementById('modalManagerDecision').innerText = data.directManagerStatus || '-';
                document.getElementById('modalManagerBy').innerText = `${data.directManagerBy || '-'} / ${data.directManagerAt || '-'}`;
                document.getElementById('modalHrDecision').innerText = data.hrStatus || '-';
                document.getElementById('modalHrBy').innerText = `${data.hrBy || '-'} / ${data.hrAt || '-'}`;
                document.getElementById('modalReason').innerText = data.reason || '-';
                document.getElementById('modalApprovedBy').innerText = data.approvedBy || '-';
                document.getElementById('modalApprovedAt').innerText = data.approvedAt || '-';
                document.getElementById('modalRejectedBy').innerText = data.rejectedBy || '-';
                document.getElementById('modalRejectedAt').innerText = data.rejectedAt || '-';
                document.getElementById('modalRejectReason').innerText = data.rejectReason || '-';

                const attachmentBox = document.getElementById('modalAttachment');

                if (data.attachmentUrl) {
                    attachmentBox.innerHTML = `<a href="${data.attachmentUrl}" target="_blank" class="mini-btn mini-attachment">
                    <i class="fas fa-paperclip"></i>
                    ${data.attachmentText}
                </a>`;
                } else {
                    attachmentBox.innerText = data.attachmentText;
                }

                const approveForm = document.getElementById('approveForm');
                const rejectForm = document.getElementById('rejectForm');
                const cancelForm = document.getElementById('cancelForm');
                const showRejectBoxBtn = document.getElementById('showRejectBoxBtn');

                approveForm.action = data.approveUrl;
                rejectForm.action = data.rejectUrl;
                cancelForm.action = data.cancelUrl;

                approveForm.style.display = data.canApprove ? 'inline-flex' : 'none';
                showRejectBoxBtn.style.display = data.canReject ? 'inline-flex' : 'none';
                cancelForm.style.display = data.canCancel ? 'inline-flex' : 'none';

                hideRejectBox();

                document.getElementById('leaveDetailsModal').classList.add('show');
            }

            function closeLeaveDetailsModal() {
                document.getElementById('leaveDetailsModal').classList.remove('show');
            }

            function showRejectBox() {
                document.getElementById('rejectBox').classList.add('show');
                document.getElementById('rejectReasonInput').focus();
            }

            function hideRejectBox() {
                document.getElementById('rejectBox').classList.remove('show');
                document.getElementById('rejectReasonInput').value = '';
            }

            function showApproveConfirm() {
                const requestedDays = document.getElementById('modalDays').innerText;
                const remainingBalance = document.getElementById('modalRemainingBalance').innerText;

                return confirm(`هل تريد قبول طلب الإجازة؟\nعدد أيام الطلب: ${requestedDays}\nالرصيد المتاح: ${remainingBalance}`);
            }

            function showRejectConfirm() {
                const reason = document.getElementById('rejectReasonInput').value.trim();

                if (!reason) {
                    alert('يرجى كتابة سبب الرفض أولاً');
                    return false;
                }

                return confirm('هل تريد رفض الطلب؟');
            }

            function closeSuccessModal() {
                const modal = document.getElementById('successModal');

                if (modal) {
                    modal.classList.remove('show');
                }
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeLeaveDetailsModal();
                    closeSuccessModal();
                }
            });

            document.getElementById('leaveDetailsModal')?.addEventListener('click', function (event) {
                if (event.target.id === 'leaveDetailsModal') {
                    closeLeaveDetailsModal();
                }
            });
        </script>

    </div>

@endsection
