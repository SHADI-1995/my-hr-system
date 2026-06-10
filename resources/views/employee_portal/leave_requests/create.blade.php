@extends('layouts.employee_portal')

@section('title', 'طلب إجازة جديد')

@section('content')

    <style>
        .leave-create-page,
        .leave-create-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .leave-create-hero {
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

        .leave-create-hero h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 900;
        }

        .leave-create-hero p {
            margin: 0;
            opacity: .92;
            font-weight: 700;
            line-height: 1.8;
        }

        .leave-create-hero-icon {
            width: 68px;
            height: 68px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
            font-weight: 900;
        }

        .leave-create-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
        }

        .manager-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            padding: 15px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-weight: 800;
            line-height: 1.8;
        }

        .form-section-title {
            margin: 0 0 16px;
            color: #4c3b91;
            font-size: 18px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title span {
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

        .approval-path-box {
            background: #f8f7ff;
            border: 1px solid #e7e0ff;
            border-radius: 18px;
            padding: 15px;
        }

        .approval-path-box strong {
            display: block;
            color: #4c3b91;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 900;
        }

        .approval-step {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 14px;
            padding: 11px 13px;
            margin-top: 8px;
            box-shadow: 0 8px 18px rgba(76, 59, 145, .04);
        }

        .approval-step-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 900;
        }

        .approval-step-name {
            color: #111827;
            font-size: 15px;
            font-weight: 900;
            text-align: left;
        }

        .submit-disabled-note {
            margin-top: 12px;
            color: #991b1b;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.7;
        }

        .portal-btn[disabled] {
            opacity: .55;
            cursor: not-allowed;
            box-shadow: none;
        }

        .required-star {
            color: #dc2626;
            font-weight: 900;
        }

        .attachment-upload-box {
            border: 1px dashed #c4b5fd;
            background: #fbfaff;
            border-radius: 18px;
            padding: 15px;
        }

        .attachment-upload-box input[type="file"] {
            width: 100%;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            padding: 11px;
            background: #fff;
            font-weight: 800;
            color: #111827;
        }

        .attachment-upload-box input[type="file"]:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109,91,208,.12);
            outline: none;
        }

        .attachment-help {
            display: block;
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
            margin-top: 9px;
            line-height: 1.8;
        }

        .attachment-required-note {
            margin-top: 10px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 13px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.8;
        }

        .attachment-preview {
            display: none;
            margin-top: 12px;
            padding: 12px;
            border-radius: 16px;
            background: #f8f7ff;
            border: 1px solid #e7e0ff;
        }

        .attachment-preview strong {
            display: block;
            color: #4c3b91;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 900;
        }

        .attachment-preview img {
            max-width: 100%;
            max-height: 240px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            object-fit: cover;
            display: block;
        }

        .pdf-preview {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #4338ca;
            padding: 10px 12px;
            border-radius: 13px;
            font-weight: 900;
            max-width: 100%;
            word-break: break-word;
        }

        .leave-create-submit-row {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-weight: 800;
            line-height: 1.7;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 700px) {
            .leave-create-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px;
            }

            .leave-create-actions {
                justify-content: stretch;
            }

            .leave-create-actions .portal-btn,
            .leave-create-submit-row .portal-btn {
                width: 100%;
                justify-content: center;
            }

            .approval-step {
                align-items: flex-start;
                flex-direction: column;
            }

            .approval-step-name {
                text-align: right;
            }

            .attachment-upload-box {
                padding: 12px;
            }

            .attachment-preview img {
                max-height: 190px;
            }
        }
    </style>

    <div class="leave-create-page">

        <div class="leave-create-hero">
            <div>
                <h2>طلب إجازة جديد</h2>
                <p>{{ $employee->display_name }} — سيتم إرسال الطلب للمدير المباشر ثم الموارد البشرية</p>
            </div>

            <div class="leave-create-hero-icon">
                HR
            </div>
        </div>

        <div class="leave-create-actions">
            <a href="{{ route('employee-portal.leave-requests.index') }}" class="portal-btn secondary">طلباتي</a>
        </div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin:8px 0 0; padding-right:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$employee->direct_manager_user_id)
            <div class="manager-warning">
                لا يمكن إرسال طلب إجازة حالياً، لأن المدير المباشر غير محدد في ملفك الوظيفي.
                يرجى التواصل مع الموارد البشرية لتحديد المدير المباشر أولاً.
            </div>
        @endif

        <div class="portal-card">
            <h3 class="form-section-title">
                <span>✓</span>
                بيانات طلب الإجازة
            </h3>

            <form method="POST" action="{{ route('employee-portal.leave-requests.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label>نوع الإجازة</label>
                        <select name="leave_type_id" {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}>
                            <option value="">اختر نوع الإجازة</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>مسار الموافقة</label>

                        <div class="approval-path-box">
                            <strong>سيتم إرسال الطلب إلى:</strong>

                            <div class="approval-step">
                                <div class="approval-step-label">المرحلة الأولى: المدير المباشر</div>
                                <div class="approval-step-name">
                                    {{ $employee->directManagerUser->name ?? 'لم يتم تحديد مدير مباشر' }}
                                </div>
                            </div>

                            <div class="approval-step">
                                <div class="approval-step-label">المرحلة الثانية: الموارد البشرية</div>
                                <div class="approval-step-name">إدارة الموارد البشرية</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>من تاريخ</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}>
                        @error('start_date')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}>
                        @error('end_date')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label>
                            المرفق
                            <span class="required-star">*</span>
                        </label>

                        <div class="attachment-upload-box">
                            <input
                                type="file"
                                name="attachment"
                                accept="image/jpeg,image/jpg,image/png,image/webp,application/pdf,.jpg,.jpeg,.png,.webp,.pdf"
                                required
                                onchange="previewLeaveAttachment(this)"
                                {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}
                            >

                            <span class="attachment-help">
                                يجب إرفاق صورة أو ملف PDF لإرسال طلب الإجازة. الصيغ المسموحة: JPG أو PNG أو WEBP أو PDF، والحجم لا يتجاوز 4 ميجا.
                            </span>

                            <div class="attachment-required-note">
                                تنبيه: لن يتم إرسال طلب الإجازة بدون رفع مرفق.
                            </div>

                            <div id="leaveAttachmentPreview" class="attachment-preview">
                                <strong>معاينة المرفق المختار:</strong>
                                <div id="leaveAttachmentPreviewContent"></div>
                            </div>
                        </div>

                        @error('attachment')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label>سبب الطلب</label>
                        <textarea name="reason" placeholder="اكتب سبب طلب الإجازة..." {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}>{{ old('reason') }}</textarea>
                        @error('reason')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="leave-create-submit-row">
                    <button type="submit" class="portal-btn" {{ !$employee->direct_manager_user_id ? 'disabled' : '' }}>
                        إرسال الطلب
                    </button>

                    <a href="{{ route('employee-portal.leave-requests.index') }}" class="portal-btn secondary">إلغاء</a>
                </div>

                @if(!$employee->direct_manager_user_id)
                    <div class="submit-disabled-note">
                        زر الإرسال معطل حتى يتم تحديد المدير المباشر للموظف.
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function previewLeaveAttachment(input) {
            const previewBox = document.getElementById('leaveAttachmentPreview');
            const previewContent = document.getElementById('leaveAttachmentPreviewContent');

            previewContent.innerHTML = '';

            if (!input.files || !input.files[0]) {
                previewBox.style.display = 'none';
                return;
            }

            const file = input.files[0];

            previewBox.style.display = 'block';

            if (file.type && file.type.startsWith('image/')) {
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = 'معاينة صورة المرفق';
                previewContent.appendChild(image);
                return;
            }

            if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                previewContent.innerHTML =
                    '<div class="pdf-preview">' +
                    '<span>📄</span>' +
                    '<span>' + file.name + '</span>' +
                    '</div>';
                return;
            }

            previewBox.style.display = 'none';
        }
    </script>
@endsection
