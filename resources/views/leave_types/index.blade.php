@extends('layouts.hr')

@section('title', 'أنواع الإجازات')
@section('page-title', 'أنواع الإجازات')

@section('content')

    <style>
        .filter-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #eee;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            background: #fff;
        }

        .leave-types-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .leave-types-table {
            min-width: 1180px;
            table-layout: fixed;
        }

        .leave-types-table th,
        .leave-types-table td {
            white-space: nowrap;
            vertical-align: middle;
            font-size: 13px;
            line-height: 1.4;
            padding: 11px 10px;
        }

        .leave-types-table th {
            font-size: 13px;
            font-weight: 800;
        }

        .leave-types-table .name-col {
            width: 160px;
            font-weight: 800;
        }

        .leave-types-table .code-col {
            width: 105px;
        }

        .leave-types-table .small-col {
            width: 105px;
            text-align: center;
        }

        .leave-types-table .limit-col {
            width: 115px;
            text-align: center;
        }

        .leave-types-table .status-col {
            width: 95px;
            text-align: center;
        }

        .leave-types-table .actions-col {
            width: 210px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            min-width: 58px;
        }

        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }

        .table-actions {
            display: flex;
            gap: 6px;
            flex-wrap: nowrap;
            align-items: center;
        }

        .table-actions form {
            margin: 0;
        }

        .mini-btn {
            border: none;
            border-radius: 9px;
            padding: 7px 9px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            height: 32px;
        }

        .mini-edit { background: #eef2ff; color: #4c3b91; }
        .mini-toggle { background: #fef3c7; color: #92400e; }
        .mini-delete { background: #fee2e2; color: #991b1b; }

        @media (max-width: 800px) {
            .filter-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-list-check"></i>
            </div>

            <div>
                <h1>أنواع الإجازات</h1>
                <p>إدارة أنواع الإجازات وقواعد الخصم والموافقات والمرفقات</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('leave_types.create'))
                <a href="{{ route('leave-types.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة نوع إجازة
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fef2f2; color:#991b1b; padding:14px; border-radius:12px; margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('leave-types.index') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم نوع الإجازة أو الكود">
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status">
                        <option value="">الكل</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>مفعل</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="leave-types-table-wrapper">
            <table class="leave-types-table">
                <thead>
                <tr>
                    <th class="name-col">الاسم</th>
                    <th class="code-col">الكود</th>
                    <th class="small-col">مدفوعة؟</th>
                    <th class="small-col">تخصم من السنوي؟</th>
                    <th class="small-col">مرفق؟</th>
                    <th class="small-col">موافقة؟</th>
                    <th class="small-col">اعتماد تلقائي؟</th>
                    <th class="limit-col">الحد السنوي</th>
                    <th class="status-col">الحالة</th>
                    <th class="actions-col">الإجراءات</th>
                </tr>
                </thead>

                <tbody>
                @forelse($leaveTypes as $leaveType)
                    <tr>
                        <td class="name-col"><strong>{{ $leaveType->name }}</strong></td>
                        <td class="code-col"><span class="badge badge-blue">{{ $leaveType->code }}</span></td>
                        <td class="small-col">
                        <span class="badge {{ $leaveType->is_paid ? 'badge-green' : 'badge-red' }}">
                            {{ $leaveType->is_paid ? 'مدفوعة' : 'غير مدفوعة' }}
                        </span>
                        </td>
                        <td class="small-col">
                        <span class="badge {{ $leaveType->deduct_from_annual_balance ? 'badge-purple' : 'badge-gray' }}">
                            {{ $leaveType->deduct_from_annual_balance ? 'نعم' : 'لا' }}
                        </span>
                        </td>
                        <td class="small-col">{{ $leaveType->requires_attachment ? 'نعم' : 'لا' }}</td>
                        <td class="small-col">{{ $leaveType->requires_approval ? 'نعم' : 'لا' }}</td>
                        <td class="small-col">{{ $leaveType->auto_approved ? 'نعم' : 'لا' }}</td>
                        <td class="limit-col">{{ $leaveType->max_days_per_year ?? 'غير محدد' }}</td>
                        <td class="status-col">
                        <span class="badge {{ $leaveType->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $leaveType->is_active ? 'مفعل' : 'معطل' }}
                        </span>
                        </td>
                        <td class="actions-col">
                            <div class="table-actions">
                                @if(auth()->user()->hasPermission('leave_types.edit'))
                                    <a href="{{ route('leave-types.edit', $leaveType->id) }}" class="mini-btn mini-edit">
                                        <i class="fas fa-pen"></i>
                                        تعديل
                                    </a>

                                    <form action="{{ route('leave-types.toggle-status', $leaveType->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="mini-btn mini-toggle">
                                            <i class="fas fa-toggle-on"></i>
                                            {{ $leaveType->is_active ? 'تعطيل' : 'تفعيل' }}
                                        </button>
                                    </form>
                                @endif

                                @if(auth()->user()->hasPermission('leave_types.delete'))
                                    <form action="{{ route('leave-types.destroy', $leaveType->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف نوع الإجازة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mini-btn mini-delete">
                                            <i class="fas fa-trash"></i>
                                            حذف
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">لا توجد أنواع إجازات.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:15px;">
            {{ $leaveTypes->links() }}
        </div>
    </div>

@endsection
