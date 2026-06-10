@extends('layouts.hr')

@section('title', 'تقارير إدارة الإجازات')
@section('page-title', 'تقارير إدارة الإجازات')

@section('content')

    <style>
        .leave-reports-hub,
        .leave-reports-hub * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .reports-hub-hero {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            border-radius: 26px;
            padding: 28px;
            color: #fff;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            box-shadow: 0 22px 55px rgba(76, 59, 145, .22);
        }

        .reports-hub-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 900;
        }

        .reports-hub-hero p {
            margin: 0;
            opacity: .92;
            font-weight: 700;
            line-height: 1.8;
        }

        .reports-hub-icon {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .hub-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, .08);
        }

        .hub-title {
            margin: 0 0 18px;
            color: #4c3b91;
            font-size: 20px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hub-title span {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #f1edff;
            color: #6d5bd0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .hub-filter-grid {
            display: grid;
            grid-template-columns: 1.2fr 1.4fr 150px;
            gap: 14px;
            align-items: end;
        }

        .hub-form-group label {
            display: block;
            color: #4c3b91;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .hub-form-group input,
        .hub-form-group select {
            width: 100%;
            height: 46px;
            border-radius: 15px;
            border: 1px solid #ddd6fe;
            background: #fff;
            padding: 0 13px;
            color: #111827;
            font-weight: 800;
            outline: none;
        }

        .hub-btn {
            height: 46px;
            border: none;
            border-radius: 15px;
            background: #6d5bd0;
            color: #fff;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 12px 28px rgba(109, 91, 208, .22);
        }

        .hub-btn.excel {
            background: #16a34a;
            box-shadow: 0 12px 28px rgba(22,163,74,.18);
        }

        .selected-report-box {
            display: none;
            margin-top: 18px;
            padding: 16px;
            border-radius: 18px;
            background: #f8f7ff;
            border: 1px solid #e7e0ff;
        }

        .selected-report-box.show {
            display: block;
        }

        .selected-report-box h3 {
            margin: 0 0 8px;
            color: #111827;
            font-size: 18px;
            font-weight: 900;
        }

        .selected-report-box p {
            margin: 0 0 12px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.8;
        }

        .selected-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .report-card {
            border: 1px solid #eeeafc;
            border-radius: 20px;
            padding: 16px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(76, 59, 145, .06);
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 205px;
        }

        .report-category {
            align-self: flex-start;
            background: #f1edff;
            color: #4c3b91;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
        }

        .report-card h3 {
            margin: 0;
            color: #111827;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.6;
        }

        .report-card p {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.8;
            flex: 1;
        }

        .report-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .report-actions a {
            height: 38px;
            border-radius: 13px;
            background: #6d5bd0;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
        }

        .report-actions a.excel-link {
            background: #16a34a;
        }

        .empty-box {
            text-align: center;
            padding: 32px;
            color: #6b7280;
            font-weight: 900;
            background: #f9fafb;
            border-radius: 18px;
            border: 1px dashed #ddd6fe;
        }

        @media (max-width: 1050px) {
            .reports-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hub-filter-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .reports-hub-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .reports-grid {
                grid-template-columns: 1fr;
            }

            .hub-btn,
            .selected-actions a {
                width: 100%;
            }
        }
    </style>

    <div class="leave-reports-hub">

        <div class="reports-hub-hero">
            <div>
                <h1>تقارير إدارة الإجازات</h1>
                <p>اختر التقرير من القائمة المنسدلة أو ابحث عن تقرير معين، ويمكنك تصدير أي تقرير إلى Excel مباشرة.</p>
            </div>

            <div class="reports-hub-icon">📊</div>
        </div>

        <div class="hub-card">
            <h2 class="hub-title">
                <span>▾</span>
                اختيار التقرير
            </h2>

            <form method="GET" action="{{ route('leave-reports.hub') }}">
                <div class="hub-filter-grid">
                    <div class="hub-form-group">
                        <label>بحث عن تقرير</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="مثال: بانتظار المدير، الرصيد، المرفقات، HR">
                    </div>

                    <div class="hub-form-group">
                        <label>اختر التقرير</label>
                        <select name="report_key" id="reportSelect">
                            <option value="">اختر تقرير من القائمة</option>
                            @foreach($reports as $report)
                                <option
                                    value="{{ $report['key'] }}"
                                    data-url="{{ $report['route'] }}"
                                    data-export-url="{{ route('leave-reports.hub.export', $report['key']) }}"
                                    data-title="{{ $report['title'] }}"
                                    data-description="{{ $report['description'] }}"
                                    {{ $selectedReport === $report['key'] ? 'selected' : '' }}>
                                    {{ $report['category'] }} — {{ $report['title'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="hub-btn">
                        🔎 عرض
                    </button>
                </div>
            </form>

            <div id="selectedReportBox" class="selected-report-box {{ $currentReport ? 'show' : '' }}">
                <h3 id="selectedReportTitle">{{ $currentReport['title'] ?? 'اختر تقريرًا' }}</h3>
                <p id="selectedReportDescription">{{ $currentReport['description'] ?? '' }}</p>

                <div class="selected-actions">
                    <a
                        id="openSelectedReport"
                        href="{{ $currentReport['route'] ?? '#' }}"
                        class="hub-btn"
                        style="width:190px;">
                        فتح التقرير
                    </a>

                    <a
                        id="exportSelectedReport"
                        href="{{ $currentReport ? route('leave-reports.hub.export', $currentReport['key']) : '#' }}"
                        class="hub-btn excel"
                        style="width:190px;">
                        تحميل Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="hub-card">
            <h2 class="hub-title">
                <span>☰</span>
                جميع تقارير الإجازات
            </h2>

            @if(count($filteredReports))
                <div class="reports-grid">
                    @foreach($filteredReports as $report)
                        <div class="report-card">
                            <div class="report-category">{{ $report['category'] }}</div>
                            <h3>{{ $report['title'] }}</h3>
                            <p>{{ $report['description'] }}</p>

                            <div class="report-actions">
                                <a href="{{ $report['route'] }}">فتح التقرير</a>
                                <a href="{{ route('leave-reports.hub.export', $report['key']) }}" class="excel-link">
                                    Excel
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    لا توجد تقارير مطابقة للبحث الحالي.
                </div>
            @endif
        </div>

    </div>

    <script>
        const reportSelect = document.getElementById('reportSelect');
        const selectedReportBox = document.getElementById('selectedReportBox');
        const selectedReportTitle = document.getElementById('selectedReportTitle');
        const selectedReportDescription = document.getElementById('selectedReportDescription');
        const openSelectedReport = document.getElementById('openSelectedReport');
        const exportSelectedReport = document.getElementById('exportSelectedReport');

        reportSelect?.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                selectedReportBox.classList.remove('show');
                openSelectedReport.href = '#';
                exportSelectedReport.href = '#';
                return;
            }

            selectedReportTitle.innerText = selectedOption.dataset.title || selectedOption.text;
            selectedReportDescription.innerText = selectedOption.dataset.description || '';
            openSelectedReport.href = selectedOption.dataset.url || '#';
            exportSelectedReport.href = selectedOption.dataset.exportUrl || '#';
            selectedReportBox.classList.add('show');
        });
    </script>

@endsection
