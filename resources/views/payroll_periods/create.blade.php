@extends('layouts.hr')

@section('title', 'إنشاء مسير رواتب')
@section('page-title', 'إنشاء مسير رواتب')

@section('content')
    <style>
        .form-card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
        .field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:8px}
        .field input,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:12px 13px;font-weight:800;background:#fff;color:#111827;outline:none}
        .field input:focus,.field textarea:focus{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.12)}
        .field textarea{min-height:95px;resize:vertical}.full{grid-column:1/-1}
        .btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        .scope-card{border:1px solid #eeeafc;background:#faf9ff;border-radius:18px;padding:14px}
        .scope-options{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .scope-option{display:flex;align-items:flex-start;gap:10px;border:1px solid #ddd6fe;background:#fff;border-radius:16px;padding:14px;cursor:pointer;transition:.18s ease}
        .scope-option:hover{border-color:#6d5bd0;box-shadow:0 8px 22px rgba(76,59,145,.08)}
        .scope-option input{width:auto;margin-top:4px;box-shadow:none}
        .scope-option strong{display:block;color:#4c3b91;font-size:14px;margin-bottom:4px}
        .scope-option span{display:block;color:#6b7280;font-size:12px;line-height:1.7;font-weight:800}
        .groups-dropdown-wrap{position:relative}
        .groups-dropdown-btn{width:100%;border:1px solid #ddd6fe;background:#fff;color:#111827;border-radius:16px;padding:13px 14px;font-weight:900;display:flex;align-items:center;justify-content:space-between;gap:10px;cursor:pointer;transition:.18s ease}
        .groups-dropdown-btn:hover,.groups-dropdown-btn.active{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.10)}
        .groups-dropdown-btn .selected-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right}
        .groups-dropdown-menu{display:none;position:absolute;top:calc(100% + 8px);right:0;left:0;z-index:50;background:#fff;border:1px solid #ddd6fe;border-radius:18px;box-shadow:0 22px 55px rgba(76,59,145,.16);overflow:hidden}
        .groups-dropdown-menu.show{display:block}
        .groups-search-box{padding:12px;border-bottom:1px solid #eeeafc;background:#fbfaff}
        .groups-search-box input{width:100%;border:1px solid #ddd6fe;border-radius:13px;padding:11px 12px;font-weight:900;outline:none}
        .groups-actions{display:flex;gap:8px;padding:10px 12px;border-bottom:1px solid #eeeafc;background:#fff}
        .tiny-btn{border:0;border-radius:11px;padding:8px 10px;font-size:12px;font-weight:900;cursor:pointer;background:#ede9fe;color:#4c3b91}
        .tiny-btn.clear{background:#fee2e2;color:#991b1b}
        .groups-list{max-height:260px;overflow-y:auto;padding:8px}
        .group-check-row{display:flex;align-items:center;gap:10px;padding:10px 11px;border-radius:13px;cursor:pointer;transition:.15s ease}
        .group-check-row:hover{background:#f8f6ff}
        .group-check-row input{width:18px;height:18px;accent-color:#6d5bd0}
        .group-name{flex:1;font-weight:900;color:#374151}.group-code{color:#6b7280;font-size:12px;font-weight:800}
        .selected-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
        .selected-tag{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#f1edff;color:#4c3b91;padding:7px 10px;font-size:12px;font-weight:900}
        .help-box{margin-top:10px;color:#6b7280;font-size:12px;line-height:1.8;font-weight:800;background:#f9fafb;border:1px solid #eef2ff;border-radius:13px;padding:10px 12px}
        .error-text{display:block;color:#dc2626;margin-top:7px;font-size:12px;font-weight:900}
        .selected-groups-box{display:none}
        @media(max-width:800px){.grid,.scope-options{grid-template-columns:1fr}.groups-dropdown-menu{position:static;margin-top:8px}}
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
                                <input type="radio" name="payroll_group_scope" value="all" {{ old('payroll_group_scope', 'all') === 'all' ? 'checked' : '' }} onchange="togglePayrollGroups()">
                                <div>
                                    <strong>كل المجموعات</strong>
                                    <span>سيتم احتساب جميع الموظفين الداخلين في مسير الرواتب من جميع المجموعات.</span>
                                </div>
                            </label>

                            <label class="scope-option">
                                <input type="radio" name="payroll_group_scope" value="selected" {{ old('payroll_group_scope') === 'selected' ? 'checked' : '' }} onchange="togglePayrollGroups()">
                                <div>
                                    <strong>مجموعات محددة</strong>
                                    <span>اختر مجموعة واحدة أو أكثر من القائمة المنسدلة مع إمكانية البحث.</span>
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

                    <div class="groups-dropdown-wrap" id="groupsDropdownWrap">
                        <button type="button" class="groups-dropdown-btn" id="groupsDropdownBtn" onclick="toggleGroupsDropdown()">
                            <span class="selected-text" id="groupsSelectedText">اختر مجموعات الرواتب</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="groups-dropdown-menu" id="groupsDropdownMenu">
                            <div class="groups-search-box">
                                <input type="text" id="groupsSearchInput" placeholder="ابحث عن مجموعة..." onkeyup="filterPayrollGroups()" autocomplete="off">
                            </div>

                            <div class="groups-actions">
                                <button type="button" class="tiny-btn" onclick="selectVisibleGroups()">تحديد الظاهر</button>
                                <button type="button" class="tiny-btn clear" onclick="clearSelectedGroups()">مسح التحديد</button>
                            </div>

                            <div class="groups-list" id="groupsList">
                                @foreach($payrollGroups as $group)
                                    <label class="group-check-row" data-search="{{ mb_strtolower($group->name_ar . ' ' . ($group->name_en ?? '') . ' ' . ($group->code ?? '')) }}">
                                        <input type="checkbox" name="payroll_group_ids[]" value="{{ $group->id }}" data-name="{{ $group->name_ar }}" {{ in_array($group->id, old('payroll_group_ids', [])) ? 'checked' : '' }} onchange="updateSelectedGroupsText()">
                                        <span class="group-name">{{ $group->name_ar }}</span>
                                        @if(!empty($group->code))
                                            <span class="group-code">{{ $group->code }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="selected-tags" id="selectedGroupsTags"></div>

                    <div class="help-box">
                        افتح القائمة، ابحث عن المجموعة، ثم ضع علامة صح على مجموعة واحدة أو أكثر.
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
        function payrollScopeValue() {
            return document.querySelector('input[name="payroll_group_scope"]:checked')?.value || 'all';
        }

        function togglePayrollGroups() {
            const selectedScope = payrollScopeValue();
            const groupsBox = document.getElementById('selectedPayrollGroupsBox');
            const checkboxes = document.querySelectorAll('input[name="payroll_group_ids[]"]');
            const menu = document.getElementById('groupsDropdownMenu');
            const btn = document.getElementById('groupsDropdownBtn');

            if (selectedScope === 'selected') {
                groupsBox.style.display = 'block';
                checkboxes.forEach(item => item.disabled = false);
            } else {
                groupsBox.style.display = 'none';
                checkboxes.forEach(item => item.disabled = true);
                menu.classList.remove('show');
                btn.classList.remove('active');
            }

            updateSelectedGroupsText();
        }

        function toggleGroupsDropdown() {
            const menu = document.getElementById('groupsDropdownMenu');
            const btn = document.getElementById('groupsDropdownBtn');

            menu.classList.toggle('show');
            btn.classList.toggle('active');

            if (menu.classList.contains('show')) {
                setTimeout(() => document.getElementById('groupsSearchInput').focus(), 50);
            }
        }

        function filterPayrollGroups() {
            const keyword = document.getElementById('groupsSearchInput').value.trim().toLowerCase();
            const rows = document.querySelectorAll('.group-check-row');

            rows.forEach(row => {
                const searchText = row.getAttribute('data-search') || '';
                row.style.display = searchText.includes(keyword) ? 'flex' : 'none';
            });
        }

        function selectedGroupCheckboxes() {
            return Array.from(document.querySelectorAll('input[name="payroll_group_ids[]"]:checked:not(:disabled)'));
        }

        function updateSelectedGroupsText() {
            const selected = selectedGroupCheckboxes();
            const textBox = document.getElementById('groupsSelectedText');
            const tagsBox = document.getElementById('selectedGroupsTags');

            tagsBox.innerHTML = '';

            if (selected.length === 0) {
                textBox.textContent = 'اختر مجموعات الرواتب';
                return;
            }

            if (selected.length === 1) {
                textBox.textContent = selected[0].dataset.name;
            } else {
                textBox.textContent = 'تم اختيار ' + selected.length + ' مجموعات';
            }

            selected.forEach(item => {
                const tag = document.createElement('span');
                tag.className = 'selected-tag';
                tag.innerHTML = '<i class="fas fa-check"></i> ' + item.dataset.name;
                tagsBox.appendChild(tag);
            });
        }

        function selectVisibleGroups() {
            const visibleRows = Array.from(document.querySelectorAll('.group-check-row')).filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox && !checkbox.disabled) {
                    checkbox.checked = true;
                }
            });

            updateSelectedGroupsText();
        }

        function clearSelectedGroups() {
            document.querySelectorAll('input[name="payroll_group_ids[]"]').forEach(item => {
                item.checked = false;
            });

            updateSelectedGroupsText();
        }

        document.addEventListener('click', function (event) {
            const wrap = document.getElementById('groupsDropdownWrap');
            const menu = document.getElementById('groupsDropdownMenu');
            const btn = document.getElementById('groupsDropdownBtn');

            if (!wrap.contains(event.target)) {
                menu.classList.remove('show');
                btn.classList.remove('active');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            togglePayrollGroups();
            filterPayrollGroups();
            updateSelectedGroupsText();
        });
    </script>
@endsection
