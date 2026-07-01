@extends('layouts.employee_portal')

@section('title', 'تفاصيل طلب الإجازة')

@section('content')

    @php
        $workflowStatus = $leaveRequest->workflow_status ?? 'pending_manager';

        $workflowName = match($workflowStatus) {
            'pending_manager' => 'قيد المراجعة من المدير المباشر',
            'manager_approved_pending_hr' => 'وافق المدير - بانتظار الموارد البشرية',
            'approved_by_hr' => 'مقبولة من الموارد البشرية',
            'rejected_by_manager' => 'مرفوضة من المدير المباشر',
            'rejected_by_hr' => 'مرفوضة من الموارد البشرية',
            'cancelled' => 'ملغاة',
            default => 'قيد المراجعة',
        };

        $workflowClass = match($workflowStatus) {
            'manager_approved_pending_hr' => 'status-processing',
            'approved_by_hr' => 'status-approved',
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

        $step1Desc = 'تم إرسال الطلب';
        $step2Desc = 'بانتظار قرار المدير المباشر';
        $step3Desc = 'لم يصل إلى الموارد البشرية بعد';
        $step4Desc = 'لا يوجد إجراء';

        if ($workflowStatus === 'pending_manager') {
            $step2Class = 'current';
            $progressWidth = '18%';
            $step2Icon = '…';
            $step2Desc = 'قيد المراجعة من المدير المباشر';
            $step4Desc = 'لم يتم الإغلاق بعد';
        }

        if ($workflowStatus === 'manager_approved_pending_hr') {
            $step2Class = 'done';
            $step3Class = 'current';
            $progressWidth = '50%';
            $step2Icon = '✓';
            $step3Icon = '…';
            $step2Desc = 'تمت موافقة المدير المباشر';
            $step3Desc = 'قيد المعالجة من الموارد البشرية';
            $step4Desc = 'بانتظار القرار النهائي';
        }

        if ($workflowStatus === 'rejected_by_manager') {
            $step2Class = 'rejected';
            $progressWidth = '18%';
            $step2Icon = '×';
            $step2Desc = 'تم رفض الطلب من المدير المباشر';
            $step3Desc = 'لن يتم تحويل الطلب إلى الموارد البشرية';
            $step4Desc = 'تم إنهاء الطلب';
        }

        if ($workflowStatus === 'approved_by_hr') {
            $step2Class = 'done';
            $step3Class = 'done';
            $step4Class = 'done';
            $progressWidth = '75%';
            $step2Icon = '✓';
            $step3Icon = '✓';
            $step4Icon = '✓';
            $step2Desc = 'تمت موافقة المدير المباشر';
            $step3Desc = 'تم الاعتماد النهائي من الموارد البشرية';
            $step4Desc = 'تم إغلاق الطلب بالموافقة';
        }

        if ($workflowStatus === 'rejected_by_hr') {
            $step2Class = 'done';
            $step3Class = 'rejected';
            $progressWidth = '50%';
            $step2Icon = '✓';
            $step3Icon = '×';
            $step2Desc = 'تمت موافقة المدير المباشر';
            $step3Desc = 'تم رفض الطلب من الموارد البشرية';
            $step4Desc = 'تم إنهاء الطلب';
        }

        if ($workflowStatus === 'cancelled') {
            $step2Class = $leaveRequest->direct_manager_status === 'approved' ? 'done' : 'waiting';
            $step3Class = $leaveRequest->hr_status === 'approved' || $leaveRequest->status === 'approved' ? 'done' : 'waiting';
            $step4Class = 'rejected';
            $progressWidth = '75%';
            $step2Icon = $step2Class === 'done' ? '✓' : '-';
            $step3Icon = $step3Class === 'done' ? '✓' : '-';
            $step4Icon = '×';
            $step2Desc = $step2Class === 'done' ? 'تمت موافقة المدير المباشر قبل الإلغاء' : 'لم تتم موافقة المدير قبل الإلغاء';
            $step3Desc = $step3Class === 'done' ? 'تم اعتماد الموارد البشرية قبل الإلغاء' : 'لم يتم اعتماد الموارد البشرية قبل الإلغاء';
            $step4Desc = 'تم إلغاء الطلب من بوابة الموظف';
        }

        $cancelReason = $leaveRequest->reject_reason
            ?? $leaveRequest->direct_manager_reject_reason
            ?? $leaveRequest->hr_reject_reason
            ?? null;

        $canEmployeeCancelLeave = $leaveRequest->can_employee_cancel
            ?? in_array($workflowStatus, ['pending_manager', 'manager_approved_pending_hr']);

        $leaveLogs = $leaveLogs ?? collect();
    @endphp

    <style>
        .leave-details-card {
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 22px;
            background: #fbfaff;
            box-shadow: 0 14px 34px rgba(76, 59, 145, .06);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .leave-details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .leave-details-title {
            display: grid;
            gap: 4px;
        }

        .leave-details-title strong {
            color: #111827;
            font-size: 18px;
            font-weight: 900;
        }

        .leave-details-title span {
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .detail-box {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 8px 18px rgba(76, 59, 145, .04);
        }

        .detail-box span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .detail-box strong {
            display: block;
            color: #111827;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.6;
            word-break: break-word;
        }

        .progress-wrapper {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            padding: 22px 18px;
            overflow: hidden;
            margin-bottom: 16px;
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

        .progress-step.done .progress-dot { background: #16a34a; color: #fff; box-shadow: 0 0 0 1px #bbf7d0; }
        .progress-step.current .progress-dot { background: #f59e0b; color: #fff; box-shadow: 0 0 0 4px rgba(245, 158, 11, .16); }
        .progress-step.rejected .progress-dot { background: #dc2626; color: #fff; box-shadow: 0 0 0 4px rgba(220, 38, 38, .14); }
        .progress-step.waiting .progress-dot { background: #f3f4f6; color: #9ca3af; }

        .progress-label { color: #111827; font-size: 13px; font-weight: 900; line-height: 1.6; }
        .progress-desc { color: #6b7280; font-size: 11px; font-weight: 800; line-height: 1.7; min-height: 36px; }

        .status-pill {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.5;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e5e7eb; color: #374151; }

        .reason-box {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            font-weight: 800;
            line-height: 1.8;
        }

        .reason-box.rejected { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .reason-box.cancelled { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .reason-box.info { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .reason-box.success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }

        .leave-actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .section-title {
            margin: 0 0 14px;
            color: #4c3b91;
            font-size: 17px;
            font-weight: 900;
        }

        .timeline { display: grid; gap: 10px; }
        .timeline-item {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 16px;
            padding: 13px;
            box-shadow: 0 8px 18px rgba(76, 59, 145, .04);
        }

        .timeline-item strong {
            display: block;
            color: #4c3b91;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: 900;
        }

        .timeline-item small {
            display: block;
            color: #6b7280;
            font-weight: 800;
            line-height: 1.7;
        }

        @media (max-width: 850px) {
            .details-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .progress-track { grid-template-columns: 1fr; gap: 16px; }
            .progress-track::before, .progress-track::after { display: none; }
            .progress-step { grid-template-columns: 52px 1fr; justify-items: start; text-align: right; align-items: center; }
            .progress-step-text { display: grid; gap: 2px; }
            .progress-desc { min-height: auto; }
        }

        @media (max-width: 520px) {
            .details-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="portal-topbar">
        <div class="portal-title">
            <h2>تفاصيل طلب الإجازة</h2>
            <p>{{ $employee->display_name }} — متابعة تفاصيل الطلب ومسار الموافقات</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('employee-portal.leave-requests.index') }}" class="portal-btn secondary">رجوع لطلباتي</a>

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

    <div class="leave-details-card">
        <div class="leave-details-header">
            <div class="leave-details-title">
                <strong>{{ $leaveRequest->leaveType->name ?? '-' }}</strong>
                <span>رقم الطلب: #{{ $leaveRequest->id }}</span>
            </div>

            <span class="status-pill {{ $workflowClass }}">{{ $workflowName }}</span>
        </div>

        <div class="details-grid">
            <div class="detail-box"><span>نوع الإجازة</span><strong>{{ $leaveRequest->leaveType->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>من تاريخ</span><strong>{{ optional($leaveRequest->start_date)->format('Y-m-d') }}</strong></div>
            <div class="detail-box"><span>إلى تاريخ</span><strong>{{ optional($leaveRequest->end_date)->format('Y-m-d') }}</strong></div>
            <div class="detail-box"><span>عدد الأيام</span><strong>{{ $leaveRequest->days_count ?? '-' }}</strong></div>
            <div class="detail-box"><span>تاريخ الطلب</span><strong>{{ optional($leaveRequest->created_at)->format('Y-m-d H:i') }}</strong></div>
            <div class="detail-box"><span>آخر تحديث</span><strong>{{ optional($leaveRequest->updated_at)->format('Y-m-d H:i') }}</strong></div>
            <div class="detail-box"><span>حالة المدير</span><strong>{{ $leaveRequest->direct_manager_status ?? '-' }}</strong></div>
            <div class="detail-box"><span>حالة HR</span><strong>{{ $leaveRequest->hr_status ?? '-' }}</strong></div>
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
                        <div class="progress-label">الإغلاق النهائي</div>
                        <div class="progress-desc">{{ $step4Desc }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($leaveRequest->reason)
            <div class="reason-box info">
                <strong>سبب الطلب:</strong>
                <br>
                {{ $leaveRequest->reason }}
            </div>
        @endif

        @if($leaveRequest->direct_manager_reject_reason)
            <div class="reason-box rejected">
                سبب رفض المدير المباشر: {{ $leaveRequest->direct_manager_reject_reason }}
            </div>
        @endif

        @if($leaveRequest->hr_reject_reason)
            <div class="reason-box rejected">
                سبب رفض الموارد البشرية: {{ $leaveRequest->hr_reject_reason }}
            </div>
        @endif

        @if($workflowStatus === 'manager_approved_pending_hr')
            <div class="reason-box info">
                تمت موافقة المدير المباشر، والطلب الآن بانتظار قرار الموارد البشرية.
            </div>
        @endif

        @if($workflowStatus === 'approved_by_hr')
            <div class="reason-box success">
                تم اعتماد طلب الإجازة من الموارد البشرية، وتم إغلاق الطلب بالموافقة.
            </div>
        @endif

        @if($workflowStatus === 'cancelled')
            <div class="reason-box cancelled">
                <strong>تم إلغاء الطلب نهائيًا.</strong>
                <br>
                تم إلغاء الطلب من بوابة الموظف، ولا يعود إلى المدير المباشر أو الموارد البشرية بعد الإلغاء.
                @if($cancelReason)
                    <br>
                    <strong>سبب الإلغاء:</strong> {{ $cancelReason }}
                @endif
            </div>
        @endif

        @if($leaveRequest->attachment)
            <div class="reason-box info">
                <strong>المرفق:</strong>
                <br><br>
                <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank" class="portal-btn secondary">
                    عرض المرفق
                </a>
            </div>
        @endif

        <div class="leave-actions">
            <a href="{{ route('employee-portal.leave-requests.index') }}" class="portal-btn secondary">
                رجوع لطلباتي
            </a>

            @if($canEmployeeCancelLeave && \Illuminate\Support\Facades\Route::has('employee-portal.leave-requests.cancel'))
                <form method="POST" action="{{ route('employee-portal.leave-requests.cancel', $leaveRequest) }}">
                    @csrf
                    <button type="submit" class="portal-btn danger" onclick="return confirm('تأكيد إلغاء طلب الإجازة؟')">
                        إلغاء الطلب
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="leave-details-card">
        <h3 class="section-title">سجل الحركة</h3>

        <div class="timeline">
            @forelse($leaveLogs as $log)
                <div class="timeline-item">
                    <strong>{{ $log->transaction_type ?? $log->action ?? 'حركة على الطلب' }}</strong>
                    <small>{{ optional($log->created_at)->format('Y-m-d H:i') }}</small>
                    <small>{{ $log->description ?? $log->notes ?? '-' }}</small>
                </div>
            @empty
                <div style="text-align:center; color:#6b7280; font-weight:800; padding:20px;">
                    لا توجد حركات مسجلة على الطلب.
                </div>
            @endforelse
        </div>
    </div>
@endsection
