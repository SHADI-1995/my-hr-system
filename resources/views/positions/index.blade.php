@extends('layouts.hr')

@section('title', 'الوظائف')
@section('page-title', 'إدارة الوظائف')

@section('content')

    <div class="page-hero">

        <div class="hero-info">

            <div class="hero-icon">
                <i class="fas fa-briefcase"></i>
            </div>

            <div>
                <h1>الوظائف</h1>
                <p>إدارة الوظائف</p>
            </div>

        </div>

        <div class="hero-actions">

            <button onclick="exportTableToExcel()" class="hero-btn">
                <i class="fas fa-file-excel"></i>
                تصدير إكسل
            </button>

            <button onclick="exportTableToWord()" class="hero-btn">
                <i class="fas fa-file-word"></i>
                تصدير وورد
            </button>

            @if(auth()->user()->hasPermission('positions.create'))
                <a href="{{ route('positions.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة وظيفة
                </a>
            @endif

        </div>

    </div>

    @if(auth()->user()->hasPermission('positions.search'))
        <div class="card" style="margin-bottom:25px;">

            <form method="GET" action="{{ route('positions.index') }}">

                <div class="filters-row">

                    <div class="filter-search">
                        <label>بحث</label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="اسم الوظيفة أو الكود">
                    </div>

                    <div class="filter-status">
                        <label>القسم</label>

                        <select name="department_id">

                            <option value="">
                                جميع الأقسام
                            </option>

                            @foreach($departments as $department)

                                <option
                                    value="{{ $department->id }}"
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>

                                    {{ $department->name }}

                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="filter-actions">

                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i>
                            بحث
                        </button>

                        <a href="{{ route('positions.index') }}" class="btn btn-danger">
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
                <th>المسمى الوظيفي</th>
                <th>القسم</th>
                <th>الكود</th>
                <th>الحد الأدنى</th>
                <th>الحد الأعلى</th>
                <th>الحالة</th>

                @if(
                    auth()->user()->hasPermission('positions.edit') ||
                    auth()->user()->hasPermission('positions.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>

            @forelse($positions as $position)

                <tr>
                    <td>{{ $position->id }}</td>
                    <td>{{ $position->title }}</td>
                    <td>{{ $position->department->name ?? '-' }}</td>
                    <td>{{ $position->code }}</td>
                    <td>{{ $position->min_salary }}</td>
                    <td>{{ $position->max_salary }}</td>

                    <td>
                        @if($position->is_active)
                            <span class="badge badge-active">نشط</span>
                        @else
                            <span class="badge badge-inactive">غير نشط</span>
                        @endif
                    </td>

                    @if(
                        auth()->user()->hasPermission('positions.edit') ||
                        auth()->user()->hasPermission('positions.delete')
                    )
                        <td>

                            @if(auth()->user()->hasPermission('positions.edit'))
                                <a href="{{ route('positions.edit', $position->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('positions.delete'))
                                <form
                                    action="{{ route('positions.destroy', $position->id) }}"
                                    method="POST"
                                    style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف الوظيفة؟')">

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
                    <td colspan="8">
                        لا توجد وظائف
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="pagination-wrapper">
            {{ $positions->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
