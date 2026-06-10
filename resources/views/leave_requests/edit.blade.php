@extends('layouts.hr')

@section('title', 'تعديل إجازة')
@section('page-title', 'تعديل طلب إجازة')

@section('content')

    <style>
        .leave-form-section {
            margin-bottom: 22px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 7px;
            color: #4c3b91;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            outline: none;
            background: #fff;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .readonly-input {
            background: #f3f4f6 !important;
            color: #374151;
            font-weight: bold;
        }

        .hint-box {
            margin-top: 10px;
            background: #f8f7ff;
            border: 1px solid #e9e5ff;
            color: #5b45a0;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.8;
        }

        .required {
            color: #dc2626;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: bold;
            width: fit-content;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        @media (max-width: 750px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-pen-to-square"></i>
            </div>

            <div>
                <h1>تعديل طلب إجازة</h1>
                <p>يمكن تعديل الطلب قبل الاعتماد أو الرفض فقط</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('leave-requests.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card">

        @if ($errors->any())
            <div style="background:#fef2f2; color:#991b1b; padding:15px; border-radius:12px; margin-bottom:20px;">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($leaveRequest->status !== 'pending')
            <div style="background:#fff7ed; color:#9a3412; padding:15px; border-radius:12px; margin-bottom:20px;">
                لا يمكن تعديل طلب الإجازة بعد الاعتماد أو الرفض.
            </div>
        @endif

        <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="leave-form-section">
                <div class="form-grid">

                    <div class="form-group">
                        <label>الموظف <span class="required">*</span></label>

                        @if(auth()->user()->hasPermission('leave_requests.edit.employee_id') && $leaveRequest->status === 'pending')
                            <select name="employee_id" required>
                                @foreach($employees as $employee)
                                    <option
                                        value="{{ $employee->id }}"
                                        data-remaining="{{ optional($employee->currentLeaveBalance)->remaining_days ?? 0 }}"
                                        data-used="{{ optional($employee->currentLeaveBalance)->used_paid_days ?? 0 }}"
                                        data-entitled="{{ optional($employee->currentLeaveBalance)->annual_entitled_days ?? 0 }}"
                                        {{ old('employee_id', $leaveRequest->employee_id) == $employee->id ? 'selected' : '' }}
                                    >
                                        {{ $employee->display_name ?? $employee->full_name ?? $employee->name }}
                                        —
                                        {{ $employee->employee_number ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input
                                type="text"
                                value="{{ $leaveRequest->employee->display_name ?? $leaveRequest->employee->full_name ?? $leaveRequest->employee->name ?? '-' }}"
                                disabled
                                class="readonly-input">

                            <input type="hidden" name="employee_id" value="{{ $leaveRequest->employee_id }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>رصيد الموظف</label>
                        <input type="text" id="employee_balance_preview" class="readonly-input" value="اختر الموظف لعرض الرصيد" readonly>
                        <div class="hint-box" id="balanceWarning" style="display:none; color:#dc2626; font-weight:bold;"></div>
                    </div>

                    <div class="form-group">
                        <label>نوع الإجازة <span class="required">*</span></label>

                        @if(auth()->user()->hasPermission('leave_requests.edit.leave_type') && $leaveRequest->status === 'pending')
                            <select name="leave_type_id" id="leave_type_id" required>
                                <option value="">اختر نوع الإجازة</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option
                                        value="{{ $leaveType->id }}"
                                        data-paid="{{ $leaveType->is_paid ? '1' : '0' }}"
                                        data-deduct="{{ $leaveType->deduct_from_annual_balance ? '1' : '0' }}"
                                        data-attachment="{{ $leaveType->requires_attachment ? '1' : '0' }}"
                                        data-approval="{{ $leaveType->requires_approval ? '1' : '0' }}"
                                        data-auto="{{ $leaveType->auto_approved ? '1' : '0' }}"
                                        data-max="{{ $leaveType->max_days_per_year }}"
                                        {{ old('leave_type_id', $leaveRequest->leave_type_id) == $leaveType->id ? 'selected' : '' }}
                                    >
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="leaveTypeInfo" class="hint-box" style="display:none;"></div>
                        @else
                            <input
                                type="text"
                                value="{{ $leaveRequest->leaveType->name ?? $leaveRequest->leave_type ?? '-' }}"
                                disabled
                                class="readonly-input">

                            <input type="hidden" name="leave_type_id" value="{{ $leaveRequest->leave_type_id }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>من تاريخ <span class="required">*</span></label>

                        @if(auth()->user()->hasPermission('leave_requests.edit.start_date') && $leaveRequest->status === 'pending')
                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                value="{{ old('start_date', optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date) }}"
                                required>
                        @else
                            <input
                                type="text"
                                value="{{ optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date }}"
                                disabled
                                class="readonly-input">

                            <input type="hidden" name="start_date" value="{{ optional($leaveRequest->start_date)->format('Y-m-d') ?? $leaveRequest->start_date }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>إلى تاريخ <span class="required">*</span></label>

                        @if(auth()->user()->hasPermission('leave_requests.edit.end_date') && $leaveRequest->status === 'pending')
                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                value="{{ old('end_date', optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date) }}"
                                required>
                        @else
                            <input
                                type="text"
                                value="{{ optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date }}"
                                disabled
                                class="readonly-input">

                            <input type="hidden" name="end_date" value="{{ optional($leaveRequest->end_date)->format('Y-m-d') ?? $leaveRequest->end_date }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>عدد الأيام</label>
                        <input
                            type="number"
                            id="days_count_preview"
                            class="readonly-input"
                            value="{{ old('days_count', $leaveRequest->days_count) }}"
                            readonly>

                        <div class="hint-box">
                            عدد الأيام يتم حسابه تلقائيًا في السيرفر حسب تاريخ البداية والنهاية وسياسة الإجازات.
                        </div>
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>

                        @php
                            $statusClass = match($leaveRequest->status) {
                                'approved' => 'status-approved',
                                'rejected' => 'status-rejected',
                                default => 'status-pending',
                            };

                            $statusName = match($leaveRequest->status) {
                                'approved' => 'مقبولة',
                                'rejected' => 'مرفوضة',
                                default => 'قيد المراجعة',
                            };
                        @endphp

                        <span class="status-pill {{ $statusClass }}">
                            {{ $statusName }}
                        </span>
                    </div>

                    <div class="form-group full">
                        <label>السبب</label>

                        @if(auth()->user()->hasPermission('leave_requests.edit.reason') && $leaveRequest->status === 'pending')
                            <textarea name="reason" rows="4" placeholder="سبب طلب الإجازة">{{ old('reason', $leaveRequest->reason) }}</textarea>
                        @else
                            <textarea rows="4" disabled class="readonly-input">{{ $leaveRequest->reason }}</textarea>
                            <input type="hidden" name="reason" value="{{ $leaveRequest->reason }}">
                        @endif
                    </div>

                    <div class="form-group full">
                        <label>المرفق</label>

                        @if($leaveRequest->attachment)
                            <div class="hint-box">
                                المرفق الحالي:
                                <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank">
                                    عرض / تحميل المرفق
                                </a>
                            </div>
                        @endif

                        @if($leaveRequest->status === 'pending')
                            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="hint-box">
                                الملفات المسموحة: PDF, JPG, PNG, WEBP — الحد الأقصى 5MB.
                                <span id="attachmentRequiredText" style="display:none; color:#dc2626; font-weight:bold;">
                                    هذا النوع من الإجازات يتطلب مرفقًا.
                                </span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="form-actions">
                @if($leaveRequest->status === 'pending')
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i>
                        تحديث
                    </button>
                @endif

                <a href="{{ route('leave-requests.index') }}" class="btn">
                    رجوع
                </a>
            </div>
        </form>

    </div>

    <script>
        function calculatePreviewDays() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const daysInput = document.getElementById('days_count_preview');

            if (!startInput || !endInput || !daysInput) {
                updateEmployeeBalancePreview();
                return;
            }

            if (!startInput.value || !endInput.value) {
                daysInput.value = 1;
                updateEmployeeBalancePreview();
                return;
            }

            const start = new Date(startInput.value);
            const end = new Date(endInput.value);

            if (end < start) {
                daysInput.value = 0;
                updateEmployeeBalancePreview();
                return;
            }

            const diffTime = end.getTime() - start.getTime();
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            daysInput.value = diffDays;
            updateEmployeeBalancePreview();
        }

        function updateEmployeeBalancePreview() {
            const employeeSelect = document.querySelector('select[name="employee_id"]');
            const balanceInput = document.getElementById('employee_balance_preview');
            const warning = document.getElementById('balanceWarning');
            const daysInput = document.getElementById('days_count_preview');
            const leaveTypeSelect = document.getElementById('leave_type_id');

            if (!employeeSelect || !balanceInput || !warning) {
                return;
            }

            const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex];

            if (!selectedEmployee || !selectedEmployee.value) {
                balanceInput.value = 'اختر الموظف لعرض الرصيد';
                warning.style.display = 'none';
                warning.innerText = '';
                return;
            }

            const remaining = parseFloat(selectedEmployee.getAttribute('data-remaining') || '0');
            const used = parseFloat(selectedEmployee.getAttribute('data-used') || '0');
            const entitled = parseFloat(selectedEmployee.getAttribute('data-entitled') || '0');
            const requestedDays = parseFloat(daysInput?.value || '0');

            balanceInput.value = `المتاح: ${remaining.toFixed(2)} يوم — المستخدم: ${used.toFixed(2)} من ${entitled.toFixed(2)} يوم`;

            const selectedLeaveType = leaveTypeSelect?.options[leaveTypeSelect.selectedIndex];
            const deductFromAnnual = selectedLeaveType?.getAttribute('data-deduct') === '1';

            if (deductFromAnnual && requestedDays > remaining) {
                warning.style.display = 'block';
                warning.innerText = 'تنبيه: عدد أيام الطلب أكبر من الرصيد المتاح، ولن يتم اعتماد الطلب إلا بعد توفر الرصيد.';
            } else {
                warning.style.display = 'none';
                warning.innerText = '';
            }
        }

        function updateLeaveTypeInfo() {
            const select = document.getElementById('leave_type_id');
            const info = document.getElementById('leaveTypeInfo');

            if (!select || !info) {
                return;
            }

            const selected = select.options[select.selectedIndex];

            if (!selected || !selected.value) {
                info.style.display = 'none';
                info.innerHTML = '';
                return;
            }

            const isPaid = selected.getAttribute('data-paid') === '1';
            const deduct = selected.getAttribute('data-deduct') === '1';
            const attachment = selected.getAttribute('data-attachment') === '1';
            const approval = selected.getAttribute('data-approval') === '1';
            const autoApproved = selected.getAttribute('data-auto') === '1';
            const maxDays = selected.getAttribute('data-max');
            const attachmentText = document.getElementById('attachmentRequiredText');

            if (attachmentText) {
                attachmentText.style.display = attachment ? 'inline' : 'none';
            }

            info.style.display = 'block';
            info.innerHTML = `
                <strong>خصائص نوع الإجازة:</strong><br>
                ${isPaid ? 'مدفوعة' : 'غير مدفوعة'} —
                ${deduct ? 'تخصم من الرصيد السنوي' : 'لا تخصم من الرصيد السنوي'} —
                ${attachment ? 'تحتاج مرفق' : 'لا تحتاج مرفق'} —
                ${approval ? 'تحتاج موافقة' : 'لا تحتاج موافقة'} —
                ${autoApproved ? 'تعتمد تلقائيًا' : 'لا تعتمد تلقائيًا'}
                ${maxDays ? `<br>الحد الأقصى سنويًا: ${maxDays} يوم` : ''}
            `;
        }

        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        const leaveTypeSelect = document.getElementById('leave_type_id');

        if (startInput) startInput.addEventListener('change', calculatePreviewDays);
        if (endInput) endInput.addEventListener('change', calculatePreviewDays);
        if (leaveTypeSelect) leaveTypeSelect.addEventListener('change', function () {
            updateLeaveTypeInfo();
            updateEmployeeBalancePreview();
            updateEmployeeBalancePreview();
        });

        const employeeSelect = document.querySelector('select[name="employee_id"]');
        if (employeeSelect) employeeSelect.addEventListener('change', updateEmployeeBalancePreview);

        calculatePreviewDays();
        updateLeaveTypeInfo();
        updateEmployeeBalancePreview();
    </script>

@endsection
