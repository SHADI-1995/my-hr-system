@extends('layouts.hr')

@section('title', 'سجل النشاط')
@section('page-title', 'سجل النشاط')

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
            'audit-logs' => 'سجل النشاط',
            'audit_logs' => 'سجل النشاط',
        ];

        $models = [
            'Department' => 'قسم',
            'Position' => 'وظيفة',
            'Employee' => 'موظف',
            'Attendance' => 'حضور وانصراف',
            'LeaveRequest' => 'طلب إجازة',
            'Payroll' => 'راتب',
            'User' => 'مستخدم',
            'Role' => 'دور',
            'Permission' => 'صلاحية',
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

        function auditIndexFieldName($field, $fields) {
            return $fields[$field] ?? $field;
        }

        function auditIndexValueText($field, $value) {
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
                    'terminated' => 'منتهي',
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
    @endphp

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>

            <div>
                <h1>سجل النشاط</h1>
                <p>متابعة كل العمليات التي تمت داخل النظام</p>
            </div>
        </div>

        <div class="hero-actions">
            <button onclick="exportTableToExcel()" class="hero-btn">
                <i class="fas fa-file-excel"></i>
                تصدير Excel
            </button>

            <button onclick="exportTableToWord()" class="hero-btn">
                <i class="fas fa-file-word"></i>
                تصدير Word
            </button>
        </div>
    </div>

    <div class="card" style="margin-bottom:25px;">
        <form method="GET" action="{{ route('audit-logs.index') }}">
            <div class="filters-row">

                <div class="filter-search">
                    <label>نوع العملية</label>
                    <select name="action">
                        <option value="">كل العمليات</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>إضافة</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>تعديل</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>حذف</option>
                        <option value="approve" {{ request('action') == 'approve' ? 'selected' : '' }}>اعتماد</option>
                        <option value="reject" {{ request('action') == 'reject' ? 'selected' : '' }}>رفض</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>دخول</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>خروج</option>
                        <option value="export" {{ request('action') == 'export' ? 'selected' : '' }}>تصدير</option>
                    </select>
                </div>

                <div class="filter-status">
                    <label>المستخدم</label>
                    <select name="user_id">
                        <option value="">كل المستخدمين</option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-status">
                    <label>القسم</label>
                    <select name="module">
                        <option value="">كل الأقسام</option>
                        <option value="department" {{ request('module') == 'department' ? 'selected' : '' }}>الأقسام</option>
                        <option value="position" {{ request('module') == 'position' ? 'selected' : '' }}>الوظائف</option>
                        <option value="employee" {{ request('module') == 'employee' ? 'selected' : '' }}>الموظفين</option>
                        <option value="attendance" {{ request('module') == 'attendance' ? 'selected' : '' }}>الحضور والانصراف</option>
                        <option value="leaverequest" {{ request('module') == 'leaverequest' ? 'selected' : '' }}>الإجازات</option>
                        <option value="payroll" {{ request('module') == 'payroll' ? 'selected' : '' }}>الرواتب</option>
                        <option value="user" {{ request('module') == 'user' ? 'selected' : '' }}>المستخدمين</option>
                        <option value="role" {{ request('module') == 'role' ? 'selected' : '' }}>الأدوار</option>
                        <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>الدخول والخروج</option>
                        <option value="audit-logs" {{ request('module') == 'audit-logs' ? 'selected' : '' }}>سجل النشاط</option>
                    </select>
                </div>

                <div class="filter-status">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="filter-status">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>

                    <a href="{{ route('audit-logs.index') }}" class="btn btn-danger">
                        مسح
                    </a>
                </div>

            </div>
        </form>
    </div>

    <div class="card">

        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>المستخدم</th>
                <th>العملية</th>
                <th>القسم</th>
                <th>الموديل</th>
                <th>رقم السجل</th>
                <th>الوصف</th>
                <th>IP</th>
                <th>التاريخ</th>
                <th>التفاصيل</th>
            </tr>
            </thead>

            <tbody>
            @forelse($auditLogs as $log)
                @php
                    $actionName = $actions[$log->action] ?? $log->action;
                    $moduleName = $modules[$log->module] ?? $log->module ?? '-';
                    $modelName = $models[$log->model_type] ?? $log->model_type ?? '-';
                @endphp

                <tr>
                    <td>{{ $log->id }}</td>

                    <td>
                        @if($log->user)
                            <strong>{{ $log->user->name }}</strong>
                            <br>
                            <small>{{ $log->user->email }}</small>
                        @else
                            <span class="badge badge-inactive">غير معروف</span>
                        @endif
                    </td>

                    <td>
                    <span class="badge {{ in_array($log->action, ['delete', 'reject', 'logout']) ? 'badge-inactive' : 'badge-active' }}">
                        {{ $actionName }}
                    </span>
                    </td>

                    <td>{{ $moduleName }}</td>
                    <td>{{ $modelName }}</td>
                    <td>{{ $log->model_id ?? '-' }}</td>
                    <td>{{ $log->description ?? '-' }}</td>
                    <td>{{ $log->ip_address ?? '-' }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>

                    <td>
                        <a href="{{ route('audit-logs.show', $log->id) }}" class="btn">
                            <i class="fas fa-eye"></i>
                            عرض
                        </a>
                    </td>
                </tr>

                @if($log->old_values || $log->new_values)
                    <tr>
                        <td colspan="10" style="background:#faf7ff;">
                            <details>
                                <summary style="cursor:pointer; color:#4c3b91; font-weight:bold;">
                                    عرض مختصر لتفاصيل التغيير
                                </summary>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:15px;">

                                    <div>
                                        <strong style="color:#991b1b;">قبل التغيير</strong>

                                        @if($log->old_values)
                                            <table style="margin-top:10px;">
                                                <thead>
                                                <tr>
                                                    <th>الحقل</th>
                                                    <th>القيمة القديمة</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($log->old_values as $field => $value)
                                                    <tr>
                                                        <td>{{ auditIndexFieldName($field, $fields) }}</td>
                                                        <td>{{ auditIndexValueText($field, $value) }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p style="margin-top:10px;">لا توجد قيم قديمة</p>
                                        @endif
                                    </div>

                                    <div>
                                        <strong style="color:#065f46;">بعد التغيير</strong>

                                        @if($log->new_values)
                                            <table style="margin-top:10px;">
                                                <thead>
                                                <tr>
                                                    <th>الحقل</th>
                                                    <th>القيمة الجديدة</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($log->new_values as $field => $value)
                                                    <tr>
                                                        <td>{{ auditIndexFieldName($field, $fields) }}</td>
                                                        <td>{{ auditIndexValueText($field, $value) }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p style="margin-top:10px;">لا توجد قيم جديدة</p>
                                        @endif
                                    </div>

                                </div>
                            </details>
                        </td>
                    </tr>
                @endif

            @empty
                <tr>
                    <td colspan="10">لا توجد عمليات مسجلة</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $auditLogs->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
