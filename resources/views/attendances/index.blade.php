@extends('layouts.hr')

@section('title', 'الحضور والانصراف')
@section('page-title', 'إدارة الحضور والانصراف')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-clock"></i>
            </div>

            <div>
                <h1>الحضور والانصراف</h1>
                <p>إدارة سجلات الحضور اليومية</p>
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

            @if(auth()->user()->hasPermission('attendances.create'))
                <a href="{{ route('attendances.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    تسجيل حضور
                </a>
            @endif
        </div>
    </div>

    @if(auth()->user()->hasPermission('attendances.search'))
        <div class="card" style="margin-bottom:25px;">
            <form method="GET" action="{{ route('attendances.index') }}">
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
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">كل الحالات</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                            <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>إجازة</option>
                        </select>
                    </div>

                    <div class="filter-status">
                        <label>التاريخ</label>
                        <input type="date" name="attendance_date" value="{{ request('attendance_date') }}">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a href="{{ route('attendances.index') }}" class="btn btn-danger">
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
                <th>التاريخ</th>
                <th>الدخول</th>
                <th>الخروج</th>
                <th>الحالة</th>
                <th>ملاحظات</th>

                @if(
                    auth()->user()->hasPermission('attendances.edit') ||
                    auth()->user()->hasPermission('attendances.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->id }}</td>
                    <td>{{ $attendance->employee->name ?? '-' }}</td>
                    <td>{{ $attendance->attendance_date }}</td>
                    <td>{{ $attendance->check_in ?? '-' }}</td>
                    <td>{{ $attendance->check_out ?? '-' }}</td>

                    <td>
                        @if($attendance->status == 'present')
                            <span class="badge badge-active">حاضر</span>
                        @elseif($attendance->status == 'late')
                            <span class="badge badge-active">متأخر</span>
                        @elseif($attendance->status == 'leave')
                            <span class="badge badge-active">إجازة</span>
                        @else
                            <span class="badge badge-inactive">غائب</span>
                        @endif
                    </td>

                    <td>{{ $attendance->notes ?? '-' }}</td>

                    @if(
                        auth()->user()->hasPermission('attendances.edit') ||
                        auth()->user()->hasPermission('attendances.delete')
                    )
                        <td>
                            @if(auth()->user()->hasPermission('attendances.edit'))
                                <a href="{{ route('attendances.edit', $attendance->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('attendances.delete'))
                                <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
                    <td colspan="8">لا توجد سجلات حضور</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    </div>

@endsection
