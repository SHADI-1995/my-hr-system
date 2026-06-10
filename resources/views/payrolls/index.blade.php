@extends('layouts.hr')

@section('title', 'الرواتب')
@section('page-title', 'إدارة الرواتب')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>

            <div>
                <h1>الرواتب</h1>
                <p>إدارة الرواتب والمستحقات الشهرية</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="#" onclick="exportTableToExcel()" class="hero-btn">
                <i class="fas fa-file-excel"></i>
                تصدير إكسل
            </a>

            <a href="#" onclick="exportTableToWord()" class="hero-btn">
                <i class="fas fa-file-word"></i>
                تصدير وورد
            </a>

            @if(auth()->user()->hasPermission('payrolls.create'))
                <a href="{{ route('payrolls.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة راتب
                </a>
            @endif
        </div>
    </div>

    @if(auth()->user()->hasPermission('payrolls.search'))
        <div class="card" style="margin-bottom:25px;">
            <form method="GET" action="{{ route('payrolls.index') }}">
                <div class="filters-row">

                    <div class="filter-search">
                        <label>الموظف</label>
                        <select name="employee_id">
                            <option value="">كل الموظفين</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-status">
                        <label>الشهر</label>
                        <input type="month" name="month" value="{{ request('month') }}">
                    </div>

                    <div class="filter-status">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">كل الحالات</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a href="{{ route('payrolls.index') }}" class="btn btn-danger">
                            مسح
                        </a>
                    </div>

                </div>
            </form>
        </div>
    @endif

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>الموظف</th>
                <th>الشهر</th>
                <th>الراتب الأساسي</th>
                <th>البدلات</th>
                <th>الخصومات</th>
                <th>الصافي</th>
                <th>الحالة</th>

                @if(
                    auth()->user()->hasPermission('payrolls.edit') ||
                    auth()->user()->hasPermission('payrolls.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>
            @forelse($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->id }}</td>
                    <td>{{ $payroll->employee->name ?? '-' }}</td>
                    <td>{{ $payroll->month }}</td>
                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                    <td>{{ number_format($payroll->allowances, 2) }}</td>
                    <td>{{ number_format($payroll->deductions, 2) }}</td>
                    <td>{{ number_format($payroll->net_salary, 2) }}</td>

                    <td>
                        @if($payroll->status == 'paid')
                            <span class="badge badge-active">مدفوع</span>
                        @else
                            <span class="badge badge-inactive">مسودة</span>
                        @endif
                    </td>

                    @if(
                        auth()->user()->hasPermission('payrolls.edit') ||
                        auth()->user()->hasPermission('payrolls.delete')
                    )
                        <td>
                            @if(auth()->user()->hasPermission('payrolls.edit'))
                                <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('payrolls.delete'))
                                <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف الراتب؟')">

                                        <i class="fas fa-trash"></i>
                                        حذف

                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="9">لا توجد رواتب</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $payrolls->appends(request()->query())->links() }}
        </div>
    </div>

@endsection
