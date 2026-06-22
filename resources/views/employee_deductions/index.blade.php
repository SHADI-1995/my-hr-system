@extends('layouts.hr')

@section('title', 'الاستقطاعات')
@section('page-title', 'الاستقطاعات')

@section('content')
    <style>
        .deductions-page{
            max-width:100%;
            overflow-x:hidden;
        }

        .hero{
            background:linear-gradient(135deg,#4c3b91,#7c3aed);
            color:#fff;
            border-radius:24px;
            padding:26px;
            margin-bottom:18px;
            box-shadow:0 20px 45px rgba(76,59,145,.20);
        }

        .hero h1{
            margin:0 0 8px;
            font-size:28px;
            font-weight:900;
        }

        .hero p{
            margin:0;
            font-weight:700;
            opacity:.9;
        }

        .card{
            background:#fff;
            border:1px solid #eeeafc;
            border-radius:22px;
            padding:20px;
            margin-bottom:18px;
            box-shadow:0 16px 40px rgba(76,59,145,.07);
        }

        .filters{
            display:grid;
            grid-template-columns:2fr 1fr 1fr 1fr auto;
            gap:12px;
            align-items:end;
        }

        .field label{
            display:block;
            color:#4c3b91;
            font-weight:900;
            font-size:12px;
            margin-bottom:7px;
        }

        .field input,
        .field select{
            width:100%;
            height:42px;
            border:1px solid #ddd6fe;
            border-radius:14px;
            padding:0 12px;
            font-weight:800;
            outline:none;
            background:#fff;
        }

        .field input:focus,
        .field select:focus{
            border-color:#6d5bd0;
            box-shadow:0 0 0 4px rgba(109,91,208,.10);
        }

        .btn2{
            border:0;
            border-radius:13px;
            padding:11px 14px;
            font-weight:900;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            cursor:pointer;
            white-space:nowrap;
        }

        .primary{background:#6d5bd0;color:#fff}
        .green{background:#16a34a;color:#fff}
        .red{background:#dc2626;color:#fff}
        .soft{background:#ede9fe;color:#4c3b91}
        .blue{background:#2563eb;color:#fff}

        .table-wrap{
            width:100%;
            overflow-x:auto;
            border:1px solid #eeeafc;
            border-radius:18px;
        }

        table{
            width:100%;
            min-width:1180px;
            border-collapse:separate;
            border-spacing:0;
            overflow:hidden;
        }

        th{
            background:#f1edff;
            color:#4c3b91;
            font-size:11px;
            font-weight:900;
            padding:11px 8px;
            text-align:center;
            white-space:nowrap;
        }

        td{
            border-top:1px solid #f1eefb;
            padding:11px 8px;
            font-size:11px;
            font-weight:800;
            text-align:center;
            vertical-align:middle;
        }

        .employee-cell{
            text-align:right;
            min-width:190px;
        }

        .employee-name{
            color:#111827;
            font-size:12px;
            font-weight:900;
            margin-bottom:4px;
        }

        .employee-number{
            color:#6b7280;
            font-size:10px;
            font-weight:900;
        }

        .deduction-title{
            text-align:right;
            min-width:170px;
        }

        .deduction-title strong{
            display:block;
            color:#111827;
            font-size:12px;
            margin-bottom:4px;
        }

        .deduction-title small{
            display:block;
            color:#6b7280;
            font-weight:900;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:5px;
            padding:5px 9px;
            border-radius:999px;
            font-size:10px;
            font-weight:900;
            white-space:nowrap;
        }

        .pending{background:#fef3c7;color:#92400e}
        .approved{background:#dcfce7;color:#166534}
        .active{background:#dcfce7;color:#166534}
        .cancelled{background:#fee2e2;color:#991b1b}
        .completed{background:#dbeafe;color:#1d4ed8}

        .mode-pill{
            background:#f1edff;
            color:#4c3b91;
        }

        .amount-main{
            font-size:13px;
            font-weight:900;
            color:#111827;
            white-space:nowrap;
        }

        .amount-sub{
            color:#6b7280;
            font-size:10px;
            margin-top:4px;
            font-weight:900;
        }

        .schedule-box{
            display:grid;
            gap:5px;
            justify-items:center;
        }

        .schedule-line{
            display:flex;
            align-items:center;
            gap:5px;
            color:#374151;
            font-size:10px;
            font-weight:900;
            white-space:nowrap;
        }

        .progress-wrap{
            min-width:130px;
        }

        .progress-head{
            display:flex;
            justify-content:space-between;
            gap:8px;
            color:#4c3b91;
            font-size:10px;
            font-weight:900;
            margin-bottom:6px;
        }

        .progress-track{
            height:8px;
            background:#ede9fe;
            border-radius:999px;
            overflow:hidden;
        }

        .progress-fill{
            height:100%;
            background:#6d5bd0;
            border-radius:999px;
            width:0;
        }

        .actions{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            flex-wrap:wrap;
            min-width:155px;
        }

        .actions .btn2{
            padding:7px 10px;
            font-size:10px;
        }

        .reason-cell{
            min-width:170px;
            color:#4b5563;
            line-height:1.7;
        }

        .empty{
            padding:28px;
            color:#6b7280;
            font-size:14px;
            font-weight:900;
        }

        @media(max-width:1100px){
            .filters{
                grid-template-columns:1fr 1fr;
            }
        }

        @media(max-width:700px){
            .filters{
                grid-template-columns:1fr;
            }

            .hero h1{
                font-size:22px;
            }
        }
    </style>

    <div class="deductions-page">
        <div class="hero">
            <h1>الاستقطاعات</h1>
            <p>إدارة الاستقطاعات العامة وجدولتها داخل مسير الرواتب حسب الشهر، الأقساط، أو الأشهر المحددة.</p>
        </div>

        @if(session('success'))
            <div class="card" style="background:#ecfdf5;color:#166534;font-weight:900">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="card" style="background:#fef2f2;color:#991b1b;font-weight:900">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <form method="GET" action="{{ route('employee-deductions.index') }}">
                <div class="filters">
                    <div class="field">
                        <label>بحث</label>
                        <input name="search" value="{{ request('search') }}" placeholder="اسم الموظف أو الرقم الوظيفي">
                    </div>

                    <div class="field">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">الكل</option>
                            <option value="pending" @selected(request('status') === 'pending')>بانتظار الاعتماد</option>
                            <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                            <option value="active" @selected(request('status') === 'active')>نشط</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                            <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>طريقة الخصم</label>
                        <select name="deduction_mode">
                            <option value="">الكل</option>
                            <option value="one_time" @selected(request('deduction_mode') === 'one_time')>مرة واحدة</option>
                            <option value="monthly" @selected(request('deduction_mode') === 'monthly')>كل شهر</option>
                            <option value="selected_months" @selected(request('deduction_mode') === 'selected_months')>أشهر محددة</option>
                            <option value="installments" @selected(request('deduction_mode') === 'installments')>أقساط</option>
                            <option value="percentage" @selected(request('deduction_mode') === 'percentage')>نسبة</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>نوع الاستقطاع</label>
                        <select name="deduction_type_id">
                            <option value="">الكل</option>
                            @foreach($deductionTypes as $type)
                                <option value="{{ $type->id }}" @selected(request('deduction_type_id') == $type->id)>
                                    {{ $type->name_ar }}
                                    @if(!empty($type->code))
                                        - {{ $type->code }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="btn2 primary" type="submit">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a class="btn2 soft" href="{{ route('employee-deductions.index') }}">
                            <i class="fas fa-rotate-right"></i>
                            مسح
                        </a>

                        <a class="btn2 green" href="{{ route('employee-deductions.create') }}">
                            <i class="fas fa-plus"></i>
                            إضافة
                        </a>

                        <a class="btn2 blue" href="{{ route('deduction-types.index') }}">
                            <i class="fas fa-tags"></i>
                            أنواع الاستقطاعات
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>رقم الاستقطاع</th>
                        <th>الموظف</th>
                        <th>الاستقطاع</th>
                        <th>المبلغ / النسبة</th>
                        <th>طريقة الخصم</th>
                        <th>الجدولة</th>
                        <th>التقدم</th>
                        <th>الحالة</th>
                        <th>السبب</th>
                        <th>الإجراء</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($deductions as $deduction)
                        @php
                            $modeName = $deduction->deduction_mode_name ?? match ($deduction->deduction_mode) {
                                'one_time' => 'مرة واحدة',
                                'monthly' => 'كل شهر',
                                'selected_months' => 'أشهر محددة',
                                'installments' => 'أقساط',
                                'percentage' => 'نسبة',
                                default => $deduction->deduction_mode ?: '-',
                            };

                            $statusName = $deduction->status_name ?? match ($deduction->status) {
                                'pending' => 'بانتظار الاعتماد',
                                'approved' => 'معتمد',
                                'active' => 'نشط',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                default => $deduction->status ?: '-',
                            };

                            $schedulesCount = $deduction->schedules_count ?? 0;
                            $pendingSchedulesCount = $deduction->pending_schedules_count ?? 0;
                            $deductedSchedulesCount = $deduction->deducted_schedules_count ?? 0;

                            $progress = 0;
                            if ($schedulesCount > 0) {
                                $progress = round(($deductedSchedulesCount / $schedulesCount) * 100, 1);
                            } elseif (($deduction->progress_percentage ?? 0) > 0) {
                                $progress = $deduction->progress_percentage;
                            }

                            $mainAmount = $deduction->deduction_mode === 'percentage'
                                ? number_format((float)($deduction->percentage ?? $deduction->amount), 2) . '%'
                                : number_format((float)($deduction->total_amount ?? $deduction->amount), 2);

                            $remainingAmount = $deduction->remaining_amount ?? null;
                            $deductedAmount = $deduction->deducted_amount ?? null;
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $deduction->deduction_number }}</strong>
                            </td>

                            <td class="employee-cell">
                                <div class="employee-name">{{ $deduction->employee->display_name ?? '-' }}</div>
                                <div class="employee-number">{{ $deduction->employee->employee_number ?? '-' }}</div>
                                <div class="employee-number">{{ $deduction->employee->department->name ?? '' }}</div>
                            </td>

                            <td class="deduction-title">
                                <strong>{{ $deduction->title ?: ($deduction->deductionType->name_ar ?? $deduction->deduction_type) }}</strong>
                                <small>
                                    {{ $deduction->deductionType->name_ar ?? $deduction->deduction_type }}
                                    @if(!empty($deduction->deductionType?->code))
                                        - {{ $deduction->deductionType->code }}
                                    @endif
                                </small>
                            </td>

                            <td>
                                <div class="amount-main">{{ $mainAmount }}</div>

                                @if($deduction->deduction_mode === 'installments')
                                    <div class="amount-sub">
                                        {{ $deduction->installments_count ?? '-' }} أقساط
                                        @if($deduction->monthly_amount)
                                            - {{ number_format((float)$deduction->monthly_amount, 2) }} شهريًا
                                        @endif
                                    </div>
                                @elseif($deduction->deduction_mode === 'monthly')
                                    <div class="amount-sub">
                                        شهريًا: {{ number_format((float)($deduction->monthly_amount ?: $deduction->amount), 2) }}
                                    </div>
                                @elseif($deduction->deduction_mode === 'percentage')
                                    <div class="amount-sub">من إجمالي راتب شهر المسير</div>
                                @endif
                            </td>

                            <td>
                                <span class="pill mode-pill">{{ $modeName }}</span>
                            </td>

                            <td>
                                <div class="schedule-box">
                                    @if($deduction->deduction_mode === 'selected_months' && is_array($deduction->selected_months))
                                        <div class="schedule-line">
                                            <i class="fas fa-calendar-check"></i>
                                            {{ count($deduction->selected_months) }} أشهر محددة
                                        </div>
                                    @elseif($deduction->deduction_mode === 'one_time')
                                        <div class="schedule-line">
                                            <i class="fas fa-calendar-day"></i>
                                            {{ $deduction->start_month ?: optional($deduction->start_date)->format('Y-m') ?: '-' }}
                                        </div>
                                    @else
                                        <div class="schedule-line">
                                            <i class="fas fa-calendar"></i>
                                            {{ $deduction->start_month ?: optional($deduction->start_date)->format('Y-m') ?: '-' }}
                                            @if($deduction->end_month || $deduction->end_date)
                                                إلى {{ $deduction->end_month ?: optional($deduction->end_date)->format('Y-m') }}
                                            @endif
                                        </div>
                                    @endif

                                    @if($schedulesCount > 0)
                                        <div class="schedule-line">
                                            <i class="fas fa-list-check"></i>
                                            المجدول: {{ $schedulesCount }}
                                            |
                                            المتبقي: {{ $pendingSchedulesCount }}
                                        </div>
                                    @else
                                        <div class="schedule-line" style="color:#92400e">
                                            <i class="fas fa-clock"></i>
                                            لم يتم توليد الجدولة بعد
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="progress-wrap">
                                    <div class="progress-head">
                                        <span>{{ $progress }}%</span>
                                        <span>{{ $deductedSchedulesCount }}/{{ $schedulesCount }}</span>
                                    </div>

                                    <div class="progress-track">
                                        <div class="progress-fill" style="width:{{ min(100, max(0, $progress)) }}%"></div>
                                    </div>

                                    @if($deductedAmount !== null || $remainingAmount !== null)
                                        <div class="amount-sub">
                                            مخصوم: {{ number_format((float)$deductedAmount, 2) }}
                                            <br>
                                            متبقي: {{ number_format((float)$remainingAmount, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="pill {{ $deduction->status }}">
                                    {{ $statusName }}
                                </span>
                            </td>

                            <td class="reason-cell">
                                {{ \Illuminate\Support\Str::limit($deduction->reason ?? '-', 55) }}
                            </td>

                            <td>
                                <div class="actions">
                                    @if(in_array($deduction->status, ['pending','approved','active'], true))
                                        @if(($deductedSchedulesCount ?? 0) == 0)
                                            <a class="btn2 blue" href="{{ route('employee-deductions.edit', $deduction) }}">
                                                <i class="fas fa-edit"></i>
                                                تعديل
                                            </a>
                                        @endif
                                    @endif

                                    @if($deduction->status === 'pending')
                                        <form method="POST" action="{{ route('employee-deductions.approve', $deduction) }}" style="display:inline">
                                            @csrf
                                            <button class="btn2 green" type="submit">
                                                <i class="fas fa-check"></i>
                                                اعتماد
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($deduction->status, ['pending','approved','active'], true))
                                        <form method="POST" action="{{ route('employee-deductions.cancel', $deduction) }}" style="display:inline" onsubmit="return confirm('هل تريد إلغاء هذا الاستقطاع؟ سيتم إلغاء الخصومات المعلقة فقط.');">
                                            @csrf
                                            <button class="btn2 red" type="submit">
                                                <i class="fas fa-ban"></i>
                                                إلغاء
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty">
                                لا توجد استقطاعات.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:14px">
                {{ $deductions->links() }}
            </div>
        </div>
    </div>
@endsection
