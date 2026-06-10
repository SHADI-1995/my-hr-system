@extends('layouts.hr')

@section('title', 'تعديل مستخدم')
@section('page-title', 'تعديل بيانات المستخدم')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-user-pen"></i>
            </div>

            ```
            <div>
                <h1>تعديل مستخدم</h1>
                <p>تعديل بيانات الحساب والصلاحيات وكلمة المرور</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('users.index') }}" class="hero-btn white">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
        ```

    </div>

    <div class="card">

        ```
        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="filters-row">

                <div class="filter-search">
                    <label>الاسم الكامل</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required>

                    @error('name')
                    <div style="color:red; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="filter-status">
                    <label>اسم المستخدم</label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username', $user->username) }}">

                    @error('username')
                    <div style="color:red; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <br>

            <div class="filters-row">

                <div class="filter-search">
                    <label>البريد الإلكتروني</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required>

                    @error('email')
                    <div style="color:red; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="filter-status">
                    <label>الدور / الصلاحية</label>

                    <select name="role_id" required>

                        <option value="">اختر الدور</option>

                        @foreach($roles as $role)

                            <option
                                value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>

                                {{ $role->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('role_id')
                    <div style="color:red; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <br>

            <div class="card" style="background:#fbfaff; box-shadow:none; border:1px solid #eee7ff;">

                <h3 style="color:#4c3b91;">
                    <i class="fas fa-lock"></i>
                    تغيير كلمة المرور
                </h3>

                <br>

                <div class="filters-row">

                    <div class="filter-search">
                        <label>كلمة المرور الجديدة</label>

                        <input
                            type="password"
                            name="password"
                            placeholder="اتركها فارغة إذا لم ترغب بالتغيير">

                        @error('password')
                        <div style="color:red; margin-top:5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="filter-status">
                        <label>تأكيد كلمة المرور الجديدة</label>

                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="تأكيد كلمة المرور">
                    </div>

                </div>

            </div>

            <br>

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث المستخدم
            </button>

            <a href="{{ route('users.index') }}" class="btn btn-danger">
                إلغاء
            </a>

        </form>
        ```

    </div>

@endsection
