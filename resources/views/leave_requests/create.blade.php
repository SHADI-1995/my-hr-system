@extends('layouts.hr')

@section('title', 'إضافة إجازة')
@section('page-title', 'إضافة طلب إجازة')

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

        .readonly-input {
            background: #f3f4f6 !important;
            color: #374151;
            font-weight: bold;
        }

        .required {
            color: #dc2626;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        @media (max-width: 750px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>

            <div>
                <h1>إضافة طلب إجازة</h1>
                <p>إنشاء طلب إجازة وربطه بنوع الإجازة ورصيد الموظف</p>
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

        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="leave-form-section">
                <div class="form-grid">

                    <div class="form-group">
                        <label>الموظف <span class="required">*</span></label>
                        <select name="employee_id" required>
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    data-remaining="{{ optional($employee->currentLeaveBalance)->remaining_days ?? 0 }}"
                                    data-used="{{ optional($employee->currentLeaveBalance)->used_paid_days ?? 0 }}"
                                    data-entitled="{{ optional($employee->currentLeaveBalance)->annual_entitled_days ?? 0 }}"
                                    {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                >
                                    {{ $employee->display_name ?? $employee->full_name ?? $employee->name }}
                                    —
                                    {{ $employee->employee_number ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>رصيد الموظف</label>
                        <input type="text" id="employee_balance_preview" class="readonly-input" value="اختر الموظف لعرض الرصيد" readonly>
                        <div class="hint-box" id="balanceWarning" style="display:none; color:#dc2626; font-weight:bold;"></div>
                    </div>

                    <div class="form-group">
                        <label>نوع الإجازة <span class="required">*</span></label>
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
                                    {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}
                                >
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>

                        <div id="leaveTypeInfo" class="hint-box" style="display:none;"></div>
                    </div>

                    <div class="form-group">
                        <label>من تاريخ <span class="required">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>إلى تاريخ <span class="required">*</span></label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>عدد الأيام التقريبي</label>
                        <input type="number" id="days_count_preview" class="readonly-input" value="1" readonly>
                        <div class="hint-box">
                            العدد النهائي يتم حسابه في السيرفر حسب سياسة الإجازات، مع استبعاد عطلة نهاية الأسبوع إذا كانت مفعلة.
                        </div>
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>
                        <input type="text" class="readonly-input" value="قيد المراجعة / أو اعتماد تلقائي حسب نوع الإجازة" readonly>
                    </div>

                    <div class="form-group full">
                        <label>السبب</label>
                        <textarea name="reason" rows="4" placeholder="سبب طلب الإجازة">{{ old('reason') }}</textarea>
                    </div>

                    <div class="form-group full">
                        <label>المرفق</label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <div class="hint-box">
                            الملفات المسموحة: PDF, JPG, PNG, WEBP — الحد الأقصى 5MB.
                            <span id="attachmentRequiredText" style="display:none; color:#dc2626; font-weight:bold;">
                                هذا النوع من الإجازات يتطلب مرفقًا.
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    حفظ الطلب
                </button>

                <a href="{{ route('leave-requests.index') }}" class="btn btn-danger">
                    إلغاء
                </a>
            </div>

        </form>

    </div>

    <script>
        function calculatePreviewDays() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const daysInput = document.getElementById('days_count_preview');

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
            const selected = select.options[select.selectedIndex];
            const info = document.getElementById('leaveTypeInfo');

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

        document.getElementById('start_date').addEventListener('change', calculatePreviewDays);
        document.getElementById('end_date').addEventListener('change', calculatePreviewDays);
        document.getElementById('leave_type_id').addEventListener('change', function () {
            updateLeaveTypeInfo();
            updateEmployeeBalancePreview();
            updateEmployeeBalancePreview();
        });
        document.querySelector('select[name="employee_id"]').addEventListener('change', updateEmployeeBalancePreview);

        calculatePreviewDays();
        updateLeaveTypeInfo();
        updateEmployeeBalancePreview();
    </script>

@endsection
