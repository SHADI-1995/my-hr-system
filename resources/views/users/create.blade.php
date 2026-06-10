@extends('layouts.hr')

@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم جديد')

@section('content')

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-user-plus"></i>
            </div>

            ```
            <div>
                <h1>إضافة مستخدم</h1>
                <p>إنشاء حساب مستخدم جديد وربطه بدور وصلاحيات النظام</p>
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
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="filters-row">

                <div class="filter-search">
                    <label>الاسم الكامل</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="أدخل اسم المستخدم"
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
                        value="{{ old('username') }}"
                        placeholder="مثال: shadi">

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
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
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
                                {{ old('role_id') == $role->id ? 'selected' : '' }}>

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

            <div class="filters-row">

                <div class="filter-search">
                    <label>كلمة المرور</label>
                    <input
                        type="password"
                        name="password"
                        required>

                    @error('password')
                    <div style="color:red; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="filter-status">
                    <label>تأكيد كلمة المرور</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required>
                </div>

            </div>

            <br>

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                حفظ المستخدم
            </button>

            <a href="{{ route('users.index') }}" class="btn btn-danger">
                إلغاء
            </a>

        </form>
        ```

    </div>

@endsection
