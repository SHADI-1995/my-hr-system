@extends('layouts.hr')

@section('title', 'سياسات الإجازات')
@section('page-title', 'سياسات الإجازات')

@section('content')

    <style>
        .badge { display:inline-flex; align-items:center; padding:6px 11px; border-radius:999px; font-size:12px; font-weight:bold; }
        .badge-active { background:#dcfce7; color:#166534; }
        .badge-muted { background:#f3f4f6; color:#6b7280; }
        .badge-blue { background:#dbeafe; color:#1d4ed8; }
        .table-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .mini-btn { border:none; border-radius:9px; padding:7px 10px; cursor:pointer; text-decoration:none; font-size:12px; font-weight:bold; display:inline-flex; align-items:center; gap:5px; }
        .mini-edit { background:#eef2ff; color:#4c3b91; }
        .mini-active { background:#dcfce7; color:#166534; }
    </style>

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-sliders"></i></div>
            <div>
                <h1>سياسات الإجازات</h1>
                <p>تحديد طريقة احتساب الإجازات السنوية حسب تاريخ المباشرة أو السنة الميلادية أو الهجرية</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('leave_policies.create'))
                <a href="{{ route('leave-policies.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة سياسة
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5; color:#166534; padding:14px; border-radius:12px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>اسم السياسة</th>
                <th>قبل 5 سنوات</th>
                <th>بعد 5 سنوات</th>
                <th>طريقة الاحتساب</th>
                <th>ترحيل الرصيد</th>
                <th>استبعاد الويكند</th>
                <th>استبعاد الإجازات الرسمية</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
            </thead>

            <tbody>
            @forelse($policies as $policy)
                <tr>
                    <td><strong>{{ $policy->name }}</strong></td>
                    <td>{{ $policy->annual_days_before_5_years }} يوم</td>
                    <td>{{ $policy->annual_days_after_5_years }} يوم</td>
                    <td>
                        @if($policy->leave_year_type === 'hire_date')
                            <span class="badge badge-blue">حسب تاريخ المباشرة</span>
                        @elseif($policy->leave_year_type === 'gregorian')
                            <span class="badge badge-blue">حسب السنة الميلادية</span>
                        @else
                            <span class="badge badge-blue">حسب السنة الهجرية</span>
                        @endif
                    </td>
                    <td>
                        {{ $policy->carry_forward_enabled ? 'نعم' : 'لا' }}
                        @if($policy->carry_forward_enabled)
                            <br><small>حتى {{ $policy->max_carry_forward_days }} يوم</small>
                        @endif
                    </td>
                    <td>{{ $policy->exclude_weekends ? 'نعم' : 'لا' }}</td>
                    <td>{{ $policy->exclude_official_holidays ? 'نعم' : 'لا' }}</td>
                    <td>
                        @if($policy->is_active)
                            <span class="badge badge-active">مفعلة</span>
                        @else
                            <span class="badge badge-muted">غير مفعلة</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            @if(auth()->user()->hasPermission('leave_policies.edit'))
                                <a href="{{ route('leave-policies.edit', $policy->id) }}" class="mini-btn mini-edit">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>

                                @if(!$policy->is_active)
                                    <form action="{{ route('leave-policies.activate', $policy->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="mini-btn mini-active">
                                            <i class="fas fa-check"></i>
                                            تفعيل
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">لا توجد سياسات إجازات.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:15px;">
            {{ $policies->links() }}
        </div>
    </div>

@endsection

