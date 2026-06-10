@extends('layouts.hr')

@section('title', 'تفاصيل سجل النشاط')
@section('page-title', 'تفاصيل سجل النشاط')

@section('content')

    @php
        $actions = [
            'create' => 'إضافة',
            'update' => 'تعديل',
            'delete' => 'حذف',
            'approve' => 'اعتماد',
            'reject' => 'رفض',
            'login' => 'دخول',
            'logout' => 'خروج',
            'export' => 'تصدير',
        ];

        $modules = [
            'department' => 'الأقسام',
            'departments' => 'الأقسام',
            'position' => 'الوظائف',
            'positions' => 'الوظائف',
            'employee' => 'الموظفين',
            'employees' => 'الموظفين',
            'attendance' => 'الحضور والانصراف',
            'attendances' => 'الحضور والانصراف',
            'leaverequest' => 'الإجازات',
            'leave_requests' => 'الإجازات',
            'payroll' => 'الرواتب',
            'payrolls' => 'الرواتب',
            'user' => 'المستخدمين',
            'users' => 'المستخدمين',
            'role' => 'الأدوار والصلاحيات',
            'roles' => 'الأدوار والصلاحيات',
            'auth' => 'الدخول والخروج',
        ];

        $fields = [
            'id' => 'رقم السجل',
            'name' => 'الاسم',
            'code' => 'الكود',
            'description' => 'الوصف',
            'is_active' => 'الحالة',
            'department_id' => 'القسم',
            'position_id' => 'الوظيفة',
            'employee_id' => 'الموظف',
            'employee_number' => 'رقم الموظف',
            'title' => 'المسمى الوظيفي',
            'email' => 'البريد الإلكتروني',
            'username' => 'اسم المستخدم',
            'phone' => 'رقم الجوال',
            'hire_date' => 'تاريخ التعيين',
            'basic_salary' => 'الراتب الأساسي',
            'min_salary' => 'الحد الأدنى للراتب',
            'max_salary' => 'الحد الأعلى للراتب',
            'attendance_date' => 'تاريخ الحضور',
            'check_in' => 'وقت الدخول',
            'check_out' => 'وقت الخروج',
            'status' => 'الحالة',
            'notes' => 'ملاحظات',
            'leave_type' => 'نوع الإجازة',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'days_count' => 'عدد الأيام',
            'reason' => 'السبب',
            'month' => 'الشهر',
            'allowances' => 'البدلات',
            'deductions' => 'الخصومات',
            'net_salary' => 'صافي الراتب',
            'role_id' => 'الدور',
            'password' => 'كلمة المرور',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التعديل',
            'type' => 'نوع التصدير',
            'page' => 'الصفحة',
        ];

        function auditFieldName($field, $fields) {
            return $fields[$field] ?? $field;
        }

        function auditValueText($field, $value) {
            if (is_null($value)) {
                return '-';
            }

            if ($field === 'is_active') {
                return $value ? 'نشط' : 'غير نشط';
            }

            if ($field === 'status') {
                return match($value) {
                    'active' => 'نشط',
                    'inactive' => 'غير نشط',
                    'present' => 'حاضر',
                    'absent' => 'غائب',
                    'late' => 'متأخر',
                    'leave' => 'إجازة',
                    'pending' => 'قيد المراجعة',
                    'approved' => 'مقبولة',
                    'rejected' => 'مرفوضة',
                    'draft' => 'مسودة',
                    'paid' => 'مدفوع',
                    default => $value,
                };
            }

            if ($field === 'leave_type') {
                return match($value) {
                    'annual' => 'سنوية',
                    'sick' => 'مرضية',
                    'emergency' => 'طارئة',
                    'unpaid' => 'بدون راتب',
                    default => $value,
                };
            }

            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            return $value;
        }

        $actionName = $actions[$auditLog->action] ?? $auditLog->action;
        $moduleName = $modules[$auditLog->module] ?? $auditLog->module ?? '-';
    @endphp

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>

            <div>
                <h1>تفاصيل سجل النشاط</h1>
                <p>عرض تفاصيل العملية المسجلة داخل النظام</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('audit-logs.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:25px;">
        <table>
            <tr>
                <th>رقم السجل</th>
                <td>{{ $auditLog->id }}</td>
            </tr>

            <tr>
                <th>المستخدم</th>
                <td>
                    @if($auditLog->user)
                        <strong>{{ $auditLog->user->name }}</strong>
                        <br>
                        <small>{{ $auditLog->user->email }}</small>
                    @else
                        غير معروف
                    @endif
                </td>
            </tr>

            <tr>
                <th>نوع العملية</th>
                <td>
                <span class="badge {{ in_array($auditLog->action, ['delete', 'reject', 'logout']) ? 'badge-inactive' : 'badge-active' }}">
                    {{ $actionName }}
                </span>
                </td>
            </tr>

            <tr>
                <th>القسم</th>
                <td>{{ $moduleName }}</td>
            </tr>

            <tr>
                <th>الموديل</th>
                <td>{{ $auditLog->model_type ?? '-' }}</td>
            </tr>

            <tr>
                <th>رقم السجل المتأثر</th>
                <td>{{ $auditLog->model_id ?? '-' }}</td>
            </tr>

            <tr>
                <th>الوصف</th>
                <td>{{ $auditLog->description ?? '-' }}</td>
            </tr>

            <tr>
                <th>IP Address</th>
                <td>{{ $auditLog->ip_address ?? '-' }}</td>
            </tr>

            <tr>
                <th>User Agent</th>
                <td style="direction:ltr; text-align:left;">
                    {{ $auditLog->user_agent ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>التاريخ والوقت</th>
                <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        <div class="card">
            <h3 style="color:#991b1b; margin-bottom:15px;">
                <i class="fas fa-clock-rotate-left"></i>
                القيم قبل العملية
            </h3>

            @if($auditLog->old_values)
                <table>
                    <thead>
                    <tr>
                        <th>الحقل</th>
                        <th>القيمة القديمة</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($auditLog->old_values as $field => $value)
                        <tr>
                            <td>{{ auditFieldName($field, $fields) }}</td>
                            <td>{{ auditValueText($field, $value) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد قيم قديمة</p>
            @endif
        </div>

        <div class="card">
            <h3 style="color:#065f46; margin-bottom:15px;">
                <i class="fas fa-circle-check"></i>
                القيم بعد العملية
            </h3>

            @if($auditLog->new_values)
                <table>
                    <thead>
                    <tr>
                        <th>الحقل</th>
                        <th>القيمة الجديدة</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($auditLog->new_values as $field => $value)
                        <tr>
                            <td>{{ auditFieldName($field, $fields) }}</td>
                            <td>{{ auditValueText($field, $value) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد قيم جديدة</p>
            @endif
        </div>

    </div>

    <div class="card" style="margin-top:25px;">
        <details>
            <summary style="cursor:pointer; color:#4c3b91; font-weight:bold;">
                عرض البيانات الخام JSON
            </summary>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:15px;">
                <div>
                    <strong style="color:#991b1b;">قبل العملية</strong>
                    <pre style="white-space:pre-wrap; background:#fef2f2; padding:15px; border-radius:12px; direction:ltr; text-align:left; overflow:auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div>
                    <strong style="color:#065f46;">بعد العملية</strong>
                    <pre style="white-space:pre-wrap; background:#ecfdf5; padding:15px; border-radius:12px; direction:ltr; text-align:left; overflow:auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </details>
    </div>

@endsection
