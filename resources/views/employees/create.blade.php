@extends('layouts.hr')

@section('title', 'إضافة موظف')
@section('page-title', 'إضافة موظف')

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

        .field-error {
            color: #dc2626;
            font-size: 12px;
            font-weight: 800;
            margin-top: 7px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-top: 25px;
        }

        .required {
            color: #dc2626;
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
                <i class="fas fa-user-plus"></i>
            </div>

            <div>
                <h1>إضافة موظف جديد</h1>
                <p>إدخال بيانات الموظف الأساسية والوظيفية والمالية والوثائق</p>
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

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="employee-form-section">
                <div class="section-title">
                    <i class="fas fa-id-card"></i>
                    البيانات الأساسية
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>الرقم الوظيفي</label>
                        <input type="text" value="سيتم توليده تلقائيًا بعد الحفظ" readonly style="background:#f3f4f6;">
                        <small style="color:#6b7280; margin-top:6px;">
                            الصيغة: EMP-000001
                        </small>
                    </div>

                    <div class="form-group">
                        <label>الاسم الأول <span class="required">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="الاسم الأول">
                    </div>

                    <div class="form-group">
                        <label>الاسم الثاني</label>
                        <input type="text" name="second_name" value="{{ old('second_name') }}" placeholder="الاسم الثاني">
                    </div>

                    <div class="form-group">
                        <label>اللقب / اسم العائلة</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="اللقب">
                    </div>

                    <div class="form-group email-field-card">
                        <label>البريد الإلكتروني الرسمي</label>
                        <span class="email-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com">

                        <div class="email-help-box">
                            هذا هو الإيميل الرسمي الوحيد للموظف، وسيُستخدم في بوابة الموظف لإرسال رمز التحقق ونسيت كلمة المرور.
                        </div>

                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>رقم الجوال</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label>الجنسية</label>
                        <select name="nationality_id">
                            <option value="">اختر الجنسية</option>

                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality->id }}" {{ old('nationality_id') == $nationality->id ? 'selected' : '' }}>
                                    {{ $nationality->name_ar }}
                                </option>
                            @endforeach
                        </select>
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
                        <label>القسم <span class="required">*</span></label>
                        <select name="department_id">
                            <option value="">اختر القسم</option>

                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>الوظيفة <span class="required">*</span></label>
                        <select name="position_id">
                            <option value="">اختر الوظيفة</option>

                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>على رأس العمل</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                        </select>
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
                                    <option value="{{ $manager->id }}" {{ old('direct_manager_user_id') == $manager->id ? 'selected' : '' }}>
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
                        <label>تاريخ المباشرة <span class="required">*</span></label>
                        <input type="date" name="hire_date" value="{{ old('hire_date') }}">
                    </div>

                    <div class="form-group">
                        <label>تاريخ انتهاء الخدمة</label>
                        <input type="date" name="termination_date" value="{{ old('termination_date') }}">
                    </div>

                    <div class="form-group">
                        <label>سبب انتهاء الخدمة</label>
                        <input type="text" name="termination_reason" value="{{ old('termination_reason') }}" placeholder="اختياري">
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
                        <label>الراتب الأساسي <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="basic_salary" value="{{ old('basic_salary') }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>بدل السكن</label>
                        <input type="number" step="0.01" min="0" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>بدل النقل</label>
                        <input type="number" step="0.01" min="0" name="transport_allowance" value="{{ old('transport_allowance', 0) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>بدل الطعام</label>
                        <input type="number" step="0.01" min="0" name="food_allowance" value="{{ old('food_allowance', 0) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>بدلات أخرى</label>
                        <input type="number" step="0.01" min="0" name="other_allowance" value="{{ old('other_allowance', 0) }}" placeholder="0.00">
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
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="مثال: الراجحي">
                    </div>

                    <div class="form-group">
                        <label>IBAN</label>
                        <input type="text" name="iban" value="{{ old('iban') }}" placeholder="SAxxxxxxxxxxxxxxxxxxxxxxxx">
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasPermission('employee_iqamas.create'))
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
                            <input type="text" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="رقم الإقامة">
                        </div>

                        <div class="form-group">
                            <label>اسم الكفيل</label>
                            <input type="text" name="sponsor_name" value="{{ old('sponsor_name') }}" placeholder="اسم الكفيل">
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الإقامة</label>
                            <input type="date" name="iqama_issue_date" value="{{ old('iqama_issue_date') }}">
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الإقامة</label>
                            <input type="date" name="iqama_expiry_date" value="{{ old('iqama_expiry_date') }}">
                        </div>

                        @if(auth()->user()->hasPermission('employee_iqamas.photo.create'))
                            <div class="form-group">
                                <label>صورة الإقامة</label>
                                <div class="file-upload-box">
                                    <input type="file" name="iqama_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'iqamaPhotoPreview', 'iqamaPhotoPreviewContent')">
                                    <div id="iqamaPhotoPreview" class="selected-preview-box">
                                        <strong>الصورة المختارة قبل الحفظ:</strong>
                                        <div id="iqamaPhotoPreviewContent"></div>
                                    </div>
                                    <div class="file-help">الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB</div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الإقامة</label>
                            <textarea name="iqama_notes" placeholder="ملاحظات الإقامة">{{ old('iqama_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->hasPermission('employee_passports.create'))
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
                            <input type="text" name="passport_number" value="{{ old('passport_number') }}" placeholder="رقم الجواز">
                        </div>

                        <div class="form-group">
                            <label>بلد الجواز</label>
                            <input type="text" name="passport_country" value="{{ old('passport_country') }}" placeholder="مثال: اليمن">
                        </div>

                        <div class="form-group">
                            <label>مكان الإصدار</label>
                            <input type="text" name="passport_issue_place" value="{{ old('passport_issue_place') }}" placeholder="مكان الإصدار">
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الجواز</label>
                            <input type="date" name="passport_issue_date" value="{{ old('passport_issue_date') }}">
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الجواز</label>
                            <input type="date" name="passport_expiry_date" value="{{ old('passport_expiry_date') }}">
                        </div>

                        @if(auth()->user()->hasPermission('employee_passports.photo.create'))
                            <div class="form-group">
                                <label>صورة الجواز</label>
                                <div class="file-upload-box">
                                    <input type="file" name="passport_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'passportPhotoPreview', 'passportPhotoPreviewContent')">
                                    <div id="passportPhotoPreview" class="selected-preview-box">
                                        <strong>الصورة المختارة قبل الحفظ:</strong>
                                        <div id="passportPhotoPreviewContent"></div>
                                    </div>
                                    <div class="file-help">الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB</div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الجواز</label>
                            <textarea name="passport_notes" placeholder="ملاحظات الجواز">{{ old('passport_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->hasPermission('employee_health_cards.create'))
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
                            <input type="text" name="health_card_number" value="{{ old('health_card_number') }}" placeholder="رقم الكرت الصحي">
                        </div>

                        <div class="form-group">
                            <label>جهة الإصدار</label>
                            <input type="text" name="health_card_issuer" value="{{ old('health_card_issuer') }}" placeholder="جهة الإصدار">
                        </div>

                        <div class="form-group">
                            <label>تاريخ إصدار الكرت</label>
                            <input type="date" name="health_card_issue_date" value="{{ old('health_card_issue_date') }}">
                        </div>

                        <div class="form-group">
                            <label>تاريخ انتهاء الكرت</label>
                            <input type="date" name="health_card_expiry_date" value="{{ old('health_card_expiry_date') }}">
                        </div>

                        @if(auth()->user()->hasPermission('employee_health_cards.photo.create'))
                            <div class="form-group">
                                <label>صورة الكرت الصحي</label>
                                <div class="file-upload-box">
                                    <input type="file" name="health_card_photo" accept="image/*,.pdf" onchange="previewSelectedDocument(this, 'healthCardPhotoPreview', 'healthCardPhotoPreviewContent')">
                                    <div id="healthCardPhotoPreview" class="selected-preview-box">
                                        <strong>الصورة المختارة قبل الحفظ:</strong>
                                        <div id="healthCardPhotoPreviewContent"></div>
                                    </div>
                                    <div class="file-help">الصيغ المسموحة: JPG, PNG, WEBP, PDF — الحجم الأقصى 4MB</div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group full">
                            <label>ملاحظات الكرت الصحي</label>
                            <textarea name="health_card_notes" placeholder="ملاحظات الكرت الصحي">{{ old('health_card_notes') }}</textarea>
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
                        <textarea name="notes" placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    حفظ الموظف
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
