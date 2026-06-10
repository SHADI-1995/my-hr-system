@extends('layouts.hr')

@section('title', 'الأقسام')
@section('page-title', 'إدارة الأقسام')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-building"></i>
            </div>

            <div>
                <h1>الأقسام</h1>
                <p>إدارة الأقسام</p>
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

            @if(auth()->user()->hasPermission('departments.create'))
                <a href="{{ route('departments.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة قسم
                </a>
            @endif

        </div>
    </div>

    @if(auth()->user()->hasPermission('departments.search'))
        <div class="card" style="margin-bottom:25px;">

            <form method="GET" action="{{ route('departments.index') }}">

                <div class="filters-row">

                    <div class="filter-search">
                        <label>بحث</label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="اسم القسم أو الكود">
                    </div>

                    <div class="filter-status">
                        <label>الحالة</label>

                        <select name="status">
                            <option value="">كل الحالات</option>

                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                نشط
                            </option>

                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                غير نشط
                            </option>
                        </select>
                    </div>

                    <div class="filter-actions">

                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a href="{{ route('departments.index') }}" class="btn btn-danger">
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
                <th>اسم القسم</th>
                <th>الكود</th>
                <th>عدد الموظفين</th>
                <th>الحالة</th>

                @if(
                    auth()->user()->hasPermission('departments.edit') ||
                    auth()->user()->hasPermission('departments.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>

            @forelse($departments as $department)

                <tr>
                    <td>{{ $department->id }}</td>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->code }}</td>
                    <td>{{ $department->employees_count }}</td>

                    <td>
                        @if($department->is_active)
                            <span class="badge badge-active">نشط</span>
                        @else
                            <span class="badge badge-inactive">غير نشط</span>
                        @endif
                    </td>

                    @if(
                        auth()->user()->hasPermission('departments.edit') ||
                        auth()->user()->hasPermission('departments.delete')
                    )
                        <td>

                            @if(auth()->user()->hasPermission('departments.edit'))
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('departments.delete'))
                                <form
                                    action="{{ route('departments.destroy', $department->id) }}"
                                    method="POST"
                                    style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف القسم؟')">

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
                    <td colspan="6">
                        لا توجد أقسام
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="pagination-wrapper">
            {{ $departments->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
