@extends('layouts.hr')

@section('title', 'موافقات الموارد البشرية على السلف')
@section('page-title', 'موافقات الموارد البشرية على السلف')

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
            opacity: .9;
            font-weight: 700;
            line-height: 1.7;
        }

        .approval-hero-icon {
            width: 68px;
            height: 68px;
            border-radius: 22px;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 14px 35px rgba(76, 59, 145, .06);
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

        .filter-card,
        .table-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 16px 40px rgba(76, 59, 145, .07);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
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
        }

        .form-group input,
        .form-group select {
            height: 46px;
        }

        .form-group textarea {
            min-height: 90px;
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
            min-height: 42px;
            border-radius: 14px;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .approval-btn.primary { background: #6d5bd0; color: #fff; }
        .approval-btn.success { background: #16a34a; color: #fff; }
        .approval-btn.danger { background: #dc2626; color: #fff; }
        .approval-btn.muted { background: #f3f4f6; color: #374151; }

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
            background: #dbeafe;
            color: #1d4ed8;
        }

        .approve-box {
            background: #f8f7ff;
            border: 1px solid #e9e5ff;
            border-radius: 16px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .approve-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
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
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 1100px) {
            .stats-grid,
            .filter-grid,
            .approve-grid {
                grid-template-columns: 1fr 1fr;
            }

            .approval-table th,
            .approval-table td {
                font-size: 11px;
                padding: 10px 6px;
            }
        }

        @media (max-width: 720px) {
            .approval-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid,
            .filter-grid,
            .approve-grid {
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
                <h1>موافقات الموارد البشرية على السلف</h1>
                <p>اعتماد طلبات السلف بعد موافقة المدير المباشر، وتسجيل السلفة تلقائيًا على الموظف.</p>
            </div>

            <div class="approval-hero-icon">
                <i class="fas fa-user-shield"></i>
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
                <div class="label">بانتظار HR</div>
                <div class="value">{{ $stats['pending_hr'] }}</div>
            </div>

            <div class="stat-card">
                <div class="label">تم تسجيلها</div>
                <div class="value">{{ $stats['registered'] }}</div>
            </div>

            <div class="stat-card">
                <div class="label">مرفوضة من HR</div>
                <div class="value">{{ $stats['rejected_by_hr'] }}</div>
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('hr-salary-advance-approvals.index') }}">
                <div class="filter-grid">
                    <div class="form-group">
                        <label>بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الموظف / الرقم الوظيفي / رقم الطلب">
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">بانتظار الموارد البشرية</option>
                            <option value="manager_approved_pending_hr" {{ request('status') == 'manager_approved_pending_hr' ? 'selected' : '' }}>بانتظار الموارد البشرية</option>
                            <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>تم تسجيلها</option>
                            <option value="rejected_by_hr" {{ request('status') == 'rejected_by_hr' ? 'selected' : '' }}>مرفوضة من HR</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="approval-btn primary">عرض</button>
                    <a href="{{ route('hr-salary-advance-approvals.index') }}" class="approval-btn muted">مسح</a>
                </div>
            </form>
        </div>

        <div class="table-card">
            <table class="approval-table">
                <thead>
                <tr>
                    <th style="width:62px;">#</th>
                    <th style="width:18%;">الموظف</th>
                    <th style="width:12%;">المبلغ المطلوب</th>
                    <th style="width:10%;">الأقساط</th>
                    <th style="width:13%;">موافقة المدير</th>
                    <th style="width:14%;">الحالة</th>
                    <th style="width:310px;">الإجراء</th>
                </tr>
                </thead>

                <tbody>
                @forelse($salaryAdvanceRequests as $salaryAdvanceRequest)
                    <tr>
                        <td>{{ $salaryAdvanceRequest->id }}</td>

                        <td>
                            <span class="employee-name">{{ $salaryAdvanceRequest->employee->display_name ?? $salaryAdvanceRequest->employee->full_name ?? '-' }}</span>
                            <span class="employee-sub">{{ $salaryAdvanceRequest->employee->employee_number ?? '-' }}</span>
                            <span class="employee-sub">{{ $salaryAdvanceRequest->employee->department->name ?? '-' }} / {{ $salaryAdvanceRequest->employee->position->title ?? '-' }}</span>
                        </td>

                        <td>{{ number_format((float) $salaryAdvanceRequest->amount, 2) }}</td>
                        <td>{{ $salaryAdvanceRequest->installments_count }}</td>
                        <td>{{ optional($salaryAdvanceRequest->direct_manager_approved_at)->format('Y-m-d H:i') }}</td>

                        <td>
                            <span class="status-pill">{{ $salaryAdvanceRequest->workflow_status_text }}</span>
                        </td>

                        <td>
                            @if($salaryAdvanceRequest->can_hr_approve)
                                <div class="approve-box">
                                    <form method="POST" action="{{ route('hr-salary-advance-approvals.approve', $salaryAdvanceRequest->id) }}">
                                        @csrf

                                        <div class="approve-grid">
                                            <div class="form-group">
                                                <label>المبلغ المعتمد</label>
                                                <input type="number" step="0.01" name="approved_amount" value="{{ old('approved_amount', $salaryAdvanceRequest->amount) }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>عدد الأقساط</label>
                                                <input type="number" name="installments_count" value="{{ old('installments_count', $salaryAdvanceRequest->installments_count) }}" min="1" max="60" required>
                                            </div>

                                            <div class="form-group">
                                                <label>شهر بداية الخصم</label>
                                                <input type="month" name="deduction_start_date" value="{{ old('deduction_start_date', optional($salaryAdvanceRequest->deduction_start_date)->format('Y-m')) }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom:10px;">
                                            <label>ملاحظة HR</label>
                                            <textarea name="hr_note" placeholder="ملاحظة اختيارية">{{ old('hr_note') }}</textarea>
                                        </div>

                                        <button class="approval-btn success" type="submit" onclick="return confirm('تأكيد قبول الطلب وتسجيل السلفة على الموظف؟')">
                                            قبول وتسجيل السلفة
                                        </button>
                                    </form>
                                </div>

                                <button type="button" class="approval-btn danger" onclick="toggleRejectBox('reject-box-{{ $salaryAdvanceRequest->id }}')">
                                    رفض
                                </button>

                                <div id="reject-box-{{ $salaryAdvanceRequest->id }}" class="reject-box">
                                    <form method="POST" action="{{ route('hr-salary-advance-approvals.reject', $salaryAdvanceRequest->id) }}">
                                        @csrf

                                        <div class="form-group">
                                            <label>سبب الرفض</label>
                                            <textarea name="hr_reject_reason" placeholder="اكتب سبب الرفض..." required></textarea>
                                        </div>

                                        <button class="approval-btn danger" type="submit" style="margin-top:10px;" onclick="return confirm('تأكيد رفض الطلب؟')">
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
                        <td colspan="7" style="text-align:center; padding:30px; color:#6b7280;">
                            لا توجد طلبات سلف حسب الفلاتر المحددة.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div style="margin-top:16px;">
                {{ $salaryAdvanceRequests->links() }}
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
