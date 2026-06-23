<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ENG-SHADI HR')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f4f1fb;
            color: #2f2f2f;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 285px;
            background: linear-gradient(180deg, #6676de 0%, #7b5cc8 45%, #6e43a3 100%);
            color: white;
            padding: 18px 14px;
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            box-shadow: -8px 0 25px rgba(74, 46, 138, 0.35);
        }

        .logo-box {
            background: rgba(255, 255, 255, 0.10);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-title {
            font-size: 22px;
            font-weight: bold;
        }

        .logo-sub {
            font-size: 13px;
            opacity: .85;
            margin-top: 5px;
        }

        .logo-circle {
            width: 48px;
            height: 48px;
            background: white;
            color: #6d55c8;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 8px 18px rgba(0,0,0,.18);
        }

        .search-box {
            margin: 18px 8px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 13px 45px 13px 15px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.25);
            background: rgba(255,255,255,.14);
            color: white;
            outline: none;
        }

        .search-box input::placeholder {
            color: rgba(255,255,255,.75);
        }

        .search-box i {
            position: absolute;
            right: 16px;
            top: 14px;
            color: rgba(255,255,255,.8);
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: white;
            text-decoration: none;
            padding: 13px 14px;
            margin-bottom: 8px;
            border-radius: 12px;
            font-size: 15px;
            transition: .25s;
        }

        .menu a i {
            width: 34px;
            height: 34px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 12px rgba(0,0,0,.12);
        }

        .menu a:hover,
        .menu a.active {
            background: rgba(255,255,255,.18);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.25);
        }

        .settings-group {
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .settings-title {
            display: flex;
            align-items: center;
            gap: 13px;
            color: white;
            padding: 13px 14px;
            margin-bottom: 8px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: bold;
            background: rgba(255,255,255,.10);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.16);
        }

        .payroll-summary {
            cursor: pointer;
            list-style: none;
            user-select: none;
        }

        .payroll-summary::-webkit-details-marker {
            display: none;
        }

        .payroll-arrow {
            margin-right: auto;
            width: auto !important;
            height: auto !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            transition: .25s;
        }

        details[open] .payroll-arrow {
            transform: rotate(180deg);
        }

        .settings-title i {
            width: 34px;
            height: 34px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 12px rgba(0,0,0,.12);
        }

        .settings-submenu {
            padding-right: 14px;
            border-right: 2px solid rgba(255,255,255,.18);
            margin-right: 16px;
        }

        .settings-submenu a {
            padding: 11px 12px;
            font-size: 14px;
            margin-bottom: 7px;
            background: rgba(0,0,0,.08);
        }

        .settings-submenu a i {
            width: 30px;
            height: 30px;
        }

        .user-card {
            margin-top: 25px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: white;
            color: #6d55c8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-details strong {
            font-size: 15px;
        }

        .user-details small {
            font-size: 12px;
            opacity: .8;
            word-break: break-all;
        }

        .sidebar-footer {
            margin-top: 18px;
            padding: 18px;
            text-align: center;
            background: rgba(0,0,0,.12);
            border-radius: 12px;
            font-weight: bold;
            font-size: 13px;
        }

        .logout-btn {
            width: 100%;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            padding: 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: .3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239,68,68,.35);
        }

        .content {
            flex: 1;
            margin-right: 285px;
            padding: 25px;
        }

        .topbar {
            background: white;
            padding: 18px 24px;
            border-radius: 18px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(90, 64, 160, .08);
        }

        .topbar strong {
            color: #4c3b91;
            font-size: 20px;
        }

        .topbar-user {
            background: #f1ecff;
            color: #5d45b8;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: bold;
        }

        .page-hero {
            background: linear-gradient(135deg, #6676de, #7b4db4);
            border-radius: 22px;
            padding: 34px;
            margin-bottom: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 16px 35px rgba(102, 91, 200, .25);
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,.10);
            border-radius: 50%;
            right: -60px;
            bottom: -80px;
        }

        .hero-info {
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            z-index: 2;
        }

        .hero-icon {
            width: 78px;
            height: 78px;
            border-radius: 20px;
            background: rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.25);
        }

        .hero-info h1 {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .hero-info p {
            opacity: .85;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .hero-btn {
            background: rgba(255,255,255,.18);
            color: white;
            border: 1px solid rgba(255,255,255,.25);
            padding: 13px 20px;
            border-radius: 13px;
            text-decoration: none;
            cursor: pointer;
            font-weight: bold;
        }

        .hero-btn.white {
            background: white;
            color: #5b55d6;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 10px 25px rgba(90, 64, 160, .08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 14px;
            overflow: hidden;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            text-align: right;
        }

        th {
            background: #f2edff;
            color: #4c3b91;
        }

        .btn {
            background: linear-gradient(135deg, #6676de, #7b5cc8);
            color: white;
            border: none;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 10px;
            cursor: pointer;
            display: inline-block;
            box-shadow: 0 6px 14px rgba(105, 89, 205, .25);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e, #15803d);
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #7b5cc8;
            box-shadow: 0 0 0 3px rgba(123, 92, 200, .12);
        }

        label {
            font-weight: bold;
            color: #4c3b91;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .badge-active {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            position: relative;
            overflow: hidden;
            min-height: 150px;
            padding: 28px;
            border-radius: 24px;
            color: white;
            box-shadow: 0 18px 35px rgba(83, 57, 160, .25);
            transform: perspective(1000px) rotateX(5deg);
            transition: .3s;
        }

        .dashboard-card:hover {
            transform: perspective(1000px) rotateX(0deg) translateY(-8px);
        }

        .dashboard-card h3 {
            font-size: 18px;
            margin-bottom: 18px;
        }

        .dashboard-card h1 {
            font-size: 46px;
            font-weight: bold;
        }

        .dashboard-card i {
            position: absolute;
            left: 25px;
            bottom: 20px;
            font-size: 62px;
            opacity: .22;
        }

        .blue { background: linear-gradient(135deg, #6676de, #4f46e5); }
        .green { background: linear-gradient(135deg, #34d399, #059669); }
        .orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .teal { background: linear-gradient(135deg, #14b8a6, #0f766e); }
        .red { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .dark { background: linear-gradient(135deg, #4b5563, #111827); }
        .pink { background: linear-gradient(135deg, #ec4899, #9d174d); }

        .filters-row {
            display: flex;
            gap: 20px;
            align-items: end;
        }

        .filter-search {
            flex: 2;
        }

        .filter-status {
            width: 280px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .filter-actions .btn,
        .filter-actions .btn-danger {
            min-width: 90px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-search input,
        .filter-status select {
            width: 100%;
        }

        .pagination-wrapper {
            margin-top: 25px;
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper nav {
            display: flex;
            gap: 8px;
        }

        @media(max-width:900px) {
            .sidebar {
                position: relative;
                width: 100%;
            }

            .layout {
                display: block;
            }

            .content {
                margin-right: 0;
            }

            .page-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .filters-row {
                flex-direction: column;
            }

            .filter-search,
            .filter-status {
                width: 100%;
            }

            .filter-actions {
                width: 100%;
            }

            .filter-actions .btn,
            .filter-actions .btn-danger {
                flex: 1;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <div class="logo-box">
            <div>
                <div class="logo-title">ENG-SHADI</div>
                <div class="logo-sub">نظام الموارد البشرية</div>
            </div>

            <div class="logo-circle">ES</div>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="البحث في القائمة">
        </div>
        <nav class="menu">

            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-table-cells-large"></i>
                الرئيسية
            </a>

            @if(auth()->user()->hasPermission('employees.view'))
                <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    الموظفين
                </a>
            @endif

            @if(auth()->user()->hasPermission('departments.view'))
                <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    الأقسام
                </a>
            @endif

            @if(auth()->user()->hasPermission('positions.view'))
                <a href="{{ route('positions.index') }}" class="{{ request()->routeIs('positions.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i>
                    الوظائف
                </a>
            @endif

            @if(auth()->user()->hasPermission('attendances.view'))
                <a href="{{ route('attendances.index') }}" class="{{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i>
                    الحضور والانصراف
                </a>
            @endif
            @if(auth()->user()->hasPermission('leave_balances.view'))
                <a href="{{ route('leave-balances.index') }}" class="sidebar-link {{ request()->routeIs('leave-balances.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>أرصدة الإجازات</span>
                </a>
            @endif


            @if(auth()->user()->hasPermission('leave_requests.view'))
                <a href="{{ route('leave-requests.index') }}" class="{{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-days"></i>
                    الإجازات
                </a>
            @endif

            {{-- Payroll sidebar dropdown - Permission Ready --}}
            @if(
                auth()->user()->hasPermission('payroll_periods.view') ||
                auth()->user()->hasPermission('payroll_reports.view') ||
                auth()->user()->hasPermission('salary_advances.view') ||
                auth()->user()->hasPermission('employee_deductions.view') ||
                auth()->user()->hasPermission('employee_suspensions.view') ||
                auth()->user()->hasPermission('payroll_settings.view')
            )
                <details class="settings-group"
                    {{ request()->routeIs('payroll-periods.*') ||
                       request()->routeIs('payroll-reports.*') ||
                       request()->routeIs('salary-advances.*') ||
                       request()->routeIs('employee-deductions.*') ||
                       request()->routeIs('employee-suspensions.*') ||
                       request()->routeIs('payroll-settings.*') ? 'open' : '' }}>

                    <summary class="settings-title payroll-summary">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>الرواتب</span>
                        <i class="fas fa-chevron-down payroll-arrow"></i>
                    </summary>

                    <div class="settings-submenu">
                        @if(auth()->user()->hasPermission('payroll_periods.view'))
                            <a href="{{ route('payroll-periods.index') }}" class="{{ request()->routeIs('payroll-periods.*') ? 'active' : '' }}">
                                <i class="fas fa-money-check-dollar"></i>
                                <span>مسير الرواتب</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('payroll_reports.view'))
                            <a href="{{ route('payroll-reports.index') }}" class="{{ request()->routeIs('payroll-reports.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie"></i>
                                <span>تقارير الرواتب</span>
                            </a>
                        @endif
                            @if(auth()->user()->hasPermission('payroll_bank_transfers.view'))
                                <a href="{{ route('payroll-bank-transfers.index') }}"
                                   class="{{ request()->routeIs('payroll-bank-transfers.*') ? 'active' : '' }}">
                                    <i class="fas fa-building-columns"></i>
                                    كشف تحويل الرواتب
                                </a>
                            @endif


                            @if(auth()->user()->hasPermission('payroll_bank_transfer_batches.view'))
                                <a href="{{ route('payroll-bank-transfer-batches.index') }}"
                                   class="{{ request()->routeIs('payroll-bank-transfer-batches.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-check-dollar"></i>
                                    دفعات تحويل الرواتب
                                </a>
                            @endif


                        @if(auth()->user()->hasPermission('salary_advances.view'))
                            <a href="{{ route('salary-advances.index') }}" class="{{ request()->routeIs('salary-advances.*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-dollar"></i>
                                <span>سلف الموظفين</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('employee_deductions.view'))
                            <a href="{{ route('employee-deductions.index') }}" class="{{ request()->routeIs('employee-deductions.*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>استقطاعات الموظفين</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('employee_suspensions.view'))
                            <a href="{{ route('employee-suspensions.index') }}" class="{{ request()->routeIs('employee-suspensions.*') ? 'active' : '' }}">
                                <i class="fas fa-user-slash"></i>
                                <span>إيقافات الموظفين</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('salary_payment_methods.view'))
                            <a href="{{ route('salary-payment-methods.index') }}" class="{{ request()->routeIs('salary-payment-methods.*') ? 'active' : '' }}">
                                <i class="fas fa-money-check-dollar"></i>
                                <span>طرق صرف الراتب</span>
                            </a>
                        @endif

                        @can('deduction_types.view')
                            <a href="{{ route('deduction-types.index') }}" class="submenu-link {{ request()->routeIs('deduction-types.*') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i>
                                <span>أنواع الاستقطاعات</span>
                            </a>
                        @endcan
                        @if(auth()->user()->hasPermission('payroll_groups.view'))
                            <a href="{{ route('payroll-groups.index') }}" class="{{ request()->routeIs('payroll-groups.*') ? 'active' : '' }}">
                                <i class="fas fa-users-gear"></i>
                                <span>مجموعات الرواتب</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('cost_centers.view'))
                            <a href="{{ route('cost-centers.index') }}" class="{{ request()->routeIs('cost-centers.*') ? 'active' : '' }}">
                                <i class="fas fa-building-circle-check"></i>
                                <span>مراكز التكلفة</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('payroll_settings.view'))
                            <a href="{{ route('payroll-settings.edit') }}" class="{{ request()->routeIs('payroll-settings.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders"></i>
                                <span>إعدادات الرواتب</span>
                            </a>
                        @endif
                            @if(auth()->user()->hasPermission('payroll_period_logs.view'))
                                <a href="{{ route('payroll-period-logs.index') }}"
                                   class="{{ request()->routeIs('payroll-period-logs.*') ? 'active' : '' }}">
                                    <i class="fas fa-clock-rotate-left"></i>
                                    سجل حركات المسير
                                </a>
                            @endif


                    </div>
                </details>
            @endif

            @if(auth()->user()->hasPermission('reports.view'))
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    التقارير
                </a>
            @endif

            @if(auth()->user()->hasPermission('audit_logs.view'))
                <a href="{{ route('audit-logs.index') }}" class="{{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    سجل النشاط
                </a>
            @endif
            @if(auth()->user()->hasPermission('documents.view'))
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <i class="fas fa-file-shield"></i>
                    متابعة الوثائق
                </a>
            @endif
            @if(
                auth()->user()->hasPermission('settings.view') ||
                auth()->user()->hasPermission('users.view') ||
                auth()->user()->hasPermission('roles.view') ||
                auth()->user()->hasPermission('nationalities.view') ||
                auth()->user()->hasPermission('leave_policies.view') ||
                auth()->user()->hasPermission('payroll_settings.view')
            )
                <div class="settings-group">
                    <div class="settings-title">
                        <i class="fas fa-gear"></i>
                        <span>الإعدادات</span>
                    </div>

                    <div class="settings-submenu">
                        @if(auth()->user()->hasPermission('users.view'))
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span>المستخدمين</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('payroll_settings.view'))
                            <a href="{{ route('payroll-settings.edit') }}" class="{{ request()->routeIs('payroll-settings.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders"></i>
                                <span>إعدادات الرواتب</span>
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('leave_requests.manager_approval'))
                            <a href="{{ route('manager-leave-approvals.index') }}" class="sidebar-link">
                                <i class="fas fa-user-check"></i>
                                <span>موافقات المدير المباشر</span>
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('leave_requests.hr_approval'))
                            <a href="{{ route('hr-leave-approvals.index') }}" class="sidebar-link">
                                <i class="fas fa-user-shield"></i>
                                <span>موافقات الموارد البشرية</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('roles.view'))
                            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="fas fa-user-lock"></i>
                                <span>الأدوار والصلاحيات</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('nationalities.view'))
                            <a href="{{ route('nationalities.index') }}" class="{{ request()->routeIs('nationalities.*') ? 'active' : '' }}">
                                <i class="fas fa-globe"></i>
                                <span>الجنسيات</span>
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('leave_types.view'))
                            <a href="{{ route('leave-types.index') }}" class="{{ request()->routeIs('leave-types.*') ? 'active' : '' }}">
                                <i class="fas fa-list-check"></i>
                                <span>أنواع الإجازات</span>
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('official_holidays.view'))
                            <a href="{{ route('official-holidays.index') }}" class="{{ request()->routeIs('official-holidays.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-star"></i>
                                <span>الإجازات الرسمية</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('leave_policies.view'))
                            <a href="{{ route('leave-policies.index') }}" class="{{ request()->routeIs('leave-policies.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders"></i>
                                <span>سياسات الإجازات</span>
                            </a>
                        @endif


                    </div>
                </div>
            @endif

        </nav>

        <div class="user-card">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="user-details">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->email }}</small>
            </div>
        </div>

        <div class="sidebar-footer">
            ENG-SHADI 2026 ©
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="logout-btn">
                <i class="fas fa-right-from-bracket"></i>
                تسجيل الخروج
            </button>
        </form>

    </aside>

    <main class="content">

        <div class="topbar">
            <strong>@yield('page-title', 'لوحة التحكم')</strong>

            <div class="topbar-user">
                مرحباً، {{ auth()->user()->name ?? 'User' }}
            </div>
        </div>
        @if ($errors->any())
            <div class="card" style="margin-bottom:20px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                <strong>يوجد أخطاء:</strong>

                <ul style="margin-top:10px; margin-bottom:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="card" style="margin-bottom:20px; background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="card" style="margin-bottom:20px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')

    </main>

</div>
<script>
    function logExportAction(type) {
        fetch("{{ route('audit-logs.export-action') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                type: type,
                page: document.title,
                module: window.location.pathname.replace("/", "") || "dashboard"
            })
        }).catch(function () {
            console.log("Audit export log failed");
        });
    }

    function exportTableToExcel() {
        logExportAction("excel");

        let table = document.querySelector("table");

        if (!table) {
            alert("لا يوجد جدول للتصدير");
            return;
        }

        let html = table.outerHTML;
        let blob = new Blob([html], {
            type: "application/vnd.ms-excel"
        });

        let a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = "table-data.xls";
        a.click();
    }

    function exportTableToWord() {
        logExportAction("word");

        let table = document.querySelector("table");

        if (!table) {
            alert("لا يوجد جدول للتصدير");
            return;
        }

        let html = `
            <html>
            <head>
                <meta charset="UTF-8">
            </head>
            <body dir="rtl">
                ${table.outerHTML}
            </body>
            </html>
        `;

        let blob = new Blob([html], {
            type: "application/msword"
        });

        let a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = "table-data.doc";
        a.click();
    }

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector(".sidebar");

        if (!sidebar) {
            return;
        }

        const savedScroll = sessionStorage.getItem("sidebar_scroll_top");

        if (savedScroll !== null) {
            sidebar.scrollTop = parseInt(savedScroll, 10);
        }

        sidebar.addEventListener("scroll", function () {
            sessionStorage.setItem("sidebar_scroll_top", sidebar.scrollTop);
        });

        const sidebarLinks = sidebar.querySelectorAll("a");

        sidebarLinks.forEach(function (link) {
            link.addEventListener("click", function () {
                sessionStorage.setItem("sidebar_scroll_top", sidebar.scrollTop);
            });
        });
    });
</script>
</body>
</html>
