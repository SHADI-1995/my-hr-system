<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الإجازات PDF</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f3ff;
            color: #111827;
            direction: rtl;
            margin: 0;
            padding: 14px;
        }

        .print-page {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, .08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            border-bottom: 2px solid #eee7ff;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0;
            color: #4c3b91;
            font-size: 23px;
            font-weight: 900;
        }

        .header p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .print-actions {
            display: flex;
            gap: 8px;
        }

        .print-btn {
            border: none;
            border-radius: 10px;
            background: #6d5bd0;
            color: #fff;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 800;
        }

        .back-btn {
            border: none;
            border-radius: 10px;
            background: #f3f4f6;
            color: #374151;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 800;
            text-decoration: none;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .summary-card {
            background: #f8f7ff;
            border: 1px solid #eee7ff;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
        }

        .summary-card div:first-child {
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
        }

        .summary-card div:last-child {
            color: #111827;
            font-size: 17px;
            font-weight: 900;
            margin-top: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th {
            background: #4c3b91;
            color: #fff;
            padding: 7px 5px;
            border: 1px solid #4c3b91;
            line-height: 1.5;
            white-space: normal;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #e5e7eb;
            text-align: center;
            vertical-align: middle;
            line-height: 1.6;
            white-space: normal;
            word-break: break-word;
        }

        tr:nth-child(even) td {
            background: #fafafa;
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

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .print-page {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="print-page">

    <div class="header">
        <div>
            <h1>تقرير الإجازات</h1>
            <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
            <p>يعرض التقرير مسار طلب الإجازة: المدير المباشر ثم الموارد البشرية وحالة الخصم من الرصيد.</p>
        </div>

        <div class="print-actions">
            <button type="button" class="print-btn" onclick="window.print()">طباعة / حفظ PDF</button>
            <a href="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="back-btn">رجوع</a>
        </div>
    </div>

    <div class="summary">
        <div class="summary-card">
            <div>إجمالي الطلبات</div>
            <div>{{ $summary['total_requests'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div>بانتظار المدير</div>
            <div>{{ $summary['pending_manager_requests'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div>بانتظار HR</div>
            <div>{{ $summary['pending_hr_requests'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div>معتمدة نهائيًا</div>
            <div>{{ $summary['approved_by_hr_requests'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div>الأيام المعتمدة</div>
            <div>{{ number_format($summary['approved_days'] ?? 0, 2) }}</div>
        </div>

        <div class="summary-card">
            <div>مرفوضة / ملغاة</div>
            <div>
                {{ ($summary['rejected_by_manager_requests'] ?? 0) + ($summary['rejected_by_hr_requests'] ?? 0) + ($summary['cancelled_requests'] ?? 0) }}
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>الموظف</th>
            <th>الرقم الوظيفي</th>
            <th>القسم</th>
            <th>نوع الإجازة</th>
            <th>من تاريخ</th>
            <th>إلى تاريخ</th>
            <th>الأيام</th>
            <th>حالة المسار</th>
            <th>المدير المباشر</th>
            <th>قرار المدير</th>
            <th>وقت قرار المدير</th>
            <th>قرار HR</th>
            <th>وقت قرار HR</th>
            <th>حالة الخصم</th>
            <th>سبب الطلب</th>
            <th>سبب الرفض / الإلغاء</th>
        </tr>
        </thead>

        <tbody>
        @forelse($leaveRequests as $leaveRequest)
            @php
                $workflowStatusName = match($leaveRequest->workflow_status) {
                    'pending_manager' => 'بانتظار المدير المباشر',
                    'manager_approved_pending_hr' => 'موافق من المدير - بانتظار HR',
                    'approved_by_hr' => 'موافق نهائيًا من HR',
                    'rejected_by_manager' => 'مرفوض من المدير',
                    'rejected_by_hr' => 'مرفوض من HR',
                    'cancelled' => 'ملغي',
                    default => 'قيد المراجعة',
                };

                $workflowStatusClass = match($leaveRequest->workflow_status) {
                    'approved_by_hr' => 'status-approved',
                    'rejected_by_manager', 'rejected_by_hr' => 'status-rejected',
                    'cancelled' => 'status-cancelled',
                    default => 'status-pending',
                };

                $managerName = $leaveRequest->employee->directManagerUser->name ?? '-';

                $managerDecision = match($leaveRequest->direct_manager_status) {
                    'approved' => 'موافق',
                    'rejected' => 'مرفوض',
                    default => 'قيد المراجعة',
                };

                $managerDecisionAt = optional(
                    $leaveRequest->direct_manager_approved_at ?? $leaveRequest->direct_manager_rejected_at
                )->format('Y-m-d H:i') ?? '-';

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

                $hrDecisionAt = optional(
                    $leaveRequest->hr_approved_at ?? $leaveRequest->hr_rejected_at
                )->format('Y-m-d H:i') ?? '-';

                $deductionStatus = match(true) {
                    $leaveRequest->workflow_status === 'approved_by_hr' => 'تم الخصم',
                    $leaveRequest->workflow_status === 'cancelled' && $leaveRequest->hr_status === 'cancelled_after_approval' => 'تم إرجاع الرصيد',
                    default => 'لم يتم الخصم',
                };

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
                <td>{{ $managerDecisionAt }}</td>
                <td>{{ $hrDecision }}</td>
                <td>{{ $hrDecisionAt }}</td>
                <td>{{ $deductionStatus }}</td>
                <td>{{ \Illuminate\Support\Str::limit($leaveRequest->reason ?? '-', 45) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($rejectReason, 45) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="17">لا توجد بيانات.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 600);
    });
</script>

</body>
</html>
