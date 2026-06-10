@extends('layouts.hr')

@section('title', 'الموظفين')
@section('page-title', 'إدارة الموظفين')

@section('content')

    <style>
        .ui-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            font-size: 15px;
            line-height: 1;
            flex-shrink: 0;
        }

        .hero-icon .ui-icon {
            width: 42px;
            height: 42px;
            font-size: 30px;
        }

        .employees-page,
        .employees-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .employees-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(76, 59, 145, .08);
            padding: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .employees-table-wrapper {
            width: 100%;
            overflow-x: hidden;
            border: 1px solid #eeeafc;
            border-radius: 18px;
            background: #fff;
        }

        .employees-table {
            width: 100%;
            min-width: 0;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .employees-table th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 12px;
            font-weight: 900;
            padding: 13px 10px;
            border-bottom: 1px solid #e7e0ff;
            text-align: right;
            white-space: nowrap;
        }

        .employees-table td {
            padding: 11px 8px;
            border-bottom: 1px solid #f1f1f5;
            color: #1f2937;
            font-weight: 700;
            font-size: 13px;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .employees-table tr:hover td {
            background: #fbfaff;
        }

        .col-id {
            width: 50px;
            text-align: center !important;
        }

        .col-employee-number {
            width: 115px;
        }

        .col-name {
            width: 20%;
        }

        .col-nationality {
            width: 12%;
        }

        .col-department {
            width: 14%;
        }

        .col-position {
            width: 14%;
        }

        .col-status {
            width: 118px;
            text-align: center !important;
        }

        .col-actions {
            width: 230px;
            text-align: center !important;
        }

        .employee-name-text {
            display: inline-block;
            max-width: 100%;
            font-weight: 900;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }

        .actions-cell {
            display: flex;
            gap: 6px;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            overflow: visible;
        }

        .actions-cell form {
            display: inline-flex;
            margin: 0;
        }

        .btn-sm-action {
            height: 34px !important;
            min-width: 62px;
            padding: 0 9px !important;
            font-size: 12px !important;
            border-radius: 11px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin: 0 !important;
            white-space: nowrap;
            line-height: 1 !important;
        }

        .filters-row {
            align-items: end;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            align-items: end;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        @media (max-width: 900px) {
            .employees-card {
                padding: 14px;
            }

            .hero-actions {
                gap: 8px;
                flex-wrap: wrap;
            }

            .hero-actions .hero-btn {
                flex: 1 1 145px;
                justify-content: center;
            }

            .filter-actions {
                flex-wrap: wrap;
            }

            .filter-actions .btn {
                flex: 1 1 120px;
            }
        }
    </style>

    <div class="employees-page">

        <div class="page-hero">

            <div class="hero-info">

                <div class="hero-icon">
                    <span class="ui-icon">👥</span>
                </div>

                <div>
                    <h1>الموظفين</h1>
                    <p>إدارة الموظفين</p>
                </div>

            </div>

            <div class="hero-actions">

                <button onclick="exportTableToExcel()" class="hero-btn">
                    <span class="ui-icon">📊</span>
                    تصدير إكسل
                </button>

                <button onclick="exportTableToWord()" class="hero-btn">
                    <span class="ui-icon">📄</span>
                    تصدير وورد
                </button>

                @if(auth()->user()->hasPermission('employees.create'))
                    <a href="{{ route('employees.create') }}" class="hero-btn white">
                        <span class="ui-icon">＋</span>
                        إضافة موظف
                    </a>
                @endif

            </div>

        </div>

        @if(auth()->user()->hasPermission('employees.search'))
            <div class="employees-card">

                <form method="GET" action="{{ route('employees.index') }}">
                    <div class="filters-row">

                        <div class="filter-search">
                            <label>بحث</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="الاسم، الرقم الوظيفي، البريد، الجوال">
                        </div>

                        <div class="filter-status">
                            <label>القسم</label>
                            <select name="department_id">
                                <option value="">كل الأقسام</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @isset($nationalities)
                            <div class="filter-status">
                                <label>الجنسية</label>
                                <select name="nationality_id">
                                    <option value="">كل الجنسيات</option>
                                    @foreach($nationalities as $nationality)
                                        <option value="{{ $nationality->id }}" {{ request('nationality_id') == $nationality->id ? 'selected' : '' }}>
                                            {{ $nationality->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endisset

                        <div class="filter-status">
                            <label>الحالة</label>
                            <select name="status">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>على رأس العمل</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn">
                                <span class="ui-icon">🔎</span>
                                بحث
                            </button>

                            <a href="{{ route('employees.index') }}" class="btn btn-danger">
                                مسح
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        @endif

        <div class="employees-card">

            <div class="employees-table-wrapper">
                <table class="employees-table" id="employeesTable">
                    <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-employee-number">الرقم الوظيفي</th>
                        <th class="col-name">الاسم</th>
                        <th class="col-nationality">الجنسية</th>
                        <th class="col-department">القسم</th>
                        <th class="col-position">الوظيفة</th>
                        <th class="col-status">الحالة</th>
                        <th class="col-actions">الإجراءات</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="col-id">{{ $employee->id }}</td>
                            <td class="col-employee-number">{{ $employee->employee_number }}</td>
                            <td class="col-name">
                                <span class="employee-name-text">
                                    {{ $employee->full_name ?? $employee->name }}
                                </span>
                            </td>
                            <td class="col-nationality">{{ $employee->nationality->name_ar ?? '-' }}</td>
                            <td class="col-department">{{ $employee->department->name ?? '-' }}</td>
                            <td class="col-position">{{ $employee->position->title ?? '-' }}</td>

                            <td class="col-status">
                                @if($employee->status == 'active')
                                    <span class="badge badge-active">على رأس العمل</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge badge-inactive">غير نشط</span>
                                @elseif($employee->status == 'terminated')
                                    <span class="badge badge-inactive">منتهي الخدمة</span>
                                @else
                                    {{ $employee->status }}
                                @endif
                            </td>

                            <td class="col-actions">
                                <div class="actions-cell">
                                    @if(auth()->user()->hasPermission('employees.show'))
                                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm-action">
                                            <span class="ui-icon">👁</span>
                                            عرض
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasPermission('employees.edit'))
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm-action">
                                            <span class="ui-icon">✎</span>
                                            تعديل
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasPermission('employees.delete'))
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm-action" onclick="return confirm('هل أنت متأكد؟')">
                                                <span class="ui-icon">🗑</span>
                                                حذف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:30px; color:#6b7280; font-weight:900;">
                                لا يوجد موظفين
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="pagination-wrapper">
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
