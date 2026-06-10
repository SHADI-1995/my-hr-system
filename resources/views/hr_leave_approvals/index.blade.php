@extends('layouts.hr')

@section('title', 'موافقات الموارد البشرية')
@section('page-title', 'موافقات الموارد البشرية')

@section('content')

    <style>
        .hr-approval-page,
        .hr-approval-page * {
            box-sizing: border-box;
            font-family: Tahoma, Arial, sans-serif;
        }

        .approval-hero {
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

        .approval-hero h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 900;
        }

        .approval-hero p {
            margin: 0;
            opacity: .92;
            font-weight: 700;
            line-height: 1.7;
        }

        .approval-hero-icon {
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card,
        .filter-card,
        .table-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 22px;
            box-shadow: 0 16px 40px rgba(76, 59, 145, .08);
        }

        .stat-card {
            padding: 18px;
        }

        .stat-card .label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .stat-card .value {
            color: #111827;
            font-size: 28px;
            font-weight: 900;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            transition: .18s ease;
            box-shadow: 0 20px 45px rgba(76, 59, 145, .11);
        }

        .filter-card,
        .table-card {
            padding: 22px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .table-scroll {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #eeeafc;
            border-radius: 18px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1fr;
            gap: 14px;
            align-items: end;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4c3b91;
            font-size: 13px;
            font-weight: 900;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid #ddd6fe;
            border-radius: 15px;
            padding: 0 13px;
            outline: none;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
            background: #fff;
            transition: .18s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #6d5bd0;
            box-shadow: 0 0 0 4px rgba(109, 91, 208, .12);
        }

        .form-group input,
        .form-group select {
            height: 46px;
        }

        .form-group textarea {
            min-height: 96px;
            padding-top: 12px;
            line-height: 1.8;
            resize: vertical;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .approval-btn {
            border: none;
            height: 42px;
            border-radius: 14px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .approval-btn.primary {
            background: #6d5bd0;
            color: #fff;
            box-shadow: 0 12px 28px rgba(109, 91, 208, .22);
        }

        .approval-btn.success {
            background: #16a34a;
            color: #fff;
        }

        .approval-btn.danger {
            background: #dc2626;
            color: #fff;
        }

        .approval-btn.muted {
            background: #f3f4f6;
            color: #374151;
        }

        .approval-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .approval-table th {
            background: #f1edff;
            color: #4c3b91;
            font-size: 12px;
            font-weight: 900;
            padding: 12px 8px;
            border-bottom: 1px solid #e7e0ff;
            line-height: 1.5;
        }

        .approval-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f1f5;
            color: #1f2937;
            font-weight: 700;
            font-size: 12px;
            line-height: 1.7;
            vertical-align: middle;
            word-break: break-word;
        }

        .employee-name {
            display: block;
            color: #111827;
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .employee-sub {
            display: block;
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }

        .status-pill {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.5;
        }

        .status-processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .row-actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .reject-box {
            display: none;
            margin-top: 10px;
            padding: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 16px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-weight: 800;
            line-height: 1.7;
        }

        .alert-success {
            background: #f1edff;
            color: #166534;
            border: 1px solid #e7e0ff;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 1050px) {
            .stats-grid,
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .approval-table th,
            .approval-table td {
                font-size: 11px;
                padding: 10px 6px;
            }
        }

        @media (max-width: 700px) {
            .approval-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid,
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .approval-table {
                table-layout: auto;
            }
        }
    </style>

    <div class="hr-approval-page">

        <div class="approval-hero">
            <div>
                <h1>موافقات الموارد البشرية</h1>
                <p>هنا تظهر الطلبات التي تمت الموافقة عليها من المدير المباشر وتنتظر القرار النهائي من الموارد البشرية.</p>
            </div>

            <div class="approval-hero-icon">
                HR
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>يوجد أخطاء:</strong>
                <ul style="margin:10px 0 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">بانتظار الموارد البشرية</div>
                <div class="value">{{ $stats['pending_hr'] }}</div>
            </div>

            <div class="stat-card">
                <div class="label">مقبولة من الموارد البشرية</div>
                <div class="value">{{ $stats['approved_by_hr'] }}</div>
            </div>

            <div class="stat-card">
                <div class="label">مرفوضة من الموارد البشرية</div>
                <div class="value">{{ $stats['rejected_by_hr'] }}</div>
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('hr-leave-approvals.index') }}">
                <div class="filter-grid">
                    <div class="form-group">
                        <label>بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الموظف / الرقم الوظيفي / الجوال">
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">بانتظار الموارد البشرية</option>
                            <option value="manager_approved_pending_hr" {{ request('status') == 'manager_approved_pending_hr' ? 'selected' : '' }}>بانتظار الموارد البشرية</option>
                            <option value="approved_by_hr" {{ request('status') == 'approved_by_hr' ? 'selected' : '' }}>مقبولة من الموارد البشرية</option>
                            <option value="rejected_by_hr" {{ request('status') == 'rejected_by_hr' ? 'selected' : '' }}>مرفوضة من الموارد البشرية</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="form-group">
                        <label>إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="approval-btn primary">عرض</button>
                    <a href="{{ route('hr-leave-approvals.index') }}" class="approval-btn muted">مسح</a>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-scroll">
                <table class="approval-table">
                    <thead>
                    <tr>
                        <th style="width:58px;">#</th>
                        <th style="width:21%;">الموظف</th>
                        <th style="width:15%;">نوع الإجازة</th>
                        <th style="width:16%;">الفترة</th>
                        <th style="width:70px;">الأيام</th>
                        <th style="width:17%;">المدير المباشر</th>
                        <th style="width:15%;">الحالة</th>
                        <th style="width:190px;">الإجراء</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($leaveRequests as $leaveRequest)
                        <tr>
                            <td>{{ $leaveRequest->id }}</td>

                            <td>
                                <span class="employee-name">{{ $leaveRequest->employee->display_name ?? '-' }}</span>
                                <span class="employee-sub">{{ $leaveRequest->employee->employee_number ?? '-' }}</span>
                                <span class="employee-sub">{{ $leaveRequest->employee->department->name ?? '-' }} / {{ $leaveRequest->employee->position->title ?? '-' }}</span>
                            </td>

                            <td>{{ $leaveRequest->leaveType->name ?? '-' }}</td>

                            <td>
                                {{ optional($leaveRequest->start_date)->format('Y-m-d') }}
                                <br>
                                <span class="employee-sub">إلى {{ optional($leaveRequest->end_date)->format('Y-m-d') }}</span>
                            </td>

                            <td>{{ $leaveRequest->days_count }}</td>

                            <td>
                                <span class="employee-name">{{ $leaveRequest->directManagerApprovedBy->name ?? '-' }}</span>
                                <span class="employee-sub">{{ optional($leaveRequest->direct_manager_approved_at)->format('Y-m-d H:i') ?? '-' }}</span>
                            </td>

                            <td>
                            <span class="status-pill {{ $leaveRequest->workflow_status_class }}">
                                {{ $leaveRequest->workflow_status_name }}
                            </span>
                            </td>

                            <td>
                                @if($leaveRequest->workflow_status === 'manager_approved_pending_hr')
                                    <div class="row-actions">
                                        <form method="POST" action="{{ route('hr-leave-approvals.approve', $leaveRequest->id) }}">
                                            @csrf
                                            <button type="submit" class="approval-btn success" onclick="return confirm('تأكيد اعتماد الطلب نهائياً؟ سيتم خصم الرصيد إن وجد.')">
                                                اعتماد
                                            </button>
                                        </form>

                                        <button type="button" class="approval-btn danger" onclick="toggleRejectBox('hr-reject-box-{{ $leaveRequest->id }}')">
                                            رفض
                                        </button>
                                    </div>

                                    <div id="hr-reject-box-{{ $leaveRequest->id }}" class="reject-box">
                                        <form method="POST" action="{{ route('hr-leave-approvals.reject', $leaveRequest->id) }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>سبب الرفض</label>
                                                <textarea name="hr_reject_reason" placeholder="اكتب سبب الرفض..." required></textarea>
                                            </div>

                                            <button type="submit" class="approval-btn danger" style="margin-top:10px;" onclick="return confirm('تأكيد رفض الطلب؟')">
                                                تأكيد الرفض
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="employee-sub">لا يوجد إجراء متاح</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:30px; color:#6b7280;">
                                لا توجد طلبات إجازة حسب الفلاتر المحددة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $leaveRequests->links() }}
            </div>
        </div>
    </div>

    <script>
        function toggleRejectBox(id) {
            const box = document.getElementById(id);
            if (!box) return;

            box.style.display = box.style.display === 'block' ? 'none' : 'block';
        }
    </script>

@endsection

