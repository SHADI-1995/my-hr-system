@extends('layouts.hr')

@section('title', 'المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-users-cog"></i>
            </div>

            <div>
                <h1>المستخدمين</h1>
                <p>إدارة حسابات المستخدمين والأدوار والصلاحيات</p>
            </div>
        </div>

        <div class="hero-actions">

            <button onclick="exportTableToExcel()" class="hero-btn">
                <i class="fas fa-file-excel"></i>
                تصدير Excel
            </button>

            <button onclick="exportTableToWord()" class="hero-btn">
                <i class="fas fa-file-word"></i>
                تصدير Word
            </button>

            @if(auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة مستخدم
                </a>
            @endif

        </div>
    </div>

    <div class="card" style="margin-bottom:25px;">

        <form method="GET" action="{{ route('users.index') }}">

            <div class="filters-row">

                <div class="filter-search">
                    <label>بحث</label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="الاسم أو البريد الإلكتروني أو اسم المستخدم">
                </div>

                <div class="filter-actions">

                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>

                    <a href="{{ route('users.index') }}" class="btn btn-danger">
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
                <th>الاسم</th>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>الدور</th>
                <th>تاريخ الإنشاء</th>

                @if(
                    auth()->user()->hasPermission('users.edit') ||
                    auth()->user()->hasPermission('users.delete')
                )
                    <th>الإجراءات</th>
                @endif
            </tr>
            </thead>

            <tbody>

            @forelse($users as $user)

                <tr>

                    <td>{{ $user->id }}</td>

                    <td>
                        <strong>{{ $user->name }}</strong>
                    </td>

                    <td>
                        {{ $user->username ?? '-' }}
                    </td>

                    <td>
                        {{ $user->email }}
                    </td>

                    <td>
                        @php
                            $userRole = $user->role()->first();
                        @endphp

                        @if($userRole)
                            <span class="badge badge-active">
                            {{ $userRole->name }}
                        </span>
                        @else
                            <span class="badge badge-inactive">
                            بدون دور
                        </span>
                        @endif
                    </td>

                    <td>
                        {{ $user->created_at->format('Y-m-d') }}
                    </td>

                    @if(
                        auth()->user()->hasPermission('users.edit') ||
                        auth()->user()->hasPermission('users.delete')
                    )
                        <td>

                            @if(auth()->user()->hasPermission('users.edit'))
                                <a href="{{ route('users.edit', $user->id) }}" class="btn">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('users.delete') && auth()->id() != $user->id)
                                <form
                                    action="{{ route('users.destroy', $user->id) }}"
                                    method="POST"
                                    style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('هل أنت متأكد من حذف المستخدم؟')">

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
                        لا يوجد مستخدمين
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
        </div>

    </div>

@endsection
