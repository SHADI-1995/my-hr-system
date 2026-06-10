@extends('layouts.hr')

@section('title', 'تعديل دور')
@section('page-title', 'تعديل الدور والصلاحيات')

@section('content')

    <style>
        .permissions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .permissions-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .permission-module {
            margin-bottom: 30px;
            border: 1px solid #eee7ff;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .permission-module-header {
            background: #f2edff;
            color: #4c3b91;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .permission-module-header h4 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-check-wrapper {
            background: #fff;
            border: 1px solid #ded6ff;
            padding: 8px 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #4c3b91;
            font-weight: bold;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 15px;
            padding: 16px;
        }

        .permission-item {
            background: white;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #eee7ff;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .permission-item:hover {
            border-color: #6d5dfc;
            box-shadow: 0 5px 14px rgba(109, 93, 252, 0.10);
        }

        .permission-item input,
        .module-check-wrapper input,
        .all-check-wrapper input {
            width: auto;
            margin: 0;
        }

        .all-check-wrapper {
            background: #f8f7ff;
            color: #4c3b91;
            border: 1px solid #ded6ff;
            padding: 11px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-small-action {
            border: none;
            padding: 11px 15px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            background: #fff;
            color: #4c3b91;
            border: 1px solid #ded6ff;
        }

        .btn-small-action:hover {
            background: #f2edff;
        }

        .module-name-badge {
            background: rgba(76, 59, 145, 0.10);
            color: #4c3b91;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
        }

        @media (max-width: 650px) {
            .permissions-header,
            .permission-module-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-user-pen"></i>
            </div>

            <div>
                <h1>تعديل دور</h1>
                <p>تعديل بيانات الدور والتحكم في الصلاحيات المرتبطة به</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('roles.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card">

        @if ($errors->any())
            <div style="background:#fef2f2;color:#991b1b;padding:15px;border-radius:12px;margin-bottom:20px;">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            <div class="filters-row">

                <div class="filter-search">
                    <label>اسم الدور</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $role->name) }}"
                        placeholder="مثال: مدير الموارد البشرية"
                        required>

                    @error('name')
                    <div style="color:red;margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="filter-status">
                    <label>كود الدور</label>
                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $role->code) }}"
                        placeholder="مثال: hr_manager"
                        required>

                    @error('code')
                    <div style="color:red;margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="filter-actions">
                    <label style="background:#f8f7ff;padding:13px 18px;border-radius:12px;display:flex;align-items:center;gap:8px;height:50px;">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            style="width:auto;margin:0;"
                            {{ old('is_active', $role->is_active) ? 'checked' : '' }}>

                        دور نشط
                    </label>
                </div>

            </div>

            <br>

            <div class="card" style="background:#fbfaff;box-shadow:none;border:1px solid #eee7ff;">

                <div class="permissions-header">
                    <h3 style="color:#4c3b91;margin:0;">
                        <i class="fas fa-shield-halved"></i>
                        الصلاحيات
                    </h3>

                    <div class="permissions-actions">
                        <label class="all-check-wrapper">
                            <input type="checkbox" id="selectAllPermissions">
                            تحديد جميع الصلاحيات
                        </label>

                        <button type="button" class="btn-small-action" id="unselectAllPermissions">
                            <i class="fas fa-xmark"></i>
                            إلغاء تحديد الكل
                        </button>
                    </div>
                </div>

                @php
                    $selectedPermissions = old(
                        'permissions',
                        $role->permissions->pluck('id')->toArray()
                    );

                    $moduleNames = [
                        'employees' => 'الموظفين',
                        'employee_iqamas' => 'إقامات الموظفين',
                        'employee_passports' => 'جوازات الموظفين',
                        'employee_health_cards' => 'الكروت الصحية للموظفين',
                        'departments' => 'الأقسام',
                        'positions' => 'الوظائف',
                        'attendances' => 'الحضور والانصراف',
                        'leave_requests' => 'الإجازات',
                        'payrolls' => 'الرواتب',
                        'users' => 'المستخدمين',
                        'roles' => 'الأدوار والصلاحيات',
                        'reports' => 'التقارير',
                        'settings' => 'الإعدادات',
                        'audit_logs' => 'سجل النشاط',
                    ];
                @endphp

                @if($permissions->count())

                    @foreach($permissions->groupBy('module') as $module => $modulePermissions)

                        @php
                            $moduleKey = $module ?: 'general';
                            $moduleTitle = $moduleNames[$moduleKey] ?? ucfirst($moduleKey);
                        @endphp

                        <div class="permission-module" data-module="{{ $moduleKey }}">

                            <div class="permission-module-header">
                                <h4>
                                    <i class="fas fa-folder-open"></i>
                                    {{ $moduleTitle }}
                                    <span class="module-name-badge">{{ $moduleKey }}</span>
                                </h4>

                                <label class="module-check-wrapper">
                                    <input
                                        type="checkbox"
                                        class="select-module-permissions"
                                        data-module="{{ $moduleKey }}">
                                    تحديد صلاحيات القسم كامل
                                </label>
                            </div>

                            <div class="permissions-grid">

                                @foreach($modulePermissions as $permission)

                                    <label class="permission-item">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            class="permission-checkbox module-{{ $moduleKey }}"
                                            data-module="{{ $moduleKey }}"
                                            {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>

                                        <span>
                                            <strong>{{ $permission->name }}</strong>
                                            <br>
                                            <small style="color:#888;">
                                                {{ $permission->code }}
                                            </small>
                                        </span>
                                    </label>

                                @endforeach

                            </div>

                        </div>

                    @endforeach

                @else

                    <div style="background:#fff3cd;color:#856404;padding:15px;border-radius:12px;">
                        لا توجد صلاحيات مضافة حتى الآن.
                    </div>

                @endif

            </div>

            <br>

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث الدور
            </button>

            <a href="{{ route('roles.index') }}" class="btn btn-danger">
                رجوع
            </a>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAllPermissions');
            const unselectAllButton = document.getElementById('unselectAllPermissions');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const moduleCheckboxes = document.querySelectorAll('.select-module-permissions');

            function updateModuleCheckboxes() {
                moduleCheckboxes.forEach(function (moduleCheckbox) {
                    const moduleName = moduleCheckbox.dataset.module;
                    const modulePermissions = document.querySelectorAll('.permission-checkbox[data-module="' + moduleName + '"]');

                    if (modulePermissions.length === 0) {
                        moduleCheckbox.checked = false;
                        return;
                    }

                    const checkedCount = Array.from(modulePermissions).filter(function (checkbox) {
                        return checkbox.checked;
                    }).length;

                    moduleCheckbox.checked = checkedCount === modulePermissions.length;
                    moduleCheckbox.indeterminate = checkedCount > 0 && checkedCount < modulePermissions.length;
                });
            }

            function updateSelectAllCheckbox() {
                const total = permissionCheckboxes.length;
                const checked = Array.from(permissionCheckboxes).filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                selectAllCheckbox.checked = total > 0 && checked === total;
                selectAllCheckbox.indeterminate = checked > 0 && checked < total;
            }

            function updateAllStates() {
                updateModuleCheckboxes();
                updateSelectAllCheckbox();
            }

            selectAllCheckbox.addEventListener('change', function () {
                permissionCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });

                updateAllStates();
            });

            unselectAllButton.addEventListener('click', function () {
                permissionCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });

                updateAllStates();
            });

            moduleCheckboxes.forEach(function (moduleCheckbox) {
                moduleCheckbox.addEventListener('change', function () {
                    const moduleName = moduleCheckbox.dataset.module;
                    const modulePermissions = document.querySelectorAll('.permission-checkbox[data-module="' + moduleName + '"]');

                    modulePermissions.forEach(function (checkbox) {
                        checkbox.checked = moduleCheckbox.checked;
                    });

                    updateAllStates();
                });
            });

            permissionCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    updateAllStates();
                });
            });

            updateAllStates();
        });
    </script>

@endsection
