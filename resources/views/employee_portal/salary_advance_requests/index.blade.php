@extends('layouts.employee_portal')

@section('title', 'طلبات السلف')

@section('content')

    <style>
        .advance-request-card {
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 22px;
            background: #fbfaff;
            box-shadow: 0 14px 34px rgba(76, 59, 145, .06);
            overflow: hidden;
        }

        .advance-request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .advance-request-title {
            display: grid;
            gap: 4px;
        }

        .advance-request-title strong {
            color: #111827;
            font-size: 16px;
            font-weight: 900;
        }

        .advance-request-title span {
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
        }

        .advance-meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            color: #6b7280;
            font-weight: 800;
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .progress-wrapper {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 22px 18px;
            overflow: hidden;
        }

        .progress-track {
            position: relative;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            max-width: 100%;
            isolation: isolate;
        }

        .progress-track::before {
            content: "";
            position: absolute;
            top: 24px;
            right: 12.5%;
            left: 12.5%;
            height: 5px;
            background: #e5e7eb;
            border-radius: 999px;
            z-index: 1;
        }

        .progress-track::after {
            content: "";
            position: absolute;
            top: 24px;
            right: 12.5%;
            height: 5px;
            background: linear-gradient(90deg, #6d5bd0, #16a34a);
            border-radius: 999px;
            z-index: 2;
            width: min(var(--progress-width, 0%), 75%);
            max-width: 75%;
        }

        .progress-step {
            position: relative;
            z-index: 3;
            display: grid;
            justify-items: center;
            text-align: center;
            gap: 8px;
        }

        .progress-dot {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #6b7280;
            border: 5px solid #fff;
            box-shadow: 0 0 0 1px #e5e7eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
        }

        .progress-step.done .progress-dot {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 0 0 1px #bbf7d0;
        }

        .progress-step.current .progress-dot {
            background: #f59e0b;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, .16);
        }

        .progress-step.rejected .progress-dot {
            background: #dc2626;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, .14);
        }

        .progress-step.waiting .progress-dot {
            background: #f3f4f6;
            color: #9ca3af;
        }

        .progress-label {
            color: #111827;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.6;
        }

        .progress-desc {
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.7;
            min-height: 36px;
        }

        .status-pill {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.5;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background: #e5e7eb;
            color: #374151;
        }

        .reason-box {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            font-weight: 800;
            line-height: 1.8;
        }

        .reason-box.rejected {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .reason-box.cancelled {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .reason-box.info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .reason-box.success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .advance-actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .advance-summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .advance-summary-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 12px 26px rgba(76, 59, 145, .06);
            position: relative;
            overflow: hidden;
        }

        .advance-summary-card::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 6px;
            height: 100%;
            background: #6d5bd0;
        }

        .advance-summary-card.orange::before { background: #f59e0b; }
        .advance-summary-card.blue::before { background: #3b82f6; }
        .advance-summary-card.green::before { background: #16a34a; }
        .advance-summary-card.red::before { background: #dc2626; }
        .advance-summary-card.gray::before { background: #6b7280; }

        .advance-summary-card span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .advance-summary-card strong {
            display: block;
            color: #111827;
            font-size: 24px;
            font-weight: 900;
            line-height: 1;
        }

        .advance-info-note {
            background: #f8f7ff;
            border: 1px solid #e7e0ff;
            border-radius: 18px;
            padding: 14px 16px;
            color: #4c3b91;
            font-weight: 900;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        @media (max-width: 850px) {
            .advance-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .advance-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .progress-track {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .progress-track::before,
            .progress-track::after {
                display: none;
            }

            .progress-step {
                grid-template-columns: 52px 1fr;
                justify-items: start;
                text-align: right;
                align-items: center;
            }

            .progress-step-text {
                display: grid;
                gap: 2px;
            }

            .progress-desc {
                min-height: auto;
            }
        }

        @media (max-width: 520px) {
            .advance-summary-grid,
            .advance-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="portal-topbar">
        <div class="portal-title">
            <h2>طلبات السلف</h2>
            <p>{{ $employee->display_name }} — متابعة طلبات السلف ومسار الموافقات</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('employee-portal.salary-advance-requests.create') }}" class="portal-btn">طلب سلفة جديد</a>

            <form method="POST" action="{{ route('employee-portal.logout') }}">
                @csrf
                <button type="submit" class="portal-btn danger">خروج</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @php
        $advanceRequestsBaseQuery = \App\Models\SalaryAdvanceRequest::query()
            ->where('employee_id', $employee->id);

        $advanceStats = [
            'total' => (clone $advanceRequestsBaseQuery)->count(),
            'pending_manager' => (clone $advanceRequestsBaseQuery)->where('workflow_status', 'pending_manager')->count(),
            'pending_hr' => (clone $advanceRequestsBaseQuery)->where('workflow_status', 'manager_approved_pending_hr')->count(),
            'registered' => (clone $advanceRequestsBaseQuery)
                ->where(function ($query) {
                    $query->where('workflow_status', 'registered')
                        ->orWhere('workflow_status', 'approved_by_hr')
                        ->orWhere('status', 'approved');
                })
                ->count(),
            'rejected' => (clone $advanceRequestsBaseQuery)->whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])->count(),
            'cancelled' => (clone $advanceRequestsBaseQuery)->where('workflow_status', 'cancelled')->count(),
        ];
    @endphp

    <div class="advance-summary-grid">
        <div class="advance-summary-card">
            <span>إجمالي طلباتي</span>
            <strong>{{ $advanceStats['total'] ?? 0 }}</strong>
        </div>

        <div class="advance-summary-card orange">
            <span>بانتظار المدير</span>
            <strong>{{ $advanceStats['pending_manager'] ?? 0 }}</strong>
        </div>

        <div class="advance-summary-card blue">
            <span>بانتظار HR</span>
            <strong>{{ $advanceStats['pending_hr'] ?? 0 }}</strong>
        </div>

        <div class="advance-summary-card green">
            <span>مسجلة ومعتمدة</span>
            <strong>{{ $advanceStats['registered'] ?? 0 }}</strong>
        </div>

        <div class="advance-summary-card red">
            <span>مرفوضة</span>
            <strong>{{ $advanceStats['rejected'] ?? 0 }}</strong>
        </div>

        <div class="advance-summary-card gray">
            <span>ملغاة</span>
            <strong>{{ $advanceStats['cancelled'] ?? 0 }}</strong>
        </div>
    </div>

    <div class="advance-info-note">
        يمكنك متابعة مسار كل طلب من مرحلة الإرسال إلى موافقة المدير المباشر ثم الموارد البشرية وحتى تسجيل السلفة.
    </div>

    <div class="portal-card">
        <div style="display:grid; gap:16px;">
            @forelse($salaryAdvanceRequests as $salaryAdvanceRequest)
                @php
                    $workflowStatus = $salaryAdvanceRequest->workflow_status ?? 'pending_manager';

                    $workflowName = match($workflowStatus) {
                        'pending_manager' => 'قيد المراجعة من المدير المباشر',
                        'manager_approved_pending_hr' => 'وافق المدير - بانتظار الموارد البشرية',
                        'rejected_by_manager' => 'مرفوضة من المدير المباشر',
                        'registered' => 'تم اعتمادها وتسجيل السلفة',
                        'approved_by_hr' => 'مقبولة من الموارد البشرية',
                        'rejected_by_hr' => 'مرفوضة من الموارد البشرية',
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

                    $step1Class = 'done';
                    $step2Class = 'waiting';
                    $step3Class = 'waiting';
                    $step4Class = 'waiting';

                    $progressWidth = '0%';

                    $step1Icon = '✓';
                    $step2Icon = '2';
                    $step3Icon = '3';
                    $step4Icon = '4';

                    $step1Desc = 'تم إرسال طلب السلفة';
                    $step2Desc = 'بانتظار قرار المدير المباشر';
                    $step3Desc = 'لم يصل إلى الموارد البشرية بعد';
                    $step4Desc = 'لم يتم تسجيل السلفة بعد';

                    if ($workflowStatus === 'pending_manager') {
                        $step1Class = 'done';
                        $step2Class = 'current';
                        $step3Class = 'waiting';
                        $step4Class = 'waiting';
                        $progressWidth = '18%';

                        $step2Icon = '…';
                        $step2Desc = 'قيد المراجعة من المدير المباشر';
                        $step3Desc = 'لم يصل إلى الموارد البشرية بعد';
                        $step4Desc = 'بانتظار إكمال الموافقات';
                    }

                    if ($workflowStatus === 'manager_approved_pending_hr') {
                        $step1Class = 'done';
                        $step2Class = 'done';
                        $step3Class = 'current';
                        $step4Class = 'waiting';
                        $progressWidth = '50%';

                        $step2Icon = '✓';
                        $step2Desc = 'تمت موافقة المدير المباشر';
                        $step3Icon = '…';
                        $step3Desc = 'قيد المعالجة من الموارد البشرية';
                        $step4Desc = 'بانتظار تسجيل السلفة';
                    }

                    if ($workflowStatus === 'rejected_by_manager') {
                        $step1Class = 'done';
                        $step2Class = 'rejected';
                        $step3Class = 'waiting';
                        $step4Class = 'waiting';
                        $progressWidth = '18%';

                        $step2Icon = '×';
                        $step2Desc = 'تم رفض الطلب من المدير المباشر';
                        $step3Desc = 'لن يتم تحويل الطلب إلى الموارد البشرية';
                        $step4Desc = 'تم إنهاء الطلب';
                    }

                    if ($workflowStatus === 'registered' || $workflowStatus === 'approved_by_hr') {
                        $step1Class = 'done';
                        $step2Class = 'done';
                        $step3Class = 'done';
                        $step4Class = 'done';
                        $progressWidth = '75%';

                        $step2Icon = '✓';
                        $step2Desc = 'تمت موافقة المدير المباشر';
                        $step3Icon = '✓';
                        $step3Desc = 'تم اعتماد الموارد البشرية';
                        $step4Icon = '✓';
                        $step4Desc = 'تم تسجيل السلفة وجدولة الأقساط';
                    }

                    if ($workflowStatus === 'rejected_by_hr') {
                        $step1Class = 'done';
                        $step2Class = 'done';
                        $step3Class = 'rejected';
                        $step4Class = 'waiting';
                        $progressWidth = '50%';

                        $step2Icon = '✓';
                        $step2Desc = 'تمت موافقة المدير المباشر';
                        $step3Icon = '×';
                        $step3Desc = 'تم رفض الطلب من الموارد البشرية';
                        $step4Desc = 'تم إنهاء الطلب';
                    }

                    if ($workflowStatus === 'cancelled') {
                        $step1Class = 'done';
                        $step2Class = $salaryAdvanceRequest->direct_manager_status === 'approved' ? 'done' : 'waiting';
                        $step3Class = $salaryAdvanceRequest->hr_status === 'approved' || $salaryAdvanceRequest->status === 'approved' ? 'done' : 'waiting';
                        $step4Class = 'rejected';
                        $progressWidth = '75%';

                        $step2Icon = $step2Class === 'done' ? '✓' : '-';
                        $step3Icon = $step3Class === 'done' ? '✓' : '-';
                        $step4Icon = '×';

                        $step1Desc = 'تم إرسال الطلب';
                        $step2Desc = $step2Class === 'done' ? 'تمت موافقة المدير المباشر قبل الإلغاء' : 'لم تتم موافقة المدير قبل الإلغاء';
                        $step3Desc = $step3Class === 'done' ? 'تم اعتماد الموارد البشرية قبل الإلغاء' : 'لم يتم اعتماد الموارد البشرية قبل الإلغاء';
                        $step4Desc = 'تم إلغاء الطلب';
                    }

                    $cancelReason = $salaryAdvanceRequest->direct_manager_reject_reason
                        ?? $salaryAdvanceRequest->hr_reject_reason
                        ?? null;
                @endphp

                <div class="advance-request-card">
                    <div class="advance-request-header">
                        <div class="advance-request-title">
                            <strong>طلب سلفة {{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</strong>
                            <span>
                                رقم الطلب:
                                {{ $salaryAdvanceRequest->request_number ?? '#' . $salaryAdvanceRequest->id }}
                            </span>
                        </div>

                        <span class="status-pill {{ $workflowClass }}">
                            {{ $workflowName }}
                        </span>
                    </div>

                    <div class="advance-meta-grid">
                        <div>المبلغ المطلوب: {{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</div>
                        <div>المبلغ المعتمد: {{ $salaryAdvanceRequest->approved_amount ? number_format((float) $salaryAdvanceRequest->approved_amount, 2) : '-' }}</div>
                        <div>الأقساط: {{ $salaryAdvanceRequest->installments_count }}</div>
                        <div>بداية الخصم: {{ optional($salaryAdvanceRequest->deduction_start_date)->format('Y-m') }}</div>
                        <div>تاريخ الطلب: {{ optional($salaryAdvanceRequest->created_at)->format('Y-m-d') }}</div>
                        <div>رقم السلفة: {{ $salaryAdvanceRequest->registeredSalaryAdvance->advance_number ?? '-' }}</div>
                    </div>

                    <div class="progress-wrapper">
                        <div class="progress-track" style="--progress-width: {{ $progressWidth }};">
                            <div class="progress-step {{ $step1Class }}">
                                <div class="progress-dot">{{ $step1Icon }}</div>
                                <div class="progress-step-text">
                                    <div class="progress-label">إرسال الطلب</div>
                                    <div class="progress-desc">{{ $step1Desc }}</div>
                                </div>
                            </div>

                            <div class="progress-step {{ $step2Class }}">
                                <div class="progress-dot">{{ $step2Icon }}</div>
                                <div class="progress-step-text">
                                    <div class="progress-label">المدير المباشر</div>
                                    <div class="progress-desc">{{ $step2Desc }}</div>
                                </div>
                            </div>

                            <div class="progress-step {{ $step3Class }}">
                                <div class="progress-dot">{{ $step3Icon }}</div>
                                <div class="progress-step-text">
                                    <div class="progress-label">الموارد البشرية</div>
                                    <div class="progress-desc">{{ $step3Desc }}</div>
                                </div>
                            </div>

                            <div class="progress-step {{ $step4Class }}">
                                <div class="progress-dot">{{ $step4Icon }}</div>
                                <div class="progress-step-text">
                                    <div class="progress-label">تسجيل السلفة</div>
                                    <div class="progress-desc">{{ $step4Desc }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($salaryAdvanceRequest->direct_manager_reject_reason)
                        <div class="reason-box rejected">
                            سبب رفض المدير المباشر: {{ $salaryAdvanceRequest->direct_manager_reject_reason }}
                        </div>
                    @endif

                    @if($salaryAdvanceRequest->hr_reject_reason)
                        <div class="reason-box rejected">
                            سبب رفض الموارد البشرية: {{ $salaryAdvanceRequest->hr_reject_reason }}
                        </div>
                    @endif

                    @if($workflowStatus === 'manager_approved_pending_hr')
                        <div class="reason-box info">
                            تمت موافقة المدير المباشر، والطلب الآن بانتظار قرار الموارد البشرية.
                        </div>
                    @endif

                    @if($workflowStatus === 'registered' && $salaryAdvanceRequest->registeredSalaryAdvance)
                        <div class="reason-box success">
                            تم تسجيل السلفة برقم:
                            <strong>{{ $salaryAdvanceRequest->registeredSalaryAdvance->advance_number }}</strong>
                            وسيتم خصم الأقساط حسب الجدولة المعتمدة.
                        </div>
                    @endif

                    @if($workflowStatus === 'cancelled')
                        <div class="reason-box cancelled">
                            <strong>تم إلغاء الطلب نهائيًا.</strong>
                            <br>
                            لا يعود الطلب إلى المدير المباشر أو الموارد البشرية بعد الإلغاء.
                            @if($cancelReason)
                                <br>
                                <strong>سبب الإلغاء:</strong> {{ $cancelReason }}
                            @endif
                        </div>
                    @endif

                    <div class="advance-actions">
                        <a href="{{ route('employee-portal.salary-advance-requests.show', $salaryAdvanceRequest) }}" class="portal-btn secondary">
                            عرض التفاصيل
                        </a>

                        @if($salaryAdvanceRequest->can_employee_cancel)
                            <form method="POST" action="{{ route('employee-portal.salary-advance-requests.cancel', $salaryAdvanceRequest) }}">
                                @csrf
                                <button type="submit" class="portal-btn danger" onclick="return confirm('تأكيد إلغاء طلب السلفة؟')">
                                    إلغاء الطلب
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#6b7280; font-weight:800; padding:30px;">
                    لا توجد طلبات سلف حتى الآن.
                </div>
            @endforelse
        </div>

        <div style="margin-top:16px;">
            {{ $salaryAdvanceRequests->links() }}
        </div>
    </div>
@endsection
