@extends('layouts.hr')

@section('title', 'الأدوار')
@section('page-title', 'إدارة الأدوار والصلاحيات')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-user-shield"></i>
            </div>

            <div>
                <h1>الأدوار والصلاحيات</h1>
                <p>إدارة أدوار المستخدمين والتحكم في الصلاحيات</p>
            </div>
        </div>

        <div class="hero-actions">
            @if(auth()->user()->hasPermission('roles.create'))
                <a href="{{ route('roles.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة دور
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="margin-bottom:20px; background:#ecfdf5; color:#065f46;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="card" style="margin-bottom:20px; background:#fef2f2; color:#991b1b;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="margin-bottom:25px;">
        <form method="GET" action="{{ route('roles.index') }}">
            <div class="filters-row">

                <div class="filter-search">
                    <label>بحث</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="اسم الدور أو الكود">
                </div>

                <div class="filter-status">
                    <label>الحالة</label>
                    <select name="status">
                        <option value="">كل الحالات</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>

                    <a href="{{ route('roles.index') }}" class="btn btn-danger">
                        مسح
                    </a>
                </div>

            </div>
        </form>
    </div>

    <div class="card">

        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>اسم الدور</th>
                <th>الكود</th>
                <th>عدد المستخدمين</th>
                <th>عدد الصلاحيات</th>
                <th>الحالة</th>

                @if(
                    auth()->user()->hasPermission('roles.edit') ||
                    auth()->user()->hasPermission('roles.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>
            @forelse($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>

                    <td>
                        <strong>{{ $role->name }}</strong>
                    </td>

                    <td>{{ $role->code }}</td>

                    <td>{{ $role->users_count }}</td>

                    <td>{{ $role->permissions_count }}</td>

                    <td>
                        @if($role->is_active)
                            <span class="badge badge-active">نشط</span>
                        @else
                            <span class="badge badge-inactive">غير نشط</span>
                        @endif
                    </td>

                    @if(
                        auth()->user()->hasPermission('roles.edit') ||
                        auth()->user()->hasPermission('roles.delete')
                    )
                        <td>
                            @if(auth()->user()->hasPermission('roles.edit'))
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('roles.delete'))
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا الدور؟')">

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
                    <td colspan="7">
                        لا توجد أدوار
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $roles->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
