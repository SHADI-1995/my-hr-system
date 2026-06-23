@extends('layouts.hr')

@section('title', 'تفاصيل مسير الرواتب')
@section('page-title', 'تفاصيل مسير الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .stats{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:18px}.stat{background:#f8f6ff;border:1px solid #eeeafc;border-radius:18px;padding:15px;text-align:center}.stat small{display:block;color:#6b5aa8;font-weight:900;margin-bottom:7px}.stat strong{font-size:18px;color:#3b2b80}
        .btn2{border:0;border-radius:13px;padding:11px 14px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.green{background:#16a34a;color:#fff}.orange{background:#f59e0b;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.red{background:#dc2626;color:#fff}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}
        .pill{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:900}.draft{background:#e5e7eb;color:#374151}.calculated{background:#dbeafe;color:#1d4ed8}.approved{background:#fef3c7;color:#92400e}.paid{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}
        details.row-details summary{cursor:pointer;color:#5b21b6;font-weight:900}.components{margin-top:8px;background:#faf9ff;border-radius:12px;padding:8px;text-align:right}

        .workflow-card{background:linear-gradient(135deg,#ffffff,#faf9ff);border:1px solid #eeeafc}
        .workflow-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px}
        .workflow-title{color:#4c3b91;font-size:18px;font-weight:900;display:flex;align-items:center;gap:9px}
        .workflow-title i{width:38px;height:38px;border-radius:14px;background:#ede9fe;color:#6d5bd0;display:inline-flex;align-items:center;justify-content:center}
        .lock-badge{display:inline-flex;align-items:center;gap:7px;border-radius:999px;padding:8px 12px;font-size:12px;font-weight:900}
        .lock-open{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0}
        .lock-closed{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}
        .workflow-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:14px}
        .workflow-step{border:1px solid #eeeafc;background:#fff;border-radius:16px;padding:12px;position:relative}
        .workflow-step .step-icon{width:34px;height:34px;border-radius:12px;background:#f3f4f6;color:#6b7280;display:inline-flex;align-items:center;justify-content:center;margin-bottom:8px}
        .workflow-step strong{display:block;color:#111827;font-size:13px;margin-bottom:4px}
        .workflow-step small{color:#6b7280;font-size:11px;font-weight:800}
        .workflow-step.done{background:#f0fdf4;border-color:#bbf7d0}
        .workflow-step.done .step-icon{background:#dcfce7;color:#166534}
        .workflow-step.active{background:#fff7ed;border-color:#fed7aa;box-shadow:0 10px 24px rgba(245,158,11,.12)}
        .workflow-step.active .step-icon{background:#ffedd5;color:#c2410c}
        .workflow-info{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .workflow-info div{background:#fff;border:1px dashed #ddd6fe;border-radius:14px;padding:10px;text-align:center}
        .workflow-info small{display:block;color:#6b5aa8;font-size:11px;font-weight:900;margin-bottom:5px}
        .workflow-info strong{color:#111827;font-size:12px;font-weight:900}
        .workflow-alert{margin-top:12px;border-radius:14px;padding:11px 13px;font-size:12px;font-weight:900;line-height:1.8;display:flex;align-items:flex-start;gap:9px}
        .workflow-alert.open{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0}
        .workflow-alert.locked{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}

        .audit-card h3{
            color:#4c3b91;
            margin:0 0 14px;
            font-size:18px;
            font-weight:900;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .audit-action{
            display:inline-flex;
            padding:5px 9px;
            border-radius:999px;
            background:#ede9fe;
            color:#4c3b91;
            font-size:10px;
            font-weight:900;
            white-space:nowrap;
        }
        .audit-user{
            font-weight:900;
            color:#111827;
        }
        .audit-date{
            direction:ltr;
            white-space:nowrap;
            color:#374151;
            font-weight:900;
        }
        .audit-meta{
            color:#6b7280;
            font-size:9px;
            font-weight:800;
            margin-top:4px;
        }

        .scope-card{
            display:grid;
            grid-template-columns:220px minmax(0,1fr);
            gap:16px;
            align-items:start;
            background:linear-gradient(135deg,#faf9ff,#fff);
        }
        .scope-title{
            display:flex;
            align-items:center;
            gap:10px;
            color:#4c3b91;
            font-weight:900;
            font-size:16px;
        }
        .scope-title i{
            width:38px;
            height:38px;
            border-radius:14px;
            background:#ede9fe;
            color:#6d5bd0;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }
        .scope-info{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }
        .scope-chip{
            display:inline-flex;
            align-items:center;
            gap:7px;
            padding:9px 12px;
            border-radius:999px;
            background:#f1edff;
            color:#4c3b91;
            font-size:12px;
            font-weight:900;
            border:1px solid #ddd6fe;
        }
        .scope-chip.all{
            background:#ecfdf5;
            color:#166534;
            border-color:#bbf7d0;
        }
        .scope-note{
            margin-top:10px;
            color:#6b7280;
            font-size:12px;
            line-height:1.8;
            font-weight:800;
        }

        @media(max-width:1100px){.stats{grid-template-columns:repeat(2,1fr)}.scope-card{grid-template-columns:1fr}}
        @media(max-width:700px){.stats{grid-template-columns:1fr}}
    </style>

    <div class="page">
        <div class="hero">
            <div>
                <h1>{{ $payrollPeriod->period_number }} - {{ $payrollPeriod->month }}</h1>
                <p>من {{ optional($payrollPeriod->start_date)->format('Y-m-d') }} إلى {{ optional($payrollPeriod->end_date)->format('Y-m-d') }}</p>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a class="btn2 soft" href="{{ route('payroll-periods.index') }}">رجوع</a>

                {{-- زر تصدير Excel يظهر بعد الاحتساب مباشرة، سواء قبل الاعتماد أو بعد الاعتماد --}}
                @if($payrollPeriod->items->count() > 0 && auth()->user()->hasPermission('payroll_reports.export'))
                    <a class="btn2 green" href="{{ route('payroll-reports.export-excel', $payrollPeriod) }}">
                        <i class="fas fa-file-excel"></i>
                        تصدير Excel
                    </a>
                @endif

                @if($payrollPeriod->items->count() > 0 && auth()->user()->hasPermission('payroll_reports.export'))
                    <a class="btn2 orange" href="{{ route('payroll-reports.print-pdf', $payrollPeriod) }}" target="_blank">
                        <i class="fas fa-print"></i>
                        طباعة / PDF
                    </a>
                @endif

                @if($payrollPeriod->can_calculate && auth()->user()->hasPermission('payroll_periods.calculate'))
                    <form method="POST" action="{{ route('payroll-periods.calculate', $payrollPeriod) }}">
                        @csrf
                        <button class="btn2 primary" onclick="return confirm('{{ $payrollPeriod->status === 'calculated' ? 'سيتم إعادة احتساب المسير وحذف النتائج السابقة لهذه الفترة فقط، هل تريد المتابعة؟' : 'سيتم احتساب مسير الرواتب لهذه الفترة، هل تريد المتابعة؟' }}')">
                            <i class="fas fa-calculator"></i>
                            {{ $payrollPeriod->status === 'calculated' ? 'إعادة احتساب' : 'احتساب' }}
                        </button>
                    </form>
                @endif

                @if($payrollPeriod->can_approve && auth()->user()->hasPermission('payroll_periods.approve'))
                    <form method="POST" action="{{ route('payroll-periods.approve', $payrollPeriod) }}">
                        @csrf
                        <button class="btn2 orange" onclick="return confirm('هل تريد اعتماد مسير الرواتب؟')">
                            <i class="fas fa-check"></i> اعتماد
                        </button>
                    </form>
                @endif

                @if($payrollPeriod->status === 'approved' && auth()->user()->hasPermission('payroll_periods.cancel_approval'))
                    <form method="POST" action="{{ route('payroll-periods.cancel-approval', $payrollPeriod) }}">
                        @csrf
                        <button class="btn2 red" onclick="return confirm('سيتم إلغاء اعتماد المسير وإعادته إلى مرحلة الاحتساب، هل أنت متأكد؟')">
                            <i class="fas fa-rotate-left"></i> إلغاء الاعتماد
                        </button>
                    </form>
                @endif

                @if($payrollPeriod->can_pay && auth()->user()->hasPermission('payroll_periods.pay'))
                    <form method="POST" action="{{ route('payroll-periods.mark-paid', $payrollPeriod) }}">
                        @csrf
                        <button class="btn2 green" onclick="return confirm('سيتم صرف المسير وتحديث أقساط السلف كمدفوعة، هل أنت متأكد؟')">
                            <i class="fas fa-money-bill-transfer"></i> صرف
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @php
            $statusText = $payrollPeriod->status_text ?? match ($payrollPeriod->status) {
                'draft' => 'مسودة',
                'calculated' => 'محسوب',
                'approved' => 'معتمد',
                'paid' => 'مدفوع',
                'cancelled' => 'ملغي',
                default => $payrollPeriod->status ?: '-',
            };

            $isLocked = (bool) ($payrollPeriod->is_locked ?? in_array($payrollPeriod->status, ['approved', 'paid'], true));

            $workflowSteps = [
                ['key' => 'draft', 'title' => 'مسودة', 'icon' => 'fa-file-lines', 'description' => 'تم إنشاء المسير'],
                ['key' => 'calculated', 'title' => 'محسوب', 'icon' => 'fa-calculator', 'description' => 'تم احتساب الرواتب'],
                ['key' => 'approved', 'title' => 'معتمد', 'icon' => 'fa-circle-check', 'description' => 'تم اعتماد المسير'],
                ['key' => 'paid', 'title' => 'مدفوع', 'icon' => 'fa-money-bill-transfer', 'description' => 'تم صرف المسير'],
            ];

            $statusOrder = ['draft' => 1, 'calculated' => 2, 'approved' => 3, 'paid' => 4];
            $currentStepNumber = $statusOrder[$payrollPeriod->status] ?? 1;
        @endphp

        <div class="card workflow-card">
            <div class="workflow-head">
                <div class="workflow-title">
                    <i class="fas fa-route"></i>
                    سير عمل المسير
                </div>

                @if($isLocked)
                    <span class="lock-badge lock-closed">
                        <i class="fas fa-lock"></i>
                        المسير مقفل
                    </span>
                @else
                    <span class="lock-badge lock-open">
                        <i class="fas fa-lock-open"></i>
                        المسير مفتوح
                    </span>
                @endif
            </div>

            <div class="workflow-steps">
                @foreach($workflowSteps as $index => $step)
                    @php
                        $stepNumber = $index + 1;
                        $stepClass = $stepNumber < $currentStepNumber
                            ? 'done'
                            : ($stepNumber === $currentStepNumber ? 'active' : '');
                    @endphp

                    <div class="workflow-step {{ $stepClass }}">
                        <span class="step-icon">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </span>
                        <strong>{{ $step['title'] }}</strong>
                        <small>{{ $step['description'] }}</small>
                    </div>
                @endforeach
            </div>

            <div class="workflow-info">
                <div><small>الحالة الحالية</small><strong>{{ $statusText }}</strong></div>
                <div><small>أنشأ بواسطة</small><strong>{{ $payrollPeriod->createdBy?->name ?? '-' }}</strong></div>
                <div><small>اعتمد بواسطة</small><strong>{{ $payrollPeriod->approvedBy?->name ?? '-' }}</strong></div>
                <div><small>صرف بواسطة</small><strong>{{ $payrollPeriod->paidBy?->name ?? '-' }}</strong></div>
            </div>

            @if($payrollPeriod->status === 'draft')
                <div class="workflow-alert open">
                    <i class="fas fa-circle-info"></i>
                    <div>المسير ما زال مسودة. اضغط احتساب لإنشاء رواتب الموظفين.</div>
                </div>
            @elseif($payrollPeriod->status === 'calculated')
                <div class="workflow-alert open">
                    <i class="fas fa-circle-info"></i>
                    <div>تم احتساب المسير. يمكنك إعادة الاحتساب قبل الاعتماد، أو اعتماده لإقفال مرحلة الاحتساب.</div>
                </div>
            @elseif($payrollPeriod->status === 'approved')
                <div class="workflow-alert locked">
                    <i class="fas fa-lock"></i>
                    <div>تم اعتماد المسير. لا يمكن إعادة الاحتساب أو تعديل النتائج بعد الاعتماد. الخطوة التالية هي الصرف.</div>
                </div>
            @elseif($payrollPeriod->status === 'paid')
                <div class="workflow-alert locked">
                    <i class="fas fa-lock"></i>
                    <div>تم صرف المسير وهو مقفل بالكامل، وتم تحديث أقساط السلف والاستقطاعات المرتبطة به.</div>
                </div>
            @endif
        </div>

        <div class="card audit-card">
            <h3>
                <i class="fas fa-clock-rotate-left"></i>
                سجل حركات المسير
            </h3>

            <table>
                <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الإجراء</th>
                    <th>من حالة</th>
                    <th>إلى حالة</th>
                    <th>بواسطة</th>
                    <th style="width:24%">الوصف</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payrollPeriod->logs as $log)
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
                    @endphp

                    <tr>
                        <td>
                            <span class="audit-date">{{ optional($log->created_at)->format('Y-m-d H:i') }}</span>
                        </td>
                        <td>
                            <span class="audit-action">{{ $log->action_text ?? $log->action }}</span>
                        </td>
                        <td>{{ $statusFromText }}</td>
                        <td>{{ $statusToText }}</td>
                        <td>
                            <span class="audit-user">{{ $log->user?->name ?? '-' }}</span>
                        </td>
                        <td>
                            {{ $log->description ?: '-' }}

                            @if(!empty($log->meta))
                                <div class="audit-meta">
                                    @if(isset($log->meta['employees_count']))
                                        الموظفين: {{ $log->meta['employees_count'] }}
                                    @endif

                                    @if(isset($log->meta['total_net_salary']))
                                        | الصافي: {{ number_format((float) $log->meta['total_net_salary'], 2) }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">لا توجد حركات مسجلة لهذا المسير حتى الآن.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card scope-card">
            <div class="scope-title">
                <i class="fas fa-layer-group"></i>
                نطاق المسير
            </div>

            <div>
                <div class="scope-info">
                    @if(($payrollPeriod->payroll_group_scope ?? 'all') === 'all')
                        <span class="scope-chip all">
                            <i class="fas fa-check-double"></i>
                            كل مجموعات الرواتب
                        </span>
                    @else
                        <span class="scope-chip">
                            <i class="fas fa-users-gear"></i>
                            مجموعات محددة
                        </span>

                        @forelse($payrollGroups as $group)
                            <span class="scope-chip">
                                <i class="fas fa-circle-dot"></i>
                                {{ $group->name_ar }}
                                @if(!empty($group->code))
                                    - {{ $group->code }}
                                @endif
                            </span>
                        @empty
                            <span class="scope-chip">
                                لا توجد مجموعات مرتبطة بهذا المسير
                            </span>
                        @endforelse
                    @endif
                </div>

                <div class="scope-note">
                    @if(($payrollPeriod->payroll_group_scope ?? 'all') === 'all')
                        عند الضغط على احتساب، سيتم إدخال جميع الموظفين الذين حالتهم داخل مسير الرواتب.
                    @else
                        عند الضغط على احتساب، سيتم إدخال موظفي المجموعات المختارة فقط.
                    @endif
                </div>
            </div>
        </div>

        <div class="stats">
            <div class="stat"><small>الحالة</small><strong><span class="pill {{ $payrollPeriod->status_badge_class ?? $payrollPeriod->status }}">{{ $payrollPeriod->status_text ?? $payrollPeriod->status }}</span></strong></div>
            <div class="stat"><small>الموظفين</small><strong>{{ $payrollPeriod->employees_count }}</strong></div>
            <div class="stat"><small>إجمالي الراتب</small><strong>{{ number_format($payrollPeriod->total_gross_salary, 2) }}</strong></div>
            <div class="stat"><small>الاستقطاعات</small><strong>{{ number_format($payrollPeriod->total_regular_deductions, 2) }}</strong></div>
            <div class="stat"><small>السلف</small><strong>{{ number_format($payrollPeriod->total_salary_advances, 2) }}</strong></div>
            <div class="stat"><small>الصافي</small><strong>{{ number_format($payrollPeriod->total_net_salary, 2) }}</strong></div>
        </div>

        <div class="card">
            <table id="payroll-period-table">
                <thead>
                <tr>
                    <th style="width:12%">الموظف</th>
                    <th>الحالة</th>
                    <th>أيام الاستحقاق<br><small>مستحق / غير مدفوع</small></th>
                    <th>الإجمالي</th>
                    <th>إجازات غير مدفوعة</th>
                    <th>إيقاف</th>
                    <th>استقطاعات</th>
                    <th>سلف</th>
                    <th>إجمالي الخصم</th>
                    <th>الصافي</th>
                    <th style="width:12%">التفاصيل</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payrollPeriod->items as $item)
                    @php
                        /*
                         * حساب أيام الاستحقاق للعرض مباشرة من تاريخ مباشرة الموظف وتاريخ نهاية الخدمة.
                         * هذا يمنع عرض 30 يوم لموظف بدأ أو انتهت خدمته داخل نفس الشهر.
                         */
                        $periodStartForDisplay = \Carbon\Carbon::parse($payrollPeriod->start_date)->startOfDay();
                        $periodEndForDisplay = \Carbon\Carbon::parse($payrollPeriod->end_date)->startOfDay();

                        $employeeHireDate = $item->employee?->hire_date
                            ? \Carbon\Carbon::parse($item->employee->hire_date)->startOfDay()
                            : null;

                        $employeeTerminationDate = $item->employee?->termination_date
                            ? \Carbon\Carbon::parse($item->employee->termination_date)->startOfDay()
                            : null;

                        $displayEligibleStart = $employeeHireDate && $employeeHireDate->gt($periodStartForDisplay)
                            ? $employeeHireDate->copy()
                            : $periodStartForDisplay->copy();

                        $displayEligibleEnd = $employeeTerminationDate && $employeeTerminationDate->lt($periodEndForDisplay)
                            ? $employeeTerminationDate->copy()
                            : $periodEndForDisplay->copy();

                        $displayPayableDays = $displayEligibleEnd->lt($displayEligibleStart)
                            ? 0
                            : ((int) $displayEligibleStart->diffInDays($displayEligibleEnd) + 1);

                        $displayUnpaidDays = (int) ($item->unpaid_leave_days ?? 0);
                        $storedPayableDays = (int) ($item->payable_days ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $item->employee_name }}<br><small>{{ $item->employee_number }}</small></td>
                        <td>
                            {{ $item->employee_status_text ?? $item->employment_status_note ?? '-' }}
                            <br>
                            <small>{{ $displayEligibleStart->format('Y-m-d') }} إلى {{ $displayEligibleEnd->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            <strong>{{ $displayPayableDays }}</strong> / {{ $displayUnpaidDays }}
                            @if($storedPayableDays !== $displayPayableDays)
                                <br>

                            @endif
                        </td>
                        <td>{{ number_format($item->gross_salary, 2) }}</td>
                        <td>{{ number_format($item->unpaid_leave_deductions ?? 0, 2) }}<br><small>{{ $item->unpaid_leave_days ?? 0 }} يوم</small></td>
                        <td>{{ number_format($item->suspension_deductions, 2) }}<br><small>{{ $item->suspended_days }} يوم</small></td>
                        <td>{{ number_format($item->regular_deductions, 2) }}</td>
                        <td>{{ number_format($item->salary_advance_deductions, 2) }}</td>
                        <td>{{ number_format($item->total_deductions, 2) }}</td>
                        <td><strong>{{ number_format($item->net_salary, 2) }}</strong></td>
                        <td>
                            <details class="row-details">
                                <summary>عرض</summary>
                                <div class="components">
                                    @foreach($item->components as $component)
                                        <div style="display:flex;justify-content:space-between;gap:8px;border-bottom:1px solid #eee;padding:5px 0">
                                            <span>{{ $component->name }}</span>
                                            <strong>{{ number_format($component->amount, 2) }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">لم يتم احتساب هذا المسير بعد. اضغط زر احتساب.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
