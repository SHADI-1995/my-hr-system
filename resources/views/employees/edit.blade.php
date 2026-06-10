@extends('layouts.hr')

@section('title', 'تعديل موظف')
@section('page-title', 'تعديل الموظف')

@section('content')

    <style>
        .employee-form-section {
            margin-bottom: 25px;
            padding: 22px;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 12px 30px rgba(76, 59, 145, 0.05);
        }

        .employee-form-section:last-child {
            border-bottom: 1px solid #eeeafc;
        }

        .section-title {
            font-size: 19px;
            font-weight: 900;
            color: #4c3b91;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.2px;
        }

        .section-title i {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #f1edff;
            color: #6d5bd0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .form-grid.two {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 900;
            margin-bottom: 8px;
            color: #374151;
            font-size: 13px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            outline: none;
            font-size: 14px;
            font-weight: 700;
            background: #fff;
            color: #111827;
            transition: 0.18s ease;
        }

        .form-group input[disabled],
        .form-group select[disabled],
        .form-group textarea[disabled] {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }

        .form-group textarea {
            min-height: 90px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #6d5dfc;
            box-shadow: 0 0 0 4px rgba(109, 93, 252, 0.12);
        }

        .direct-manager-card {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            background: linear-gradient(135deg, #f8f7ff, #ffffff);
            border: 1px solid #e7e0ff;
            border-radius: 18px;
            padding: 16px;
        }

        .direct-manager-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: #6d5bd0;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 12px 24px rgba(109, 91, 208, .22);
        }

        .direct-manager-card label {
            color: #4c3b91;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .direct-manager-help {
            margin-top: 8px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.8;
            font-weight: 700;
        }

        .email-field-card {
            position: relative;
        }

        .email-field-card input {
            padding-inline-start: 42px;
        }

        .email-field-card .email-icon {
            position: absolute;
            top: 39px;
            right: 13px;
            width: 24px;
            height: 24px;
            border-radius: 9px;
            background: #eef2ff;
            color: #4c3b91;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            pointer-events: none;
        }

        .email-help-box {
            margin-top: 9px;
            padding: 10px 12px;
            border-radius: 13px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 12px;
            line-height: 1.8;
            font-weight: 800;
        }

        .email-verified-box {
            margin-top: 9px;
            padding: 9px 11px;
            border-radius: 13px;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 12px;
            line-height: 1.8;
            font-weight: 900;
        }

        .email-unverified-box {
            margin-top: 9px;
            padding: 9px 11px;
            border-radius: 13px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 12px;
            line-height: 1.8;
            font-weight: 900;
        }

        .field-error {
            color: #dc2626;
            font-size: 12px;
            font-weight: 800;
            margin-top: 7px;
        }

        @media (max-width: 650px) {
            .direct-manager-card {
                grid-template-columns: 1fr;
            }

            .direct-manager-icon {
                width: 48px;
                height: 48px;
                border-radius: 15px;
            }
        }

        .optional-note {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 15px;
            background: #f9fafb;
            padding: 10px 12px;
            border-radius: 10px;
        }

        .file-upload-box {
            border: 1px dashed #c7c3ff;
            background: #fbfaff;
            border-radius: 12px;
            padding: 14px;
        }

        .file-upload-box input[type="file"] {
            border: none;
            padding: 0;
            background: transparent;
        }

        .file-help {
            color: #6b7280;
            font-size: 12px;
            margin-top: 7px;
        }

        .selected-preview-box {
            display: none;
            margin-top: 12px;
            padding: 10px;
            border-radius: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .selected-preview-box strong {
            display: block;
            color: #166534;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .selected-preview-box img {
            width: 100%;
            max-height: 160px;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            display: block;
            margin-bottom: 10px;
        }

        .selected-preview-file {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4c3b91;
            background: #f2edff;
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 13px;
        }

        .current-photo-box {
            margin-top: 10px;
            padding: 10px;
            border-radius: 12px;
            background: #f9fafb;
            border: 1px solid #eee;
        }

        .current-photo-box img {
            width: 100%;
            max-height: 160px;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            display: block;
            margin-bottom: 10px;
        }

        .current-photo-box a {
            color: #4c3b91;
            font-weight: bold;
            text-decoration: none;
            font-size: 13px;
        }

        .current-photo-box a:hover {
            text-decoration: underline;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-top: 25px;
        }

        @media (max-width: 1000px) {
            .form-grid,
            .form-grid.two {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .form-grid,
            .form-grid.two {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-user-pen"></i>
            </div>

            <div>
                <h1>تعديل الموظف</h1>
                <p>تعديل بيانات الموظف حسب الصلاحيات الممنوحة</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('employees.index') }}" class="hero-btn white">
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

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-id-card"></i>
                    البيانات الأساسية
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label>الرقم الوظيفي</label>

                        @if(auth()->user()->hasPermission('employees.edit.employee_number'))
                            <input type="text" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}">
                        @else
                            <input type="text" value="{{ $employee->employee_number }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>الاسم الأول</label>

                        @if(auth()->user()->hasPermission('employees.edit.name'))
                            <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}">
                        @else
                            <input type="text" value="{{ $employee->first_name }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>الاسم الثاني</label>

                        @if(auth()->user()->hasPermission('employees.edit.name'))
                            <input type="text" name="second_name" value="{{ old('second_name', $employee->second_name) }}">
                        @else
                            <input type="text" value="{{ $employee->second_name }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>اللقب / اسم العائلة</label>

                        @if(auth()->user()->hasPermission('employees.edit.name'))
                            <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}">
                        @else
                            <input type="text" value="{{ $employee->last_name }}" disabled>
                        @endif
                    </div>

                    <div class="form-group email-field-card">
                        <label>البريد الإلكتروني الرسمي</label>

                        <span class="email-icon">
                            <i class="fas fa-envelope"></i>
                        </span>

                        @if(auth()->user()->hasPermission('employees.edit.email'))
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" placeholder="example@email.com">

                            <div class="email-help-box">
                                هذا هو الإيميل الرسمي الوحيد للموظف، ويُستخدم في بوابة الموظف لإرسال رمز التحقق ونسيت كلمة المرور.
                                عند تغييره من الإدارة سيتم إلغاء توثيق البريد السابق حتى يتحقق الموظف من البريد الجديد عند الدخول.
                            </div>
                        @else
                            <input type="email" value="{{ $employee->email }}" disabled>

                            <div class="email-help-box">
                                لا تملك صلاحية تعديل البريد الإلكتروني.
                            </div>
                        @endif

                        @if($employee->portal_email_verified_at)
                            <div class="email-verified-box">
                                <i class="fas fa-check-circle"></i>
                                البريد موثق بتاريخ: {{ optional($employee->portal_email_verified_at)->format('Y-m-d H:i') }}
                            </div>
                        @else
                            <div class="email-unverified-box">
                                <i class="fas fa-triangle-exclamation"></i>
                                البريد غير موثق بعد، وسيُطلب من الموظف التحقق عند الدخول.
                            </div>
                        @endif

                        @if($employee->portal_pending_email)
                            <div class="email-unverified-box">
                                <i class="fas fa-clock"></i>
                                يوجد بريد جديد بانتظار التحقق: {{ $employee->portal_pending_email }}
                            </div>
                        @endif

                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>رقم الجوال</label>

                        @if(auth()->user()->hasPermission('employees.edit.phone'))
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}">
                        @else
                            <input type="text" value="{{ $employee->phone }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>الجنسية</label>

                        @if(auth()->user()->hasPermission('employees.edit.nationality_id'))
                            <select name="nationality_id">
                                <option value="">اختر الجنسية</option>

                                @foreach($nationalities as $nationality)
                                    <option value="{{ $nationality->id }}" {{ old('nationality_id', $employee->nationality_id) == $nationality->id ? 'selected' : '' }}>
                                        {{ $nationality->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ $employee->nationality->name_ar ?? '-' }}" disabled>
                        @endif
                    </div>

                </div>
            </div>

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-briefcase"></i>
                    البيانات الوظيفية
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label>القسم</label>

                        @if(auth()->user()->hasPermission('employees.edit.department_id'))
                            <select name="department_id">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ $employee->department->name ?? '-' }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>الوظيفة</label>

                        @if(auth()->user()->hasPermission('employees.edit.position_id'))
                            <select name="position_id">
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->title }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ $employee->position->title ?? '-' }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>

                        @if(auth()->user()->hasPermission('employees.edit.status'))
                            <select name="status">
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>على رأس العمل</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                            </select>
                        @else
                            <input type="text" value="{{ $employee->status }}" disabled>
                        @endif
                    </div>

                    <div class="direct-manager-card">
                        <div class="direct-manager-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <div class="form-group">
                            <label>المدير المباشر</label>

                            <select name="direct_manager_user_id">
                                <option value="">اختر المدير المباشر للموظف</option>

                                @foreach($directManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('direct_manager_user_id', $employee->direct_manager_user_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                        @if(!empty($manager->email))
                                            - {{ $manager->email }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <div class="direct-manager-help">
                                يتم استخدام هذا المدير في مسار الموافقة على طلبات الإجازة. عند تقديم الموظف طلب إجازة، سيظهر الطلب أولًا للمدير المباشر المحدد هنا.
                            </div>

                            @error('direct_manager_user_id')
                            <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>تاريخ المباشرة</label>

                        @if(auth()->user()->hasPermission('employees.edit.hire_date'))
                            <input type="date" name="hire_date" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                        @else
                            <input type="text" value="{{ optional($employee->hire_date)->format('Y-m-d') }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>تاريخ انتهاء الخدمة</label>

                        @if(auth()->user()->hasPermission('employees.edit.termination_date'))
                            <input type="date" name="termination_date" value="{{ old('termination_date', optional($employee->termination_date)->format('Y-m-d')) }}">
                        @else
                            <input type="text" value="{{ optional($employee->termination_date)->format('Y-m-d') ?? '-' }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>سبب انتهاء الخدمة</label>

                        @if(auth()->user()->hasPermission('employees.edit.termination_reason'))
                            <input type="text" name="termination_reason" value="{{ old('termination_reason', $employee->termination_reason) }}">
                        @else
                            <input type="text" value="{{ $employee->termination_reason ?? '-' }}" disabled>
                        @endif
                    </div>

                </div>
            </div>

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-money-bill-wave"></i>
                    بيانات الراتب والبدلات
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label>الراتب الأساسي</label>

                        @if(auth()->user()->hasPermission('employees.edit.basic_salary'))
                            <input type="number" step="0.01" min="0" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}">
                        @else
                            <input type="text" value="{{ number_format($employee->basic_salary, 2) }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>بدل السكن</label>

                        @if(auth()->user()->hasPermission('employees.edit.housing_allowance'))
                            <input type="number" step="0.01" min="0" name="housing_allowance" value="{{ old('housing_allowance', $employee->housing_allowance) }}">
                        @else
                            <input type="text" value="{{ number_format($employee->housing_allowance, 2) }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>بدل النقل</label>

                        @if(auth()->user()->hasPermission('employees.edit.transport_allowance'))
                            <input type="number" step="0.01" min="0" name="transport_allowance" value="{{ old('transport_allowance', $employee->transport_allowance) }}">
                        @else
                            <input type="text" value="{{ number_format($employee->transport_allowance, 2) }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>بدل الطعام</label>

                        @if(auth()->user()->hasPermission('employees.edit.food_allowance'))
                            <input type="number" step="0.01" min="0" name="food_allowance" value="{{ old('food_allowance', $employee->food_allowance) }}">
                        @else
                            <input type="text" value="{{ number_format($employee->food_allowance, 2) }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>بدلات أخرى</label>

                        @if(auth()->user()->hasPermission('employees.edit.other_allowance'))
                            <input type="number" step="0.01" min="0" name="other_allowance" value="{{ old('other_allowance', $employee->other_allowance) }}">
                        @else
                            <input type="text" value="{{ number_format($employee->other_allowance, 2) }}" disabled>
                        @endif
                    </div>

                </div>
            </div>

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-building-columns"></i>
                    بيانات البنك
                </div>

                <div class="form-grid two">

                    <div class="form-group">
                        <label>اسم البنك</label>

                        @if(auth()->user()->hasPermission('employees.edit.bank_name'))
                            <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}">
                        @else
                            <input type="text" value="{{ $employee->bank_name ?? '-' }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>IBAN</label>

                        @if(auth()->user()->hasPermission('employees.edit.iban'))
                            <input type="text" name="iban" value="{{ old('iban', $employee->iban) }}">
                        @else
                            <input type="text" value="{{ $employee->iban ?? '-' }}" disabled>
                        @endif
                    </div>

                </div>
            </div>

            @if(
                auth()->user()->hasPermission('employee_iqamas.view') ||
                auth()->user()->hasPermission('employee_iqamas.edit') ||
                auth()->user()->hasPermission('employee_iqamas.photo.view') ||
                auth()->user()->hasPermission('employee_iqamas.photo.edit')
            )
                <div class="employee-form-section">
                    <div class="section-title">
                        <i class="fas fa-id-card"></i>
                        بيانات الإقامة
                    </div>

                    <div class="optional-note">
                        هذا القسم اختياري. إذا أدخلت رقم الإقامة يجب إدخال تاريخ انتهاء الإقامة.
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>رقم الإقامة</label>

                            @if(auth()->user()->hasPermission('employee_iqamas.edit'))
                                <input type="text" name="iqama_number" value="{{ old('iqama_number', $employee->latestIqama->iqama_number ?? '') }}" placeholder="رقم الإقامة">
                            @else
                                <input type="text" value="{{ $employee->latestIqama->iqama_number ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>اسم الكفيل</label>

                            @if(auth()->user()->hasPermission('employee_iqamas.edit'))
                                <input type="text" name="sponsor_name" value="{{ old('sponsor_name', $employee->latestIqama->sponsor_name ?? '') }}" placeholder="اسم الكفيل">
                            @else
                                <input type="text" value="{{ $employee->latestIqama->sponsor_name ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الإقامة</label>

                            @if(auth()->user()->hasPermission('employee_iqamas.edit'))
                                <input type="date" name="iqama_issue_date" value="{{ old('iqama_issue_date', optional($employee->latestIqama?->issue_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestIqama?->issue_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الإقامة</label>

                            @if(auth()->user()->hasPermission('employee_iqamas.edit'))
                                <input type="date" name="iqama_expiry_date" value="{{ old('iqama_expiry_date', optional($employee->latestIqama?->expiry_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestIqama?->expiry_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        @if(
                            auth()->user()->hasPermission('employee_iqamas.photo.view') ||
                            auth()->user()->hasPermission('employee_iqamas.photo.edit')
                        )
                            <div class="form-group">
                                <label>صورة الإقامة</label>

                                @if(auth()->user()->hasPermission('employee_iqamas.photo.edit'))
                                    <div class="file-upload-box">
                                        <input type="file" name="iqama_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'iqamaPhotoPreview', 'iqamaPhotoPreviewContent')">
                                        <div id="iqamaPhotoPreview" class="selected-preview-box">
                                            <strong>الصورة الجديدة قبل الحفظ:</strong>
                                            <div id="iqamaPhotoPreviewContent"></div>
                                        </div>
                                        <div class="file-help">
                                            ارفع صورة جديدة فقط إذا تريد استبدال الصورة الحالية. الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB
                                        </div>

                                        @if(auth()->user()->hasPermission('employee_iqamas.photo.view') && $employee->latestIqama?->photo)
                                            <div class="current-photo-box">
                                                <strong>الصورة الحالية:</strong>

                                                @if(Str::endsWith(strtolower($employee->latestIqama->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                    <img src="{{ asset('storage/' . $employee->latestIqama->photo) }}" alt="صورة الإقامة">
                                                @endif

                                                <a href="{{ asset('storage/' . $employee->latestIqama->photo) }}" target="_blank">
                                                    عرض الملف الحالي
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($employee->latestIqama?->photo)
                                        <div class="current-photo-box">
                                            @if(Str::endsWith(strtolower($employee->latestIqama->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                <img src="{{ asset('storage/' . $employee->latestIqama->photo) }}" alt="صورة الإقامة">
                                            @endif

                                            <a href="{{ asset('storage/' . $employee->latestIqama->photo) }}" target="_blank">
                                                عرض صورة الإقامة
                                            </a>
                                        </div>
                                    @else
                                        <input type="text" value="لا توجد صورة محفوظة" disabled>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الإقامة</label>

                            @if(auth()->user()->hasPermission('employee_iqamas.edit'))
                                <textarea name="iqama_notes" placeholder="ملاحظات الإقامة">{{ old('iqama_notes', $employee->latestIqama->notes ?? '') }}</textarea>
                            @else
                                <textarea disabled>{{ $employee->latestIqama->notes ?? '' }}</textarea>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(
                auth()->user()->hasPermission('employee_passports.view') ||
                auth()->user()->hasPermission('employee_passports.edit') ||
                auth()->user()->hasPermission('employee_passports.photo.view') ||
                auth()->user()->hasPermission('employee_passports.photo.edit')
            )
                <div class="employee-form-section">
                    <div class="section-title">
                        <i class="fas fa-passport"></i>
                        بيانات الجواز
                    </div>

                    <div class="optional-note">
                        هذا القسم اختياري. إذا أدخلت رقم الجواز يجب إدخال تاريخ انتهاء الجواز.
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>رقم الجواز</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <input type="text" name="passport_number" value="{{ old('passport_number', $employee->latestPassport->passport_number ?? '') }}" placeholder="رقم الجواز">
                            @else
                                <input type="text" value="{{ $employee->latestPassport->passport_number ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>بلد الجواز</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <input type="text" name="passport_country" value="{{ old('passport_country', $employee->latestPassport->country ?? '') }}" placeholder="مثال: اليمن">
                            @else
                                <input type="text" value="{{ $employee->latestPassport->country ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>مكان الإصدار</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <input type="text" name="passport_issue_place" value="{{ old('passport_issue_place', $employee->latestPassport->issue_place ?? '') }}" placeholder="مكان الإصدار">
                            @else
                                <input type="text" value="{{ $employee->latestPassport->issue_place ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الجواز</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <input type="date" name="passport_issue_date" value="{{ old('passport_issue_date', optional($employee->latestPassport?->issue_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestPassport?->issue_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الجواز</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <input type="date" name="passport_expiry_date" value="{{ old('passport_expiry_date', optional($employee->latestPassport?->expiry_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestPassport?->expiry_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        @if(
                            auth()->user()->hasPermission('employee_passports.photo.view') ||
                            auth()->user()->hasPermission('employee_passports.photo.edit')
                        )
                            <div class="form-group">
                                <label>صورة الجواز</label>

                                @if(auth()->user()->hasPermission('employee_passports.photo.edit'))
                                    <div class="file-upload-box">
                                        <input type="file" name="passport_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'passportPhotoPreview', 'passportPhotoPreviewContent')">
                                        <div id="passportPhotoPreview" class="selected-preview-box">
                                            <strong>الصورة الجديدة قبل الحفظ:</strong>
                                            <div id="passportPhotoPreviewContent"></div>
                                        </div>
                                        <div class="file-help">
                                            ارفع صورة جديدة فقط إذا تريد استبدال الصورة الحالية. الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB
                                        </div>

                                        @if(auth()->user()->hasPermission('employee_passports.photo.view') && $employee->latestPassport?->photo)
                                            <div class="current-photo-box">
                                                <strong>الصورة الحالية:</strong>

                                                @if(Str::endsWith(strtolower($employee->latestPassport->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                    <img src="{{ asset('storage/' . $employee->latestPassport->photo) }}" alt="صورة الجواز">
                                                @endif

                                                <a href="{{ asset('storage/' . $employee->latestPassport->photo) }}" target="_blank">
                                                    عرض الملف الحالي
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($employee->latestPassport?->photo)
                                        <div class="current-photo-box">
                                            @if(Str::endsWith(strtolower($employee->latestPassport->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                <img src="{{ asset('storage/' . $employee->latestPassport->photo) }}" alt="صورة الجواز">
                                            @endif

                                            <a href="{{ asset('storage/' . $employee->latestPassport->photo) }}" target="_blank">
                                                عرض صورة الجواز
                                            </a>
                                        </div>
                                    @else
                                        <input type="text" value="لا توجد صورة محفوظة" disabled>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الجواز</label>

                            @if(auth()->user()->hasPermission('employee_passports.edit'))
                                <textarea name="passport_notes" placeholder="ملاحظات الجواز">{{ old('passport_notes', $employee->latestPassport->notes ?? '') }}</textarea>
                            @else
                                <textarea disabled>{{ $employee->latestPassport->notes ?? '' }}</textarea>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(
                auth()->user()->hasPermission('employee_health_cards.view') ||
                auth()->user()->hasPermission('employee_health_cards.edit') ||
                auth()->user()->hasPermission('employee_health_cards.photo.view') ||
                auth()->user()->hasPermission('employee_health_cards.photo.edit')
            )
                <div class="employee-form-section">
                    <div class="section-title">
                        <i class="fas fa-notes-medical"></i>
                        بيانات الكرت الصحي
                    </div>

                    <div class="optional-note">
                        هذا القسم اختياري. إذا أدخلت رقم الكرت الصحي يجب إدخال تاريخ انتهاء الكرت الصحي.
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>رقم الكرت الصحي</label>

                            @if(auth()->user()->hasPermission('employee_health_cards.edit'))
                                <input type="text" name="health_card_number" value="{{ old('health_card_number', $employee->latestHealthCard->card_number ?? '') }}" placeholder="رقم الكرت الصحي">
                            @else
                                <input type="text" value="{{ $employee->latestHealthCard->card_number ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>جهة الإصدار</label>

                            @if(auth()->user()->hasPermission('employee_health_cards.edit'))
                                <input type="text" name="health_card_issuer" value="{{ old('health_card_issuer', $employee->latestHealthCard->issuer ?? '') }}" placeholder="جهة الإصدار">
                            @else
                                <input type="text" value="{{ $employee->latestHealthCard->issuer ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الكرت</label>

                            @if(auth()->user()->hasPermission('employee_health_cards.edit'))
                                <input type="date" name="health_card_issue_date" value="{{ old('health_card_issue_date', optional($employee->latestHealthCard?->issue_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestHealthCard?->issue_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الكرت</label>

                            @if(auth()->user()->hasPermission('employee_health_cards.edit'))
                                <input type="date" name="health_card_expiry_date" value="{{ old('health_card_expiry_date', optional($employee->latestHealthCard?->expiry_date)->format('Y-m-d')) }}">
                            @else
                                <input type="text" value="{{ optional($employee->latestHealthCard?->expiry_date)->format('Y-m-d') ?? '-' }}" disabled>
                            @endif
                        </div>

                        @if(
                            auth()->user()->hasPermission('employee_health_cards.photo.view') ||
                            auth()->user()->hasPermission('employee_health_cards.photo.edit')
                        )
                            <div class="form-group">
                                <label>صورة الكرت الصحي</label>

                                @if(auth()->user()->hasPermission('employee_health_cards.photo.edit'))
                                    <div class="file-upload-box">
                                        <input type="file" name="health_card_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'healthCardPhotoPreview', 'healthCardPhotoPreviewContent')">
                                        <div id="healthCardPhotoPreview" class="selected-preview-box">
                                            <strong>الصورة الجديدة قبل الحفظ:</strong>
                                            <div id="healthCardPhotoPreviewContent"></div>
                                        </div>
                                        <div class="file-help">
                                            ارفع صورة جديدة فقط إذا تريد استبدال الصورة الحالية. الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB
                                        </div>

                                        @if(auth()->user()->hasPermission('employee_health_cards.photo.view') && $employee->latestHealthCard?->photo)
                                            <div class="current-photo-box">
                                                <strong>الصورة الحالية:</strong>

                                                @if(Str::endsWith(strtolower($employee->latestHealthCard->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                    <img src="{{ asset('storage/' . $employee->latestHealthCard->photo) }}" alt="صورة الكرت الصحي">
                                                @endif

                                                <a href="{{ asset('storage/' . $employee->latestHealthCard->photo) }}" target="_blank">
                                                    عرض الملف الحالي
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($employee->latestHealthCard?->photo)
                                        <div class="current-photo-box">
                                            @if(Str::endsWith(strtolower($employee->latestHealthCard->photo), ['.jpg', '.jpeg', '.png', '.webp']))
                                                <img src="{{ asset('storage/' . $employee->latestHealthCard->photo) }}" alt="صورة الكرت الصحي">
                                            @endif

                                            <a href="{{ asset('storage/' . $employee->latestHealthCard->photo) }}" target="_blank">
                                                عرض صورة الكرت الصحي
                                            </a>
                                        </div>
                                    @else
                                        <input type="text" value="لا توجد صورة محفوظة" disabled>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الكرت الصحي</label>

                            @if(auth()->user()->hasPermission('employee_health_cards.edit'))
                                <textarea name="health_card_notes" placeholder="ملاحظات الكرت الصحي">{{ old('health_card_notes', $employee->latestHealthCard->notes ?? '') }}</textarea>
                            @else
                                <textarea disabled>{{ $employee->latestHealthCard->notes ?? '' }}</textarea>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-note-sticky"></i>
                    ملاحظات
                </div>

                <div class="form-grid">
                    <div class="form-group full">
                        <label>ملاحظات الموظف</label>

                        @if(auth()->user()->hasPermission('employees.edit.notes'))
                            <textarea name="notes">{{ old('notes', $employee->notes) }}</textarea>
                        @else
                            <textarea disabled>{{ $employee->notes }}</textarea>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    تحديث الموظف
                </button>

                <a href="{{ route('employees.index') }}" class="btn btn-danger">
                    إلغاء
                </a>
            </div>

        </form>

    </div>

    <script>
        function previewSelectedDocument(input, previewBoxId, previewContentId) {
            const previewBox = document.getElementById(previewBoxId);
            const previewContent = document.getElementById(previewContentId);

            previewContent.innerHTML = '';

            if (!input.files || !input.files[0]) {
                previewBox.style.display = 'none';
                return;
            }

            const file = input.files[0];
            const fileName = file.name;
            const fileType = file.type;

            previewBox.style.display = 'block';

            if (fileType.startsWith('image/')) {
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.onload = function () {
                    URL.revokeObjectURL(image.src);
                };

                previewContent.appendChild(image);
            } else {
                previewContent.innerHTML = `
                    <div class="selected-preview-file">
                        <i class="fas fa-file-pdf"></i>
                        ${fileName}
                    </div>
                `;
            }
        }
    </script>

@endsection
