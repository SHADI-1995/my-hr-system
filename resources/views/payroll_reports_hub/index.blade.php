@extends('layouts.hr')

@section('title', 'تقارير الرواتب الشاملة')
@section('page-title', 'تقارير الرواتب الشاملة')

@section('content')
    {{-- Font Awesome fallback --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    @php
        $reportGroups = [
            'تقارير أساسية' => [
                'payroll_summary',
                'employee_details',
                'payslips',
                'net_salary',
                'gross_salary',
                'deductions',
                'salary_advances',
                'cost_centers',
            ],
            'تقارير التحويلات البنكية' => [
                'bank_transfer',
                'bank_transfer_batches',
                'missing_bank_data',
                'bank_summary',
                'payment_methods_summary',
                'bank_monthly_trend',
            ],
            'تقارير السجلات والمراجعة' => [
                'payroll_statuses',
                'payroll_period_logs',
                'bank_transfer_batch_logs',
                'paid_archive',
                'payroll_warnings',
            ],
            'تقارير تحليلية' => [
                'payroll_comparison',
                'net_salary_changes',
                'employees_without_payroll',
                'employee_status_payroll',
                'nationality_payroll',
                'payable_days',
            ],
            'تقارير تفصيلية' => [
                'payroll_components',
                'allowances_details',
                'deductions_details',
                'employee_payroll_history',
                'top_net_salary',
                'zero_negative_net_salary',
            ],
            'تقارير إدارية وتجميعية' => [
                'payroll_groups_summary',
                'position_payroll',
                'salary_range_distribution',
                'allowance_component_summary',
                'deduction_component_summary',
                'employee_cost_summary',
                'newly_hired_payroll',
            ],
            'تقارير شهرية وسنوية' => [
                'monthly_payroll_trend',
                'annual_payroll_summary',
                'employee_annual_summary',
                'department_monthly_trend',
                'cost_center_monthly_trend',
                'highest_deductions',
                'highest_advances',
                'payroll_liability_summary',
            ],
        ];

        $reportDescriptions = [
            'payroll_summary' => 'يعرض ملخص المسيرات: إجمالي الرواتب، الخصومات، السلف، الإيقافات وصافي الرواتب.',
            'employee_details' => 'يعرض تفاصيل راتب كل موظف داخل المسير المحدد أو الفترة المختارة.',
            'payslips' => 'يعرض بيانات قسائم الرواتب بشكل مختصر لكل موظف.',
            'net_salary' => 'يركز على صافي الراتب ومعلومات طريقة الدفع والبنك وIBAN.',
            'gross_salary' => 'يعرض الراتب الأساسي والبدلات والإجمالي قبل الخصومات.',
            'deductions' => 'يعرض جميع الخصومات والسلف والإيقافات على الموظفين.',
            'salary_advances' => 'يعرض الموظفين الذين لديهم سلف مخصومة فقط.',
            'cost_centers' => 'يجمع الرواتب حسب مراكز التكلفة.',
            'bank_transfer' => 'كشف تحويل الرواتب للبنك مع التحقق من البيانات البنكية.',
            'bank_transfer_batches' => 'يعرض دفعات تحويل الرواتب وحالاتها ومراجع البنك.',
            'missing_bank_data' => 'يعرض الموظفين الذين لديهم نقص في اسم البنك أو IBAN.',
            'bank_summary' => 'ملخص إجمالي التحويلات حسب البنك.',
            'payment_methods_summary' => 'ملخص الرواتب حسب طريقة الدفع والبنك.',
            'bank_monthly_trend' => 'اتجاه التحويلات البنكية شهريًا حسب البنك وطريقة الدفع.',
            'payroll_statuses' => 'تجميع المسيرات حسب حالتها: مسودة، محسوب، معتمد، مدفوع، ملغي.',
            'payroll_period_logs' => 'سجل الحركات والتغييرات على مسيرات الرواتب.',
            'bank_transfer_batch_logs' => 'سجل حركات دفعات التحويل البنكي.',
            'paid_archive' => 'أرشيف المسيرات المدفوعة فقط.',
            'payroll_warnings' => 'يعرض التحذيرات مثل الراتب السالب أو نقص البيانات البنكية.',
            'payroll_comparison' => 'يقارن صافي الراتب بين مسيرين. يجب اختيار مسير الرواتب ومسير المقارنة.',
            'net_salary_changes' => 'يعرض الموظفين الذين تغيّر صافي رواتبهم بين مسيرين.',
            'employees_without_payroll' => 'يعرض الموظفين غير الموجودين في المسير المحدد.',
            'employee_status_payroll' => 'يجمع الرواتب حسب حالة الموظف.',
            'nationality_payroll' => 'يجمع الرواتب حسب الجنسية.',
            'payable_days' => 'يعرض أيام الاستحقاق والغياب والإجازات غير المدفوعة.',
            'payroll_components' => 'يعرض مكونات الراتب التفصيلية إن كان جدول المكونات موجودًا.',
            'allowances_details' => 'يعرض تفاصيل البدلات من مكونات الراتب.',
            'deductions_details' => 'يعرض تفاصيل الخصومات من مكونات الراتب.',
            'employee_payroll_history' => 'يعرض سجل رواتب الموظف عبر المسيرات.',
            'top_net_salary' => 'يعرض أعلى صافي رواتب.',
            'zero_negative_net_salary' => 'يعرض الرواتب الصفرية أو السالبة.',
            'payroll_groups_summary' => 'يجمع الرواتب حسب مجموعات الرواتب.',
            'position_payroll' => 'يجمع الرواتب حسب المسمى الوظيفي.',
            'salary_range_distribution' => 'يوزع الموظفين حسب شرائح صافي الراتب.',
            'allowance_component_summary' => 'ملخص البدلات حسب النوع والاسم.',
            'deduction_component_summary' => 'ملخص الخصومات حسب النوع والاسم.',
            'employee_cost_summary' => 'ملخص تكلفة كل موظف عبر المسيرات.',
            'newly_hired_payroll' => 'يعرض رواتب الموظفين الجدد داخل فترة المسير.',
            'monthly_payroll_trend' => 'اتجاه الرواتب شهريًا.',
            'annual_payroll_summary' => 'ملخص سنوي للرواتب والخصومات والصافي.',
            'employee_annual_summary' => 'ملخص سنوي لكل موظف.',
            'department_monthly_trend' => 'اتجاه تكلفة الأقسام شهريًا.',
            'cost_center_monthly_trend' => 'اتجاه مراكز التكلفة شهريًا.',
            'highest_deductions' => 'يعرض أعلى الخصومات على الموظفين.',
            'highest_advances' => 'يعرض أعلى السلف المخصومة.',
            'payroll_liability_summary' => 'يعرض الالتزامات القائمة وغير المدفوعة حسب حالة المسير.',
        ];

        $numericKeys = collect(array_keys($columns ?? []))->filter(function ($key) use ($rows) {
            return collect($rows ?? [])->contains(function ($row) use ($key) {
                $value = $row[$key] ?? null;
                if ($value === null || $value === '' || $value === '-') return false;
                $clean = str_replace([',', 'SAR', 'ريال', 'ر.س', ' '], '', (string) $value);
                return is_numeric($clean);
            });
        })->values();

        $preferredValueKeys = ['net','total_net','total_transfer','total_amount','gross','deductions','total_deductions','employees_count','periods_count','amount'];
        $chartValueKey = collect($preferredValueKeys)->first(fn($key) => $numericKeys->contains($key)) ?? $numericKeys->first();

        $preferredLabelKeys = ['employee_name','department','month','period_number','bank','payment_method','cost_center','position','payroll_group','component_name','status','year','range','nationality'];
        $chartLabelKey = collect($preferredLabelKeys)->first(fn($key) => array_key_exists($key, $columns ?? [])) ?? collect(array_keys($columns ?? []))->first();

        $chartRows = collect($rows ?? [])->map(function ($row) use ($chartValueKey, $chartLabelKey) {
            $raw = $row[$chartValueKey] ?? 0;
            $clean = str_replace([',', 'SAR', 'ريال', 'ر.س', ' '], '', (string) $raw);
            return [
                'label' => (string) ($row[$chartLabelKey] ?? '-'),
                'value' => is_numeric($clean) ? (float) $clean : 0,
                'display' => $row[$chartValueKey] ?? '0',
            ];
        })->filter(fn($row) => $row['value'] > 0)->sortByDesc('value')->take(10)->values();

        $chartMaxValue = max((float) ($chartRows->max('value') ?: 0), 1);

        $visualCards = [
            ['label' => 'عدد النتائج', 'value' => number_format(count($rows ?? [])), 'icon' => '📄'],
            ['label' => 'أعلى قيمة', 'value' => $chartRows->first()['display'] ?? '-', 'icon' => '🏆'],
            ['label' => 'عمود التحليل', 'value' => $columns[$chartValueKey] ?? '-', 'icon' => '📊'],
            ['label' => 'التصنيف', 'value' => $columns[$chartLabelKey] ?? '-', 'icon' => '🏷️'],
        ];
    @endphp

    <style>
        .prh-page,
        .prh-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .prh-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        .prh-icon{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;font-size:16px;line-height:1;flex-shrink:0}
        .prh-icon.big{width:38px;height:38px;font-size:32px}
        .prh-icon.info{width:28px;height:28px;font-size:25px}

        .prh-shortcuts-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
        .prh-shortcut-card{border:1px solid #eeeafc;background:linear-gradient(135deg,#fbfaff,#fff);border-radius:20px;padding:16px;min-width:0;transition:.18s ease}
        .prh-shortcut-card:hover{transform:translateY(-3px);box-shadow:0 15px 35px rgba(76,59,145,.12);border-color:#d8cffc}
        .prh-shortcut-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
        .prh-shortcut-icon{width:42px;height:42px;border-radius:14px;background:#ede9fe;color:#4c3b91;display:inline-flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
        .prh-shortcut-head strong{display:block;color:#111827;font-size:14px;font-weight:900;margin-bottom:3px}
        .prh-shortcut-head small{color:#6b7280;font-size:11px;font-weight:800}
        .prh-chip-list{display:flex;flex-wrap:wrap;gap:8px}
        .prh-report-chip{border:1px solid #ddd6fe;background:#fff;color:#4c3b91;border-radius:999px;padding:8px 11px;font-size:11px;font-weight:900;cursor:pointer;transition:.16s ease}
        .prh-report-chip:hover,.prh-report-chip.active{background:#6d5bd0;color:#fff;border-color:#6d5bd0}

        .prh-visual-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px}
        .prh-visual-card{border:1px solid #eeeafc;background:linear-gradient(135deg,#fff,#fbfaff);border-radius:18px;padding:14px;min-width:0;display:flex;align-items:center;gap:12px}
        .prh-visual-icon{width:42px;height:42px;border-radius:14px;background:#ede9fe;color:#4c3b91;display:inline-flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
        .prh-visual-card span{display:block;color:#6b7280;font-size:11px;font-weight:900;margin-bottom:5px}
        .prh-visual-card strong{display:block;color:#111827;font-size:14px;font-weight:900;word-break:break-word}
        .prh-chart{display:grid;grid-template-columns:1fr;gap:9px}
        .prh-chart-row{display:grid;grid-template-columns:190px 1fr 110px;gap:10px;align-items:center}
        .prh-chart-label{color:#374151;font-size:11px;font-weight:900;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .prh-chart-track{height:16px;background:#f1edff;border-radius:999px;overflow:hidden;border:1px solid #e4defc}
        .prh-chart-bar{height:100%;background:linear-gradient(90deg,#6d5bd0,#8b5cf6);border-radius:999px;min-width:4px}
        .prh-chart-value{color:#4c3b91;font-size:11px;font-weight:900;text-align:left;direction:ltr}


        .prh-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #4c3b91, #6d5bd0 55%, #8b5cf6);
            color: #fff;
            border-radius: 28px;
            padding: 30px;
            margin-bottom: 18px;
            box-shadow: 0 24px 60px rgba(76, 59, 145, .24);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .prh-hero::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,.10);
            left: -70px;
            bottom: -90px;
        }

        .prh-hero-content {
            position: relative;
            z-index: 2;
        }

        .prh-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 900;
        }

        .prh-hero p {
            margin: 0;
            opacity: .9;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.9;
        }

        .prh-hero-icon {
            width: 82px;
            height: 82px;
            border-radius: 24px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255,255,255,.20);
        }

        .prh-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, .08);
        }

        .prh-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .prh-stat {
            background: #faf9ff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 16px;
            min-width: 0;
        }

        .prh-stat span {
            display: block;
            color: #6b7280;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .prh-stat strong {
            display: block;
            color: #4c3b91;
            font-size: 18px;
            font-weight: 900;
            word-break: break-word;
        }

        .prh-filter-title {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .prh-filter-title h3 {
            margin: 0;
            color: #4c3b91;
            font-size: 18px;
            font-weight: 900;
        }

        .prh-filter-title small {
            color: #6b7280;
            font-weight: 800;
        }

        .prh-filters {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
        }

        .prh-field {
            min-width: 0;
        }

        .prh-field.full {
            grid-column: 1 / -1;
        }

        .prh-field label {
            display: block;
            margin-bottom: 7px;
            color: #4c3b91;
            font-size: 12px;
            font-weight: 900;
        }

        .prh-field input,
        .prh-field select {
            width: 100%;
            height: 44px;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 800;
            outline: none;
            background: #fff;
            color: #111827;
        }

        .prh-field input:focus,
        .prh-field select:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109,91,208,.12);
        }

        .prh-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .prh-btn {
            min-height: 42px;
            border: 0;
            border-radius: 13px;
            padding: 0 15px;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            white-space: nowrap;
        }

        .prh-primary { background: #6d5bd0; color: #fff; box-shadow: 0 12px 26px rgba(109,91,208,.22); }
        .prh-soft { background: #ede9fe; color: #4c3b91; }
        .prh-green { background: #16a34a; color: #fff; }
        .prh-dark { background: #111827; color: #fff; }

        .prh-report-info {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 70px 1fr;
            gap: 12px;
            align-items: center;
            background: linear-gradient(135deg, #f8f7ff, #fff);
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 14px;
        }

        .prh-info-icon {
            width: 58px;
            height: 58px;
            border-radius: 17px;
            background: #ede9fe;
            color: #4c3b91;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .prh-report-info strong {
            display: block;
            color: #111827;
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .prh-report-info span {
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.8;
        }

        .prh-note {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 16px;
            padding: 12px;
            margin-top: 12px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.9;
        }

        .prh-result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .prh-result-header h3 {
            color: #4c3b91;
            margin: 0;
            font-weight: 900;
            font-size: 18px;
        }

        .prh-table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #eeeafc;
            background: #fff;
        }

        .prh-table {
            width: 100%;
            min-width: 1050px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .prh-table th {
            position: sticky;
            top: 0;
            background: #f1edff;
            color: #4c3b91;
            font-size: 11px;
            font-weight: 900;
            padding: 11px 7px;
            text-align: center;
            border-bottom: 1px solid #e6dcff;
            white-space: nowrap;
            z-index: 1;
        }

        .prh-table td {
            border-top: 1px solid #f1eefb;
            padding: 10px 7px;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            word-break: break-word;
            vertical-align: middle;
            color: #1f2937;
        }

        .prh-table tr:hover td {
            background: #fbfaff;
        }

        .prh-empty {
            text-align: center;
            color: #6b7280;
            font-weight: 900;
            padding: 35px !important;
        }

        @media(max-width:1200px) {
            .prh-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .prh-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .prh-shortcuts-grid,.prh-visual-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .prh-chart-row { grid-template-columns: 150px 1fr 90px; }
        }

        @media(max-width:700px) {
            .prh-filters,
            .prh-stats,
            .prh-shortcuts-grid,
            .prh-visual-grid,
            .prh-report-info {
                grid-template-columns: 1fr;
            }

            .prh-hero h1 {
                font-size: 23px;
            }

            .prh-actions .prh-btn {
                flex: 1 1 130px;
            }
        }
    </style>

    <div class="prh-page">
        <div class="prh-hero">
            <div class="prh-hero-content">
                <h1>تقارير الرواتب الشاملة</h1>
                <p>مركز موحد لكل تقارير الرواتب مع فلاتر، مقارنة، تصدير Excel، وطباعة PDF.</p>
            </div>

            <div class="prh-hero-icon">
                <span class="prh-icon big">📊</span>
            </div>
        </div>

        <div class="prh-stats">
            <div class="prh-stat">
                <span>نوع التقرير</span>
                <strong>{{ $reportTitle }}</strong>
            </div>

            <div class="prh-stat">
                <span>عدد المسيرات</span>
                <strong>{{ number_format($summary['periods_count']) }}</strong>
            </div>

            <div class="prh-stat">
                <span>عدد السجلات</span>
                <strong>{{ number_format($summary['rows_count']) }}</strong>
            </div>

            <div class="prh-stat">
                <span>إجمالي الصافي</span>
                <strong>{{ $summary['net_total'] }}</strong>
            </div>

            <div class="prh-stat">
                <span>إجمالي الخصومات</span>
                <strong>{{ $summary['deductions_total'] }}</strong>
            </div>
        </div>

        <div class="prh-card">
            <div class="prh-filter-title">
                <div>
                    <h3>اختصارات التقارير</h3>
                    <small>اضغط على أي تقرير للانتقال إليه مباشرة.</small>
                </div>
            </div>

            <div class="prh-shortcuts-grid">
                @foreach($reportGroups as $groupName => $keys)
                    @php
                        $availableKeys = collect($keys)->filter(fn($key) => isset($reports[$key]));
                        $groupIcon = match($groupName) {
                            'تقارير أساسية' => '📌',
                            'تقارير التحويلات البنكية' => '🏦',
                            'تقارير السجلات والمراجعة' => '🕘',
                            'تقارير تحليلية' => '📈',
                            'تقارير تفصيلية' => '📋',
                            'تقارير إدارية وتجميعية' => '🧩',
                            'تقارير شهرية وسنوية' => '📅',
                            default => '📊',
                        };
                    @endphp

                    @if($availableKeys->isNotEmpty())
                        <div class="prh-shortcut-card">
                            <div class="prh-shortcut-head">
                                <span class="prh-shortcut-icon">{{ $groupIcon }}</span>
                                <div>
                                    <strong>{{ $groupName }}</strong>
                                    <small>{{ $availableKeys->count() }} تقرير متاح</small>
                                </div>
                            </div>

                            <div class="prh-chip-list">
                                @foreach($availableKeys as $key)
                                    <button type="button"
                                            class="prh-report-chip {{ $reportType === $key ? 'active' : '' }}"
                                            data-report-type="{{ $key }}">
                                        {{ $reports[$key] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="prh-card">
            <div class="prh-filter-title">
                <div>
                    <h3>فلاتر التقرير</h3>
                    <small>اختر نوع التقرير ثم حدد الفلاتر المطلوبة.</small>
                </div>

                <div class="prh-actions">
                    @if(auth()->user()->hasPermission('payroll_reports_hub.export'))
                        <a href="{{ route('payroll-reports-hub.export-excel', request()->query()) }}" class="prh-btn prh-green">
                            <span class="prh-icon">📗</span>
                            Excel
                        </a>

                        <a href="{{ route('payroll-reports-hub.print-pdf', request()->query()) }}" class="prh-btn prh-dark" target="_blank">
                            <span class="prh-icon">🖨️</span>
                            PDF
                        </a>
                    @endif
                </div>
            </div>

            <form method="GET" action="{{ route('payroll-reports-hub.index') }}" id="prhReportFilterForm">
                <div class="prh-filters">
                    <div class="prh-field">
                        <label>بحث داخل قائمة التقارير</label>
                        <input type="text" id="reportSearchInput" placeholder="اكتب اسم التقرير للبحث">
                    </div>

                    <div class="prh-field">
                        <label>نوع التقرير</label>
                        <select name="report_type" id="reportTypeSelect">
                            @foreach($reportGroups as $groupName => $keys)
                                @php
                                    $availableKeys = collect($keys)->filter(fn($key) => isset($reports[$key]));
                                @endphp

                                @if($availableKeys->isNotEmpty())
                                    <optgroup label="{{ $groupName }}">
                                        @foreach($availableKeys as $key)
                                            <option
                                                value="{{ $key }}"
                                                data-description="{{ $reportDescriptions[$key] ?? '' }}"
                                                data-group="{{ $groupName }}"
                                                @selected($reportType === $key)>
                                                {{ $reports[$key] }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="prh-field">
                        <label>مسير الرواتب</label>
                        <select name="payroll_period_id">
                            <option value="">الكل</option>
                            @foreach($periodsForFilter as $period)
                                <option value="{{ $period->id }}" @selected((string) request('payroll_period_id') === (string) $period->id)>
                                    {{ $period->period_number }} - {{ $period->month }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="prh-field">
                        <label>مسير المقارنة</label>
                        <select name="compare_payroll_period_id">
                            <option value="">يستخدم لتقارير المقارنة فقط</option>
                            @foreach($periodsForFilter as $period)
                                <option value="{{ $period->id }}" @selected((string) request('compare_payroll_period_id') === (string) $period->id)>
                                    {{ $period->period_number }} - {{ $period->month }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="prh-field">
                        <label>حالة المسير</label>
                        <select name="status">
                            <option value="">الكل</option>
                            <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                            <option value="calculated" @selected(request('status') === 'calculated')>محسوب</option>
                            <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                            <option value="paid" @selected(request('status') === 'paid')>مدفوع</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                        </select>
                    </div>

                    <div class="prh-field">
                        <label>الشهر</label>
                        <input type="month" name="month" value="{{ request('month') }}">
                    </div>

                    <div class="prh-field">
                        <label>بحث موظف</label>
                        <input type="text" name="employee_search" value="{{ request('employee_search') }}" placeholder="اسم أو رقم الموظف">
                    </div>

                    <div class="prh-field">
                        <label>القسم</label>
                        <input type="text" name="department" value="{{ request('department') }}" placeholder="القسم">
                    </div>

                    <div class="prh-field">
                        <label>من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="prh-field">
                        <label>إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="prh-report-info">
                    <div class="prh-info-icon">
                        <span class="prh-icon info">ℹ️</span>
                    </div>

                    <div>
                        <strong id="selectedReportTitle">{{ $reportTitle }}</strong>
                        <span id="selectedReportDescription">
                            {{ $reportDescriptions[$reportType] ?? 'اختر التقرير ثم اضغط عرض النتائج.' }}
                        </span>
                    </div>
                </div>

                <div class="prh-note">
                    لتقارير المقارنة وتغير صافي الراتب: اختر "مسير الرواتب" كالمسير الأول، ثم اختر "مسير المقارنة".
                    بعض التقارير التفصيلية تعتمد على وجود جدول مكونات الرواتب داخل قاعدة البيانات.
                </div>

                <div class="prh-actions" style="margin-top:14px">
                    <button class="prh-btn prh-primary" type="submit">
                        <span class="prh-icon">🔎</span>
                        عرض النتائج
                    </button>

                    <a href="{{ route('payroll-reports-hub.index') }}" class="prh-btn prh-soft">
                        <span class="prh-icon">↻</span>
                        تصفير
                    </a>
                </div>
            </form>
        </div>

        <div class="prh-card">
            <div class="prh-result-header">
                <h3>تحليل مرئي سريع</h3>
                <small style="color:#6b7280;font-weight:900;">يعرض أعلى 10 نتائج حسب العمود الرقمي المناسب للتقرير.</small>
            </div>

            <div class="prh-visual-grid">
                @foreach($visualCards as $card)
                    <div class="prh-visual-card">
                        <span class="prh-visual-icon">{{ $card['icon'] }}</span>
                        <div>
                            <span>{{ $card['label'] }}</span>
                            <strong>{{ $card['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($chartRows->isNotEmpty())
                <div class="prh-chart">
                    @foreach($chartRows as $chartRow)
                        @php
                            $barWidth = max(4, min(100, round(($chartRow['value'] / $chartMaxValue) * 100, 2)));
                        @endphp

                        <div class="prh-chart-row">
                            <div class="prh-chart-label" title="{{ $chartRow['label'] }}">{{ $chartRow['label'] }}</div>
                            <div class="prh-chart-track">
                                <div class="prh-chart-bar" style="width: {{ $barWidth }}%;"></div>
                            </div>
                            <div class="prh-chart-value">{{ $chartRow['display'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="prh-empty">لا توجد قيم رقمية مناسبة لعرض الرسم في هذا التقرير.</div>
            @endif
        </div>

        <div class="prh-card">
            <div class="prh-result-header">
                <h3>{{ $reportTitle }}</h3>

                <div class="prh-actions">
                    @if(auth()->user()->hasPermission('payroll_reports_hub.export'))
                        <a href="{{ route('payroll-reports-hub.export-excel', request()->query()) }}" class="prh-btn prh-green">
                            <span class="prh-icon">📗</span>
                            تصدير Excel
                        </a>

                        <a href="{{ route('payroll-reports-hub.print-pdf', request()->query()) }}" class="prh-btn prh-dark" target="_blank">
                            <span class="prh-icon">🖨️</span>
                            طباعة PDF
                        </a>
                    @endif
                </div>
            </div>

            <div class="prh-table-wrap">
                <table class="prh-table">
                    <thead>
                    <tr>
                        @foreach($columns as $label)
                            <th>{{ $label }}</th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($columns as $key => $label)
                                <td>{{ $row[$key] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="prh-empty">
                                لا توجد بيانات حسب الفلاتر المحددة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportSelect = document.getElementById('reportTypeSelect');
            const searchInput = document.getElementById('reportSearchInput');
            const selectedTitle = document.getElementById('selectedReportTitle');
            const selectedDescription = document.getElementById('selectedReportDescription');

            function updateReportInfo() {
                if (!reportSelect || !selectedTitle || !selectedDescription) {
                    return;
                }

                const option = reportSelect.options[reportSelect.selectedIndex];

                if (!option) {
                    return;
                }

                selectedTitle.innerText = option.textContent.trim();
                selectedDescription.innerText = option.dataset.description || 'اختر التقرير ثم اضغط عرض النتائج.';
            }

            function filterReports() {
                if (!reportSelect || !searchInput) {
                    return;
                }

                const term = searchInput.value.trim().toLowerCase();

                Array.from(reportSelect.options).forEach(function (option) {
                    if (!option.value) {
                        return;
                    }

                    const optionText = option.textContent.toLowerCase();
                    const groupText = (option.parentElement?.label || '').toLowerCase();
                    option.hidden = term && !optionText.includes(term) && !groupText.includes(term);
                });
            }

            reportSelect?.addEventListener('change', updateReportInfo);
            searchInput?.addEventListener('input', filterReports);

            document.querySelectorAll('.prh-report-chip').forEach(function (button) {
                button.addEventListener('click', function () {
                    const reportType = this.dataset.reportType;
                    if (!reportType || !reportSelect) return;

                    reportSelect.value = reportType;
                    updateReportInfo();

                    const form = document.getElementById('prhReportFilterForm');
                    if (form) form.submit();
                });
            });

            updateReportInfo();
        });
    </script>
@endsection
