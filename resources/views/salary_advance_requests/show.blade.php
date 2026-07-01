@extends('layouts.hr')

@section('title', 'تفاصيل طلب السلفة')
@section('page-title', 'تفاصيل طلب السلفة')

@section('content')
    @php
        $workflowStatus = $salaryAdvanceRequest->workflow_status ?? 'pending_manager';

        $workflowName = match($workflowStatus) {
            'pending_manager' => 'قيد المراجعة من المدير المباشر',
            'manager_approved_pending_hr' => 'وافق المدير - بانتظار الموارد البشرية',
            'registered' => 'تم اعتمادها وتسجيل السلفة',
            'approved_by_hr' => 'مقبولة من الموارد البشرية',
            'rejected_by_manager' => 'مرفوضة من المدير المباشر',
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
    @endphp

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div>
                <h1>تفاصيل طلب السلفة</h1>
                <p>{{ $salaryAdvanceRequest->request_number ?? '#' . $salaryAdvanceRequest->id }}</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('salary-advance-requests.index') }}" class="hero-btn">رجوع للطلبات</a>
            @if($salaryAdvanceRequest->registeredSalaryAdvance)
                <a href="{{ route('salary-advances.show', $salaryAdvanceRequest->registeredSalaryAdvance) }}" class="hero-btn white">عرض السلفة المسجلة</a>
            @endif
        </div>
    </div>

    <style>
        .details-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:20px}
        .detail-box{background:#fbfaff;border:1px solid #eeeafc;border-radius:18px;padding:16px}
        .detail-box span{display:block;color:#6b7280;font-size:12px;font-weight:900;margin-bottom:8px}
        .detail-box strong{display:block;color:#3f3a68;font-size:16px;font-weight:900;word-break:break-word}
        .status-pill{display:inline-flex;padding:7px 12px;border-radius:999px;font-size:12px;font-weight:900;line-height:1.5}
        .status-pending{background:#fef3c7;color:#92400e}.status-processing{background:#dbeafe;color:#1d4ed8}.status-approved{background:#dcfce7;color:#166534}.status-rejected{background:#fee2e2;color:#991b1b}.status-cancelled{background:#e5e7eb;color:#374151}
        .text-box{background:#fbfaff;border:1px solid #eeeafc;border-radius:18px;padding:16px;margin-bottom:14px;font-weight:800;line-height:1.9;color:#374151}
        .section-title{margin:0 0 16px;color:#4c3b91;font-size:19px;font-weight:900}
        .timeline{display:grid;gap:12px}.timeline-item{border:1px solid #eeeafc;background:#fbfaff;border-radius:18px;padding:14px}.timeline-item strong{display:block;color:#4c3b91;margin-bottom:6px}.timeline-item small{display:block;color:#6b7280;font-weight:800;line-height:1.7}
        @media(max-width:950px){.details-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.details-grid{grid-template-columns:1fr}}
    </style>

    <div class="card">
        <h3 class="section-title">ملخص الطلب</h3>

        <div class="details-grid">
            <div class="detail-box"><span>الحالة</span><strong><span class="status-pill {{ $workflowClass }}">{{ $workflowName }}</span></strong></div>
            <div class="detail-box"><span>الموظف</span><strong>{{ $salaryAdvanceRequest->employee->display_name ?? $salaryAdvanceRequest->employee->full_name ?? $salaryAdvanceRequest->employee->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>الرقم الوظيفي</span><strong>{{ $salaryAdvanceRequest->employee->employee_number ?? '-' }}</strong></div>
            <div class="detail-box"><span>القسم</span><strong>{{ $salaryAdvanceRequest->employee->department->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>المبلغ المطلوب</span><strong>{{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</strong></div>
            <div class="detail-box"><span>المبلغ المعتمد</span><strong>{{ $salaryAdvanceRequest->approved_amount ? number_format((float) $salaryAdvanceRequest->approved_amount, 2) : '-' }}</strong></div>
            <div class="detail-box"><span>عدد الأقساط</span><strong>{{ $salaryAdvanceRequest->installments_count }}</strong></div>
            <div class="detail-box"><span>قيمة القسط</span><strong>{{ $salaryAdvanceRequest->installment_amount ? number_format((float) $salaryAdvanceRequest->installment_amount, 2) : '-' }}</strong></div>
            <div class="detail-box"><span>بداية الخصم</span><strong>{{ optional($salaryAdvanceRequest->deduction_start_date)->format('Y-m') }}</strong></div>
            <div class="detail-box"><span>تاريخ الطلب</span><strong>{{ optional($salaryAdvanceRequest->created_at)->format('Y-m-d H:i') }}</strong></div>
            <div class="detail-box"><span>رقم السلفة المسجلة</span><strong>{{ $salaryAdvanceRequest->registeredSalaryAdvance->advance_number ?? '-' }}</strong></div>
            <div class="detail-box"><span>آخر تحديث</span><strong>{{ optional($salaryAdvanceRequest->updated_at)->format('Y-m-d H:i') }}</strong></div>
        </div>

        @if($salaryAdvanceRequest->reason)
            <div class="text-box"><strong style="color:#4c3b91;">سبب طلب السلفة:</strong><br>{{ $salaryAdvanceRequest->reason }}</div>
        @endif

        @if($salaryAdvanceRequest->notes)
            <div class="text-box"><strong style="color:#4c3b91;">ملاحظات:</strong><br>{{ $salaryAdvanceRequest->notes }}</div>
        @endif

        @if($salaryAdvanceRequest->attachment)
            <div class="text-box"><strong style="color:#4c3b91;">المرفق:</strong><br><br><a href="{{ asset('storage/' . $salaryAdvanceRequest->attachment) }}" target="_blank" class="btn">عرض المرفق</a></div>
        @endif
    </div>

    <div class="card" style="margin-top:20px;">
        <h3 class="section-title">معلومات الموافقات</h3>
        <div class="details-grid">
            <div class="detail-box"><span>حالة المدير</span><strong>{{ $salaryAdvanceRequest->direct_manager_status ?? '-' }}</strong></div>
            <div class="detail-box"><span>وافق المدير بواسطة</span><strong>{{ $salaryAdvanceRequest->directManagerApprovedBy->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>وقت موافقة المدير</span><strong>{{ optional($salaryAdvanceRequest->direct_manager_approved_at)->format('Y-m-d H:i') ?? '-' }}</strong></div>
            <div class="detail-box"><span>رفض المدير بواسطة</span><strong>{{ $salaryAdvanceRequest->directManagerRejectedBy->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>حالة HR</span><strong>{{ $salaryAdvanceRequest->hr_status ?? '-' }}</strong></div>
            <div class="detail-box"><span>اعتمد HR بواسطة</span><strong>{{ $salaryAdvanceRequest->hrApprovedBy->name ?? '-' }}</strong></div>
            <div class="detail-box"><span>وقت اعتماد HR</span><strong>{{ optional($salaryAdvanceRequest->hr_approved_at)->format('Y-m-d H:i') ?? '-' }}</strong></div>
            <div class="detail-box"><span>رفض HR بواسطة</span><strong>{{ $salaryAdvanceRequest->hrRejectedBy->name ?? '-' }}</strong></div>
        </div>

        @if($salaryAdvanceRequest->direct_manager_reject_reason)
            <div class="text-box" style="background:#fef2f2;color:#991b1b;"><strong>سبب رفض المدير:</strong><br>{{ $salaryAdvanceRequest->direct_manager_reject_reason }}</div>
        @endif

        @if($salaryAdvanceRequest->hr_reject_reason)
            <div class="text-box" style="background:#fef2f2;color:#991b1b;"><strong>سبب رفض HR:</strong><br>{{ $salaryAdvanceRequest->hr_reject_reason }}</div>
        @endif
    </div>

    <div class="card" style="margin-top:20px;">
        <h3 class="section-title">سجل الحركة</h3>
        <div class="timeline">
            @forelse($salaryAdvanceRequest->logs as $log)
                <div class="timeline-item">
                    <strong>{{ $log->transaction_type }}</strong>
                    <small>{{ optional($log->created_at)->format('Y-m-d H:i') }}</small>
                    <small>{{ $log->description }}</small>
                </div>
            @empty
                <div class="text-box">لا توجد حركات مسجلة.</div>
            @endforelse
        </div>
    </div>
@endsection
