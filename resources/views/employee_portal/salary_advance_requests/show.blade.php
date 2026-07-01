@extends('layouts.employee_portal')

@section('title', 'تفاصيل طلب السلفة')

@section('content')

    <style>
        .advance-show-page,
        .advance-show-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .advance-show-hero {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            border-radius: 26px;
            padding: 26px;
            color: #fff;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            box-shadow: 0 22px 55px rgba(76, 59, 145, .22);
        }

        .advance-show-hero h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 900;
        }

        .advance-show-hero p {
            margin: 0;
            opacity: .92;
            font-weight: 700;
            line-height: 1.8;
        }

        .advance-show-hero-icon {
            width: 68px;
            height: 68px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            font-weight: 900;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .details-box {
            background: #fbfaff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 10px 22px rgba(76, 59, 145, .04);
        }

        .details-box span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .details-box strong {
            display: block;
            color: #111827;
            font-size: 16px;
            font-weight: 900;
            word-break: break-word;
        }

        .section-title {
            margin: 0 0 16px;
            color: #4c3b91;
            font-size: 18px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title span {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #f1edff;
            color: #6d5bd0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .progress-wrapper {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 22px 18px;
            overflow: hidden;
            margin-bottom: 18px;
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

        .text-card {
            background: #fbfaff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 15px;
            color: #374151;
            font-weight: 800;
            line-height: 1.9;
            margin-bottom: 12px;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-item {
            background: #fbfaff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 14px;
            display: grid;
            gap: 6px;
        }

        .timeline-item strong {
            color: #4c3b91;
            font-weight: 900;
        }

        .timeline-item small {
            color: #6b7280;
            font-weight: 800;
            line-height: 1.7;
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            padding: 10px 12px;
            border-radius: 13px;
            text-decoration: none;
            font-weight: 900;
        }

        @media (max-width: 900px) {
            .details-grid {
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

        @media (max-width: 600px) {
            .advance-show-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .top-actions .portal-btn {
                width: 100%;
                justify-content: center;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

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
            $step2Class = 'current';
            $progressWidth = '18%';
            $step2Icon = '…';
            $step2Desc = 'قيد المراجعة من المدير المباشر';
            $step4Desc = 'بانتظار إكمال الموافقات';
        }

        if ($workflowStatus === 'manager_approved_pending_hr') {
            $step2Class = 'done';
            $step3Class = 'current';
            $progressWidth = '50%';
            $step2Icon = '✓';
            $step2Desc = 'تمت موافقة المدير المباشر';
            $step3Icon = '…';
            $step3Desc = 'قيد المعالجة من الموارد البشرية';
            $step4Desc = 'بانتظار تسجيل السلفة';
        }

        if ($workflowStatus === 'rejected_by_manager') {
            $step2Class = 'rejected';
            $progressWidth = '18%';
            $step2Icon = '×';
            $step2Desc = 'تم رفض الطلب من المدير المباشر';
            $step3Desc = 'لن يتم تحويل الطلب إلى الموارد البشرية';
            $step4Desc = 'تم إنهاء الطلب';
        }

        if ($workflowStatus === 'registered' || $workflowStatus === 'approved_by_hr') {
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
            $step2Class = 'done';
            $step3Class = 'rejected';
            $progressWidth = '50%';
            $step2Icon = '✓';
            $step2Desc = 'تمت موافقة المدير المباشر';
            $step3Icon = '×';
            $step3Desc = 'تم رفض الطلب من الموارد البشرية';
            $step4Desc = 'تم إنهاء الطلب';
        }

        if ($workflowStatus === 'cancelled') {
            $step2Class = $salaryAdvanceRequest->direct_manager_status === 'approved' ? 'done' : 'waiting';
            $step3Class = $salaryAdvanceRequest->hr_status === 'approved' || $salaryAdvanceRequest->status === 'approved' ? 'done' : 'waiting';
            $step4Class = 'rejected';
            $progressWidth = '75%';
            $step2Icon = $step2Class === 'done' ? '✓' : '-';
            $step3Icon = $step3Class === 'done' ? '✓' : '-';
            $step4Icon = '×';
            $step2Desc = $step2Class === 'done' ? 'تمت موافقة المدير قبل الإلغاء' : 'لم تتم موافقة المدير قبل الإلغاء';
            $step3Desc = $step3Class === 'done' ? 'تم اعتماد الموارد البشرية قبل الإلغاء' : 'لم يتم اعتماد الموارد البشرية قبل الإلغاء';
            $step4Desc = 'تم إلغاء الطلب';
        }
    @endphp

    <div class="advance-show-page">

        <div class="advance-show-hero">
            <div>
                <h2>تفاصيل طلب السلفة</h2>
                <p>{{ $employee->display_name }} — {{ $salaryAdvanceRequest->request_number ?? '#' . $salaryAdvanceRequest->id }}</p>
            </div>

            <div class="advance-show-hero-icon">
                SAR
            </div>
        </div>

        <div class="top-actions">
            <a href="{{ route('employee-portal.salary-advance-requests.index') }}" class="portal-btn secondary">رجوع لطلباتي</a>

            @if($salaryAdvanceRequest->can_employee_cancel)
                <form method="POST" action="{{ route('employee-portal.salary-advance-requests.cancel', $salaryAdvanceRequest) }}">
                    @csrf
                    <button type="submit" class="portal-btn danger" onclick="return confirm('تأكيد إلغاء طلب السلفة؟')">
                        إلغاء الطلب
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="portal-card">
            <h3 class="section-title">
                <span>1</span>
                ملخص الطلب
            </h3>

            <div class="details-grid">
                <div class="details-box">
                    <span>حالة الطلب</span>
                    <strong><span class="status-pill {{ $workflowClass }}">{{ $workflowName }}</span></strong>
                </div>

                <div class="details-box">
                    <span>المبلغ المطلوب</span>
                    <strong>{{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</strong>
                </div>

                <div class="details-box">
                    <span>المبلغ المعتمد</span>
                    <strong>{{ $salaryAdvanceRequest->approved_amount ? number_format((float) $salaryAdvanceRequest->approved_amount, 2) : '-' }}</strong>
                </div>

                <div class="details-box">
                    <span>عدد الأقساط</span>
                    <strong>{{ $salaryAdvanceRequest->installments_count }}</strong>
                </div>

                <div class="details-box">
                    <span>قيمة القسط</span>
                    <strong>{{ $salaryAdvanceRequest->installment_amount ? number_format((float) $salaryAdvanceRequest->installment_amount, 2) : '-' }}</strong>
                </div>

                <div class="details-box">
                    <span>بداية الخصم</span>
                    <strong>{{ optional($salaryAdvanceRequest->deduction_start_date)->format('Y-m') }}</strong>
                </div>

                <div class="details-box">
                    <span>تاريخ الطلب</span>
                    <strong>{{ optional($salaryAdvanceRequest->created_at)->format('Y-m-d') }}</strong>
                </div>

                <div class="details-box">
                    <span>رقم السلفة</span>
                    <strong>{{ $salaryAdvanceRequest->registeredSalaryAdvance->advance_number ?? '-' }}</strong>
                </div>
            </div>

            @if($salaryAdvanceRequest->reason)
                <div class="text-card">
                    <strong style="color:#4c3b91;">سبب طلب السلفة:</strong>
                    <br>
                    {{ $salaryAdvanceRequest->reason }}
                </div>
            @endif

            @if($salaryAdvanceRequest->notes)
                <div class="text-card">
                    <strong style="color:#4c3b91;">ملاحظات الموظف:</strong>
                    <br>
                    {{ $salaryAdvanceRequest->notes }}
                </div>
            @endif

            @if($salaryAdvanceRequest->attachment)
                <div class="text-card">
                    <strong style="color:#4c3b91;">المرفق:</strong>
                    <br><br>
                    <a class="attachment-link" href="{{ asset('storage/' . $salaryAdvanceRequest->attachment) }}" target="_blank">
                        📎 عرض المرفق
                    </a>
                </div>
            @endif
        </div>

        <div class="portal-card" style="margin-top:16px;">
            <h3 class="section-title">
                <span>2</span>
                مسار الموافقة
            </h3>

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
        </div>

        <div class="portal-card" style="margin-top:16px;">
            <h3 class="section-title">
                <span>3</span>
                سجل الحركة
            </h3>

            <div class="timeline">
                @forelse($salaryAdvanceRequest->logs as $log)
                    <div class="timeline-item">
                        <strong>{{ $log->transaction_type }}</strong>
                        <small>{{ optional($log->created_at)->format('Y-m-d H:i') }}</small>
                        <small>{{ $log->description }}</small>
                    </div>
                @empty
                    <div class="text-card">
                        لا توجد حركات مسجلة على هذا الطلب.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
