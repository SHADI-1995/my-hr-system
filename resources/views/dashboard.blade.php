@extends('layouts.hr')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة تحكم الموارد البشرية')

@section('content')

    @php
        $iqamasValid = $documentsStats['iqamas_valid'] ?? 0;
        $iqamasNear = $documentsStats['iqamas_near_expiry'] ?? 0;
        $iqamasExpired = $documentsStats['iqamas_expired'] ?? 0;
        $iqamasTotalReal = $iqamasValid + $iqamasNear + $iqamasExpired;
        $iqamasTotal = max($iqamasTotalReal, 1);

        $passportsValid = $documentsStats['passports_valid'] ?? 0;
        $passportsNear = $documentsStats['passports_near_expiry'] ?? 0;
        $passportsExpired = $documentsStats['passports_expired'] ?? 0;
        $passportsTotalReal = $passportsValid + $passportsNear + $passportsExpired;
        $passportsTotal = max($passportsTotalReal, 1);

        $healthCardsValid = $documentsStats['health_cards_valid'] ?? 0;
        $healthCardsNear = $documentsStats['health_cards_near_expiry'] ?? 0;
        $healthCardsExpired = $documentsStats['health_cards_expired'] ?? 0;
        $healthCardsTotalReal = $healthCardsValid + $healthCardsNear + $healthCardsExpired;
        $healthCardsTotal = max($healthCardsTotalReal, 1);

        $iqamasPercentValid = $iqamasTotalReal ? round(($iqamasValid / $iqamasTotal) * 100) : 0;
        $iqamasPercentNear = $iqamasTotalReal ? round(($iqamasNear / $iqamasTotal) * 100) : 0;
        $iqamasPercentExpired = $iqamasTotalReal ? round(($iqamasExpired / $iqamasTotal) * 100) : 0;

        $passportsPercentValid = $passportsTotalReal ? round(($passportsValid / $passportsTotal) * 100) : 0;
        $passportsPercentNear = $passportsTotalReal ? round(($passportsNear / $passportsTotal) * 100) : 0;
        $passportsPercentExpired = $passportsTotalReal ? round(($passportsExpired / $passportsTotal) * 100) : 0;

        $healthCardsPercentValid = $healthCardsTotalReal ? round(($healthCardsValid / $healthCardsTotal) * 100) : 0;
        $healthCardsPercentNear = $healthCardsTotalReal ? round(($healthCardsNear / $healthCardsTotal) * 100) : 0;
        $healthCardsPercentExpired = $healthCardsTotalReal ? round(($healthCardsExpired / $healthCardsTotal) * 100) : 0;
    @endphp

    <div class="dashboard-page">

        <div class="dashboard-header-card">
            <div>
                <h2>لوحة تحكم الموارد البشرية</h2>

            </div>

            @if(auth()->user()->hasPermission('documents.view'))
                <a href="{{ route('documents.index') }}" class="soft-main-btn">
                    <i class="fas fa-file-shield"></i>
                    متابعة الوثائق
                </a>
            @endif
        </div>

        <div class="dashboard-grid compact-dashboard-grid">

            @if(auth()->user()->hasPermission('employees.view'))
                <div class="dashboard-card blue compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>إجمالي الموظفين</span>
                        <strong>{{ $employeesCount }}</strong>
                    </div>
                    <i class="fas fa-users"></i>
                </div>

                <div class="dashboard-card green compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>الموظفين النشطين</span>
                        <strong>{{ $activeEmployees }}</strong>
                    </div>
                    <i class="fas fa-user-check"></i>
                </div>
            @endif

            @if(auth()->user()->hasPermission('departments.view'))
                <div class="dashboard-card orange compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>الأقسام</span>
                        <strong>{{ $departmentsCount }}</strong>
                    </div>
                    <i class="fas fa-building"></i>
                </div>
            @endif

            @if(auth()->user()->hasPermission('positions.view'))
                <div class="dashboard-card purple compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>الوظائف</span>
                        <strong>{{ $positionsCount }}</strong>
                    </div>
                    <i class="fas fa-briefcase"></i>
                </div>
            @endif

            @if(auth()->user()->hasPermission('attendances.view'))
                <div class="dashboard-card teal compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>حضور اليوم</span>
                        <strong>{{ $todayAttendances }}</strong>
                    </div>
                    <i class="fas fa-clock"></i>
                </div>
            @endif

            @if(auth()->user()->hasPermission('leave_requests.view'))
                <div class="dashboard-card red compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>إجازات معلقة</span>
                        <strong>{{ $pendingLeaves }}</strong>
                    </div>
                    <i class="fas fa-calendar-days"></i>
                </div>
            @endif

            @if(auth()->user()->hasPermission('payrolls.view'))
                <div class="dashboard-card dark compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>رواتب مدفوعة</span>
                        <strong>{{ $paidPayrolls }}</strong>
                    </div>
                    <i class="fas fa-money-bill-wave"></i>
                </div>

                <div class="dashboard-card pink compact-card soft-stat-card">
                    <div class="stat-card-content">
                        <span>رواتب مسودة</span>
                        <strong>{{ $draftPayrolls }}</strong>
                    </div>
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            @endif

        </div>

        @if(auth()->user()->hasPermission('documents.view'))
            <div class="documents-analytics-section">

                <div class="section-title-row">
                    <div>
                        <h3>
                            <i class="fas fa-chart-pie"></i>
                            إحصائيات الوثائق
                        </h3>

                    </div>

                    <a href="{{ route('documents.index') }}" class="soft-main-btn secondary">
                        عرض التفاصيل
                    </a>
                </div>

                <div class="documents-3d-grid">

                    <div class="document-analytics-card iqama-card">
                        <div class="document-card-header">
                            <div>
                                <h4>الإقامات</h4>
                                <span>إجمالي السجلات: {{ $iqamasTotalReal }}</span>
                            </div>
                            <div class="document-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                        </div>

                        <div class="percent-3d-list">
                            <a href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'valid']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>سارية</span>
                                    <strong>{{ $iqamasPercentValid }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="valid-bar" style="width: {{ $iqamasPercentValid }}%;"></span>
                                </div>
                                <small>{{ $iqamasValid }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'near_expiry']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>قريبة الانتهاء</span>
                                    <strong>{{ $iqamasPercentNear }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="near-bar" style="width: {{ $iqamasPercentNear }}%;"></span>
                                </div>
                                <small>{{ $iqamasNear }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'expired']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>منتهية</span>
                                    <strong>{{ $iqamasPercentExpired }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="expired-bar" style="width: {{ $iqamasPercentExpired }}%;"></span>
                                </div>
                                <small>{{ $iqamasExpired }} سجل</small>
                            </a>
                        </div>
                    </div>

                    <div class="document-analytics-card passport-card">
                        <div class="document-card-header">
                            <div>
                                <h4>الجوازات</h4>
                                <span>إجمالي السجلات: {{ $passportsTotalReal }}</span>
                            </div>
                            <div class="document-icon">
                                <i class="fas fa-passport"></i>
                            </div>
                        </div>

                        <div class="percent-3d-list">
                            <a href="{{ route('documents.index', ['document' => 'passports', 'status' => 'valid']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>سارية</span>
                                    <strong>{{ $passportsPercentValid }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="valid-bar" style="width: {{ $passportsPercentValid }}%;"></span>
                                </div>
                                <small>{{ $passportsValid }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'passports', 'status' => 'near_expiry']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>قريبة الانتهاء</span>
                                    <strong>{{ $passportsPercentNear }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="near-bar" style="width: {{ $passportsPercentNear }}%;"></span>
                                </div>
                                <small>{{ $passportsNear }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'passports', 'status' => 'expired']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>منتهية</span>
                                    <strong>{{ $passportsPercentExpired }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="expired-bar" style="width: {{ $passportsPercentExpired }}%;"></span>
                                </div>
                                <small>{{ $passportsExpired }} سجل</small>
                            </a>
                        </div>
                    </div>

                    <div class="document-analytics-card health-card">
                        <div class="document-card-header">
                            <div>
                                <h4>الكروت الصحية</h4>
                                <span>إجمالي السجلات: {{ $healthCardsTotalReal }}</span>
                            </div>
                            <div class="document-icon">
                                <i class="fas fa-notes-medical"></i>
                            </div>
                        </div>

                        <div class="percent-3d-list">
                            <a href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'valid']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>سارية</span>
                                    <strong>{{ $healthCardsPercentValid }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="valid-bar" style="width: {{ $healthCardsPercentValid }}%;"></span>
                                </div>
                                <small>{{ $healthCardsValid }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'near_expiry']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>قريبة الانتهاء</span>
                                    <strong>{{ $healthCardsPercentNear }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="near-bar" style="width: {{ $healthCardsPercentNear }}%;"></span>
                                </div>
                                <small>{{ $healthCardsNear }} سجل</small>
                            </a>

                            <a href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'expired']) }}" class="percent-row">
                                <div class="percent-info">
                                    <span>منتهية</span>
                                    <strong>{{ $healthCardsPercentExpired }}%</strong>
                                </div>
                                <div class="bar-3d">
                                    <span class="expired-bar" style="width: {{ $healthCardsPercentExpired }}%;"></span>
                                </div>
                                <small>{{ $healthCardsExpired }} سجل</small>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <div class="charts-grid">

            <div class="card chart-card">
                <h3>الموظفين حسب الأقسام</h3>
                <canvas id="employeesByDepartmentChart"></canvas>
            </div>

            <div class="card chart-card">
                <h3>حالات الحضور</h3>
                <canvas id="attendanceStatusChart"></canvas>
            </div>

            <div class="card chart-card">
                <h3>حالات الإجازات</h3>
                <canvas id="leaveStatusChart"></canvas>
            </div>

            <div class="card chart-card">
                <h3>إجمالي الرواتب حسب الشهر</h3>
                <canvas id="payrollChart"></canvas>
            </div>

        </div>

        <div class="card quick-links-card">
            <h3>روابط سريعة</h3>

            <div class="quick-links">

                @if(auth()->user()->hasPermission('employees.view'))
                    <a href="{{ route('employees.index') }}" class="btn">الموظفين</a>
                @endif

                @if(auth()->user()->hasPermission('departments.view'))
                    <a href="{{ route('departments.index') }}" class="btn">الأقسام</a>
                @endif

                @if(auth()->user()->hasPermission('positions.view'))
                    <a href="{{ route('positions.index') }}" class="btn">الوظائف</a>
                @endif

                @if(auth()->user()->hasPermission('attendances.view'))
                    <a href="{{ route('attendances.index') }}" class="btn">الحضور</a>
                @endif

                @if(auth()->user()->hasPermission('leave_requests.view'))
                    <a href="{{ route('leave-requests.index') }}" class="btn">الإجازات</a>
                @endif

                @if(auth()->user()->hasPermission('payrolls.view'))
                    <a href="{{ route('payrolls.index') }}" class="btn">الرواتب</a>
                @endif

                @if(auth()->user()->hasPermission('documents.view'))
                    <a href="{{ route('documents.index') }}" class="btn">متابعة الوثائق</a>
                @endif

            </div>
        </div>

    </div>

    <style>
        .dashboard-page {
            background:
                radial-gradient(circle at top right, rgba(109, 93, 252, .07), transparent 28%),
                radial-gradient(circle at bottom left, rgba(20, 184, 166, .05), transparent 30%);
        }

        .dashboard-header-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 22px 24px;
            margin-bottom: 22px;
            border-radius: 24px;
            background: linear-gradient(135deg, #ffffff, #f8f7ff);
            border: 1px solid #eeeafd;
            box-shadow: 0 18px 45px rgba(76, 59, 145, .08);
        }

        .dashboard-header-card h2 {
            margin: 0;
            color: #3f3a68;
            font-size: 24px;
        }

        .dashboard-header-card p {
            margin: 7px 0 0;
            color: #7a7690;
            font-size: 14px;
        }

        .soft-main-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            text-decoration: none;
            border-radius: 14px;
            padding: 12px 16px;
            background: #6d5dfc;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(109, 93, 252, .22);
        }

        .soft-main-btn.secondary {
            background: #ffffff;
            color: #5b50cc;
            border: 1px solid #e6e2ff;
            box-shadow: 0 8px 20px rgba(109, 93, 252, .10);
        }

        .compact-dashboard-grid {
            gap: 14px;
            margin-bottom: 24px;
        }

        .compact-card.soft-stat-card {
            position: relative;
            min-height: 104px !important;
            padding: 16px 18px !important;
            border-radius: 20px !important;
            background: #ffffff !important;
            color: #374151 !important;
            border: 1px solid #edf0f7;
            box-shadow:
                0 12px 25px rgba(31, 41, 55, .08),
                inset 0 1px 0 rgba(255, 255, 255, .85);
            transform: perspective(900px) rotateX(2deg);
            transition: .25s ease;
            overflow: hidden;
        }

        .compact-card.soft-stat-card:hover {
            transform: perspective(900px) rotateX(0deg) translateY(-3px);
            box-shadow: 0 16px 30px rgba(31, 41, 55, .11);
        }

        .compact-card.soft-stat-card::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 6px;
            height: 100%;
            opacity: .85;
        }

        .compact-card.blue::before { background: #93c5fd; }
        .compact-card.green::before { background: #86efac; }
        .compact-card.orange::before { background: #fcd34d; }
        .compact-card.purple::before { background: #c4b5fd; }
        .compact-card.teal::before { background: #5eead4; }
        .compact-card.red::before { background: #fca5a5; }
        .compact-card.dark::before { background: #9ca3af; }
        .compact-card.pink::before { background: #f9a8d4; }

        .stat-card-content span {
            display: block;
            font-size: 13px;
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-card-content strong {
            display: block;
            font-size: 28px;
            line-height: 1;
            color: #2f2a55;
        }

        .compact-card.soft-stat-card i {
            position: absolute;
            inset-inline-end: 18px;
            bottom: 14px;
            font-size: 34px !important;
            opacity: .16 !important;
            color: #4b5563 !important;
        }

        .documents-analytics-section {
            background: #ffffff;
            border: 1px solid #eeeafd;
            border-radius: 26px;
            padding: 22px;
            margin-bottom: 28px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, 0.08);
        }

        .section-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .section-title-row h3 {
            margin: 0;
            color: #3f3a68;
            font-size: 22px;
        }

        .section-title-row p {
            margin: 6px 0 0;
            color: #7a7690;
            font-size: 14px;
        }

        .documents-3d-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(260px, 1fr));
            gap: 18px;
        }

        .document-analytics-card {
            position: relative;
            border-radius: 24px;
            padding: 20px;
            color: #3b3b4f;
            overflow: hidden;
            min-height: 278px;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid #edf0f7;
            transform: perspective(1000px) rotateX(3deg);
            box-shadow:
                0 18px 32px rgba(31, 41, 55, .09),
                inset 0 1px 0 rgba(255, 255, 255, .95);
            transition: .25s ease;
        }

        .document-analytics-card:hover {
            transform: perspective(1000px) rotateX(0deg) translateY(-4px);
        }

        .document-analytics-card::before {
            content: "";
            position: absolute;
            top: -55px;
            inset-inline-end: -55px;
            width: 145px;
            height: 145px;
            border-radius: 50%;
            opacity: .18;
        }

        .iqama-card::before { background: #60a5fa; }
        .passport-card::before { background: #c084fc; }
        .health-card::before { background: #86efac; }

        .iqama-card { box-shadow: 0 18px 32px rgba(37, 99, 235, .09), inset 0 1px 0 rgba(255,255,255,.95); }
        .passport-card { box-shadow: 0 18px 32px rgba(124, 58, 237, .09), inset 0 1px 0 rgba(255,255,255,.95); }
        .health-card { box-shadow: 0 18px 32px rgba(22, 163, 74, .09), inset 0 1px 0 rgba(255,255,255,.95); }

        .document-card-header {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            margin-bottom: 18px;
        }

        .document-card-header h4 {
            margin: 0;
            font-size: 21px;
            color: #3f3a68;
        }

        .document-card-header span {
            display: block;
            margin-top: 5px;
            color: #7a7690;
            font-size: 13px;
        }

        .document-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: #f6f5ff;
            border: 1px solid #ebe8ff;
        }

        .document-icon i {
            font-size: 24px;
            color: #6d5dfc;
        }

        .percent-3d-list {
            position: relative;
            z-index: 2;
            display: grid;
            gap: 12px;
        }

        .percent-row {
            color: #3f3a68;
            text-decoration: none;
            display: block;
            background: #fbfcff;
            border: 1px solid #edf0f7;
            border-radius: 16px;
            padding: 12px;
            box-shadow:
                0 8px 18px rgba(31, 41, 55, .05),
                inset 0 1px 0 rgba(255,255,255,.90);
            transition: .2s ease;
        }

        .percent-row:hover {
            background: #ffffff;
            transform: translateY(-2px);
        }

        .percent-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 800;
        }

        .percent-info strong {
            font-size: 18px;
            color: #2f2a55;
        }

        .bar-3d {
            position: relative;
            height: 11px;
            background: #edf0f7;
            border-radius: 999px;
            overflow: hidden;
            box-shadow: inset 0 2px 5px rgba(31, 41, 55, .12);
        }

        .bar-3d span {
            display: block;
            height: 100%;
            min-width: 4px;
            border-radius: 999px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 4px 8px rgba(31,41,55,.12);
        }

        .valid-bar {
            background: linear-gradient(90deg, #86efac, #22c55e);
        }

        .near-bar {
            background: linear-gradient(90deg, #fde68a, #f59e0b);
        }

        .expired-bar {
            background: linear-gradient(90deg, #fecaca, #ef4444);
        }

        .percent-row small {
            display: block;
            margin-top: 6px;
            color: #7a7690;
            font-size: 12px;
            font-weight: 700;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 22px;
            margin-bottom: 28px;
        }

        .chart-card {
            min-height: 340px;
            border-radius: 22px !important;
            border: 1px solid #edf0f7;
            box-shadow: 0 14px 30px rgba(31, 41, 55, .07);
        }

        .chart-card h3 {
            color: #3f3a68;
            margin-bottom: 18px;
        }

        .quick-links-card {
            border-radius: 22px !important;
            border: 1px solid #edf0f7;
            box-shadow: 0 14px 30px rgba(31, 41, 55, .07);
        }

        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
        }

        @media (max-width: 1100px) {
            .documents-3d-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .dashboard-header-card {
                padding: 18px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .document-analytics-card {
                min-height: auto;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const departmentLabels = @json($employeesByDepartment->pluck('name'));
        const departmentData = @json($employeesByDepartment->pluck('employees_count'));
        @if(auth()->user()->hasPermission('employees.view') && auth()->user()->hasPermission('departments.view'))
        new Chart(document.getElementById('employeesByDepartmentChart'), {
            type: 'bar',
            data: {
                labels: departmentLabels,
                datasets: [{
                    label: 'عدد الموظفين',
                    data: departmentData,
                    backgroundColor: 'rgba(109, 93, 252, 0.28)',
                    borderColor: 'rgba(109, 93, 252, 0.75)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
        @endif

        const attendanceLabels = @json($attendanceStatuses->keys());
        const attendanceData = @json($attendanceStatuses->values());
        @if(auth()->user()->hasPermission('attendances.view'))
        new Chart(document.getElementById('attendanceStatusChart'), {
            type: 'doughnut',
            data: {
                labels: attendanceLabels,
                datasets: [{
                    label: 'حالات الحضور',
                    data: attendanceData,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.35)',
                        'rgba(245, 158, 11, 0.35)',
                        'rgba(239, 68, 68, 0.35)',
                        'rgba(109, 93, 252, 0.35)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true
            }
        });
        @endif

        const leaveLabels = @json($leaveStatuses->keys());
        const leaveData = @json($leaveStatuses->values());
        @if(auth()->user()->hasPermission('leave_requests.view'))
        new Chart(document.getElementById('leaveStatusChart'), {
            type: 'pie',
            data: {
                labels: leaveLabels,
                datasets: [{
                    label: 'حالات الإجازات',
                    data: leaveData,
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.35)',
                        'rgba(34, 197, 94, 0.35)',
                        'rgba(239, 68, 68, 0.35)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true
            }
        });
        @endif

        const payrollLabels = @json($payrollTotals->pluck('month'));
        const payrollData = @json($payrollTotals->pluck('total'));
        @if(auth()->user()->hasPermission('payrolls.view'))
        new Chart(document.getElementById('payrollChart'), {
            type: 'line',
            data: {
                labels: payrollLabels,
                datasets: [{
                    label: 'إجمالي الرواتب',
                    data: payrollData,
                    tension: 0.4,
                    borderWidth: 3,
                    borderColor: 'rgba(109, 93, 252, 0.75)',
                    backgroundColor: 'rgba(109, 93, 252, 0.12)',
                    fill: true,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true
            }
        });
        @endif
    </script>

@endsection
