<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #4c3b91;
            color: #ffffff;
            font-weight: bold;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
            mso-number-format: "\@";
            vertical-align: middle;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #4c3b91;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 13px;
            color: #555;
            margin-bottom: 12px;
        }

        .status-pending {
            background: #fff7cc;
            color: #92400e;
            font-weight: bold;
        }

        .status-approved {
            background: #d9fbe6;
            color: #166534;
            font-weight: bold;
        }

        .status-rejected {
            background: #ffe1e1;
            color: #991b1b;
            font-weight: bold;
        }

        .status-cancelled {
            background: #eeeeee;
            color: #374151;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="title">تقرير الإجازات</div>
<div class="subtitle">
    يعرض هذا التقرير مسار طلب الإجازة حسب موافقة المدير المباشر وموافقة الموارد البشرية وحالة الخصم من الرصيد.
</div>

<table>
    <thead>
    <tr>
        <th>رقم الطلب</th>
        <th>الموظف</th>
        <th>الرقم الوظيفي</th>
        <th>القسم</th>
        <th>نوع الإجازة</th>
        <th>من تاريخ</th>
        <th>إلى تاريخ</th>
        <th>عدد الأيام</th>

        <th>حالة مسار الموافقة</th>

        <th>المدير المباشر</th>
        <th>قرار المدير</th>
        <th>تم قرار المدير بواسطة</th>
        <th>وقت قرار المدير</th>
        <th>سبب رفض المدير</th>

        <th>قرار الموارد البشرية</th>
        <th>تم قرار HR بواسطة</th>
        <th>وقت قرار HR</th>
        <th>سبب رفض HR</th>

        <th>الحالة النهائية</th>
        <th>حالة الخصم من الرصيد</th>

        <th>سبب الطلب</th>
        <th>سبب الرفض / الإلغاء</th>
        <th>تم القبول النهائي بواسطة</th>
        <th>وقت القبول النهائي</th>
        <th>تم الرفض / الإلغاء بواسطة</th>
        <th>وقت الرفض / الإلغاء</th>
    </tr>
    </thead>

    <tbody>
    @foreach($leaveRequests as $leaveRequest)
        @php
            $workflowStatusName = match($leaveRequest->workflow_status) {
                'pending_manager' => 'بانتظار موافقة المدير المباشر',
                'manager_approved_pending_hr' => 'موافق من المدير - بانتظار الموارد البشرية',
                'approved_by_hr' => 'موافق نهائيًا من الموارد البشرية',
                'rejected_by_manager' => 'مرفوض من المدير المباشر',
                'rejected_by_hr' => 'مرفوض من الموارد البشرية',
                'cancelled' => 'ملغي',
                default => 'قيد المراجعة',
            };

            $workflowStatusClass = match($leaveRequest->workflow_status) {
                'approved_by_hr' => 'status-approved',
                'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                'cancelled' => 'status-cancelled',
                default => 'status-pending',
            };

            $generalStatusName = match($leaveRequest->status) {
                'approved' => 'مقبولة',
                'rejected' => 'مرفوضة',
                'cancelled' => 'ملغاة',
                default => 'قيد المراجعة',
            };

            $managerDecision = match($leaveRequest->direct_manager_status) {
                'approved' => 'موافق',
                'rejected' => 'مرفوض',
                default => 'قيد المراجعة',
            };

            $hrDecision = match($leaveRequest->hr_status) {
                'approved' => 'موافق',
                'rejected' => 'مرفوض',
                'pending' => 'قيد المعالجة',
                'waiting_manager' => 'بانتظار المدير',
                'not_required' => 'غير مطلوب',
                'cancelled' => 'ملغي',
                'cancelled_after_approval' => 'ملغي بعد الاعتماد',
                default => '-',
            };

            $deductionStatus = match(true) {
                $leaveRequest->workflow_status === 'approved_by_hr' => 'تم الخصم',
                $leaveRequest->workflow_status === 'cancelled' && $leaveRequest->hr_status === 'cancelled_after_approval' => 'تم إرجاع الرصيد',
                default => 'لم يتم الخصم',
            };

            $managerName = $leaveRequest->employee->directManagerUser->name ?? '-';

            $managerDecisionBy = $leaveRequest->directManagerApprovedBy->name
                ?? $leaveRequest->directManagerRejectedBy->name
                ?? '-';

            $managerDecisionAt = optional(
                $leaveRequest->direct_manager_approved_at ?? $leaveRequest->direct_manager_rejected_at
            )->format('Y-m-d H:i') ?? '-';

            $hrDecisionBy = $leaveRequest->hrApprovedBy->name
                ?? $leaveRequest->hrRejectedBy->name
                ?? '-';

            $hrDecisionAt = optional(
                $leaveRequest->hr_approved_at ?? $leaveRequest->hr_rejected_at
            )->format('Y-m-d H:i') ?? '-';

            $rejectReason = $leaveRequest->reject_reason
                ?? $leaveRequest->direct_manager_reject_reason
                ?? $leaveRequest->hr_reject_reason
                ?? '-';
        @endphp

        <tr>
            <td>{{ $leaveRequest->id }}</td>
            <td>
                {{ $leaveRequest->employee->display_name
                    ?? $leaveRequest->employee->full_name
                    ?? $leaveRequest->employee->name
                    ?? '-' }}
            </td>
            <td>{{ $leaveRequest->employee->employee_number ?? '-' }}</td>
            <td>{{ $leaveRequest->employee->department->name ?? '-' }}</td>
            <td>{{ $leaveRequest->leaveType->name ?? '-' }}</td>
            <td>{{ optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date }}</td>
            <td>{{ optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date }}</td>
            <td>{{ number_format((float) $leaveRequest->days_count, 2) }}</td>

            <td class="{{ $workflowStatusClass }}">{{ $workflowStatusName }}</td>

            <td>{{ $managerName }}</td>
            <td>{{ $managerDecision }}</td>
            <td>{{ $managerDecisionBy }}</td>
            <td>{{ $managerDecisionAt }}</td>
            <td>{{ $leaveRequest->direct_manager_reject_reason ?? '-' }}</td>

            <td>{{ $hrDecision }}</td>
            <td>{{ $hrDecisionBy }}</td>
            <td>{{ $hrDecisionAt }}</td>
            <td>{{ $leaveRequest->hr_reject_reason ?? '-' }}</td>

            <td>{{ $generalStatusName }}</td>
            <td>{{ $deductionStatus }}</td>

            <td>{{ $leaveRequest->reason ?? '-' }}</td>
            <td>{{ $rejectReason }}</td>
            <td>{{ $leaveRequest->approvedBy->name ?? '-' }}</td>
            <td>{{ optional($leaveRequest->approved_at)->format('Y-m-d H:i') ?? '-' }}</td>
            <td>{{ $leaveRequest->rejectedBy->name ?? '-' }}</td>
            <td>{{ optional($leaveRequest->rejected_at)->format('Y-m-d H:i') ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
