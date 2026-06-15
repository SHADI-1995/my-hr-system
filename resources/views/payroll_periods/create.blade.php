@extends('layouts.hr')

@section('title', 'إنشاء مسير رواتب')
@section('page-title', 'إنشاء مسير رواتب')

@section('content')
    <style>
        .form-card{
            background:#fff;
            border:1px solid #eeeafc;
            border-radius:24px;
            padding:24px;
            box-shadow:0 16px 40px rgba(76,59,145,.07)
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:14px
        }

        .field label{
            display:block;
            color:#4c3b91;
            font-weight:900;
            margin-bottom:7px
        }

        .field input,
        .field select,
        .field textarea{
            width:100%;
            border:1px solid #ddd6fe;
            border-radius:14px;
            padding:11px 12px;
            font-weight:800;
            background:#fff;
            color:#111827;
            outline:none;
        }

        .field select[multiple]{
            min-height:150px;
            padding:10px;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus{
            border-color:#6d5bd0;
            box-shadow:0 0 0 4px rgba(109,91,208,.12);
        }

        .field textarea{
            min-height:90px
        }

        .full{
            grid-column:1/-1
        }

        .btn2{
            border:0;
            border-radius:13px;
            padding:12px 16px;
            font-weight:900;
            text-decoration:none;
            cursor:pointer;
        }

        .primary{
            background:#6d5bd0;
            color:#fff
        }

        .soft{
            background:#ede9fe;
            color:#4c3b91
        }

        .scope-card{
            border:1px solid #eeeafc;
            background:#faf9ff;
            border-radius:18px;
            padding:14px;
        }

        .scope-options{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:12px;
        }

        .scope-option{
            display:flex;
            align-items:flex-start;
            gap:10px;
            border:1px solid #ddd6fe;
            background:#fff;
            border-radius:16px;
            padding:14px;
            cursor:pointer;
            transition:.18s ease;
        }

        .scope-option:hover{
            border-color:#6d5bd0;
            box-shadow:0 8px 22px rgba(76,59,145,.08);
        }

        .scope-option input{
            width:auto;
            margin-top:4px;
            box-shadow:none;
        }

        .scope-option strong{
            display:block;
            color:#4c3b91;
            font-size:14px;
            margin-bottom:4px;
        }

        .scope-option span{
            display:block;
            color:#6b7280;
            font-size:12px;
            line-height:1.7;
            font-weight:800;
        }

        .help-box{
            margin-top:9px;
            color:#6b7280;
            font-size:12px;
            line-height:1.8;
            font-weight:800;
            background:#f9fafb;
            border:1px solid #eef2ff;
            border-radius:13px;
            padding:10px 12px;
        }

        .error-text{
            display:block;
            color:#dc2626;
            margin-top:7px;
            font-size:12px;
            font-weight:900;
        }

        .selected-groups-box{
            display:none;
        }

        @media(max-width:800px){
            .grid,
            .scope-options{
                grid-template-columns:1fr
            }
        }
    </style>

    <div class="form-card">
        @if(session('error'))
            <div style="background:#fef2f2;color:#991b1b;padding:14px;border-radius:14px;margin-bottom:18px;font-weight:900">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background:#fef2f2;color:#991b1b;padding:14px;border-radius:14px;margin-bottom:18px;font-weight:900">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:8px;margin-bottom:0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('payroll-periods.store') }}">
            @csrf

            <div class="grid">
                <div class="field">
                    <label>شهر المسير</label>
                    <input type="month" name="month" value="{{ old('month', now()->format('Y-m')) }}" required>
                    @error('month')
                    <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field full">
                    <label>نطاق مجموعات الرواتب</label>

                    <div class="scope-card">
                        <div class="scope-options">
                            <label class="scope-option">
                                <input
                                    type="radio"
                                    name="payroll_group_scope"
                                    value="all"
                                    {{ old('payroll_group_scope', 'all') === 'all' ? 'checked' : '' }}
                                    onchange="togglePayrollGroups()"
                                >

                                <div>
                                    <strong>كل المجموعات</strong>
                                    <span>
                                        سيتم احتساب جميع الموظفين الداخلين في مسير الرواتب من جميع المجموعات.
                                    </span>
                                </div>
                            </label>

                            <label class="scope-option">
                                <input
                                    type="radio"
                                    name="payroll_group_scope"
                                    value="selected"
                                    {{ old('payroll_group_scope') === 'selected' ? 'checked' : '' }}
                                    onchange="togglePayrollGroups()"
                                >

                                <div>
                                    <strong>مجموعات محددة</strong>
                                    <span>
                                        اختر مجموعة واحدة أو أكثر، وسيتم احتساب موظفي هذه المجموعات فقط.
                                    </span>
                                </div>
                            </label>
                        </div>

                        @error('payroll_group_scope')
                        <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="field full selected-groups-box" id="selectedPayrollGroupsBox">
                    <label>اختيار مجموعات الرواتب</label>

                    <select name="payroll_group_ids[]" id="payrollGroupIds" multiple>
                        @foreach($payrollGroups as $group)
                            <option
                                value="{{ $group->id }}"
                                {{ in_array($group->id, old('payroll_group_ids', [])) ? 'selected' : '' }}
                            >
                                {{ $group->name_ar }}
                                @if(!empty($group->code))
                                    - {{ $group->code }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <div class="help-box">
                        يمكنك اختيار مجموعة واحدة أو أكثر. في الكمبيوتر اضغط Ctrl مع النقر لاختيار أكثر من مجموعة.
                    </div>

                    @error('payroll_group_ids')
                    <small class="error-text">{{ $message }}</small>
                    @enderror

                    @error('payroll_group_ids.*')
                    <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field full">
                    <label>ملاحظات</label>
                    <textarea name="notes" placeholder="ملاحظات اختيارية">{{ old('notes') }}</textarea>
                    @error('notes')
                    <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="margin-top:18px;display:flex;gap:10px">
                <button class="btn2 primary" type="submit">حفظ الفترة</button>
                <a class="btn2 soft" href="{{ route('payroll-periods.index') }}">رجوع</a>
            </div>
        </form>
    </div>

    <script>
        function togglePayrollGroups() {
            const selectedScope = document.querySelector('input[name="payroll_group_scope"]:checked')?.value || 'all';
            const groupsBox = document.getElementById('selectedPayrollGroupsBox');
            const groupsSelect = document.getElementById('payrollGroupIds');

            if (selectedScope === 'selected') {
                groupsBox.style.display = 'block';
                groupsSelect.disabled = false;
            } else {
                groupsBox.style.display = 'none';
                groupsSelect.disabled = true;
            }
        }

        document.addEventListener('DOMContentLoaded', togglePayrollGroups);
    </script>
@endsection
