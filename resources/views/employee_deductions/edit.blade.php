@extends('layouts.hr')

@section('title', 'تعديل استقطاع')
@section('page-title', 'تعديل استقطاع')

@section('content')
    <style>
        .form-card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
        .field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:7px}
        .field input,.field select,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:11px 12px;font-weight:800;outline:none;background:#fff;color:#111827}
        .field input:focus,.field select:focus,.field textarea:focus{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.12)}
        .field textarea{min-height:90px;resize:vertical}.full{grid-column:1/-1}
        .btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        .mode-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:10px}
        .mode-card{border:1px solid #ddd6fe;background:#fff;border-radius:16px;padding:12px;cursor:pointer;transition:.18s ease;min-height:92px}
        .mode-card:hover{border-color:#6d5bd0;box-shadow:0 10px 26px rgba(76,59,145,.08)}
        .mode-card input{width:auto;margin:0 0 8px 0;accent-color:#6d5bd0}
        .mode-card strong{display:block;color:#4c3b91;font-size:13px;margin-bottom:5px}
        .mode-card span{display:block;color:#6b7280;font-size:11px;line-height:1.6;font-weight:800}
        .hint{color:#6b7280;font-size:12px;line-height:1.8;font-weight:800;background:#f9fafb;border:1px solid #eef2ff;border-radius:13px;padding:10px 12px;margin-top:8px}
        .dynamic-section{display:none}.error-text{color:#dc2626;font-size:12px;font-weight:900;margin-top:6px;display:block}
        .months-dropdown-wrap{position:relative}.months-dropdown-btn{width:100%;border:1px solid #ddd6fe;background:#fff;color:#111827;border-radius:16px;padding:13px 14px;font-weight:900;display:flex;align-items:center;justify-content:space-between;gap:10px;cursor:pointer;transition:.18s ease}
        .months-dropdown-btn:hover,.months-dropdown-btn.active{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.10)}
        .months-dropdown-btn .selected-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right}
        .months-dropdown-menu{display:none;position:absolute;top:calc(100% + 8px);right:0;left:0;z-index:50;background:#fff;border:1px solid #ddd6fe;border-radius:18px;box-shadow:0 22px 55px rgba(76,59,145,.16);overflow:hidden}
        .months-dropdown-menu.show{display:block}.months-search-box{padding:12px;border-bottom:1px solid #eeeafc;background:#fbfaff}
        .months-search-box input{width:100%;border:1px solid #ddd6fe;border-radius:13px;padding:11px 12px;font-weight:900;outline:none}
        .months-actions{display:flex;gap:8px;padding:10px 12px;border-bottom:1px solid #eeeafc;background:#fff}
        .tiny-btn{border:0;border-radius:11px;padding:8px 10px;font-size:12px;font-weight:900;cursor:pointer;background:#ede9fe;color:#4c3b91}.tiny-btn.clear{background:#fee2e2;color:#991b1b}
        .months-list{max-height:260px;overflow-y:auto;padding:8px}.month-check-row{display:flex;align-items:center;gap:10px;padding:10px 11px;border-radius:13px;cursor:pointer;transition:.15s ease}
        .month-check-row:hover{background:#f8f6ff}.month-check-row input{width:18px;height:18px;accent-color:#6d5bd0}.month-name{flex:1;font-weight:900;color:#374151}
        .selected-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}.selected-tag{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#f1edff;color:#4c3b91;padding:7px 10px;font-size:12px;font-weight:900}
        .summary-box{border:1px dashed #c4b5fd;background:#fbfaff;border-radius:16px;padding:12px;color:#4c3b91;font-size:13px;font-weight:900;line-height:1.8}
        @media(max-width:1100px){.mode-cards{grid-template-columns:repeat(2,1fr)}}@media(max-width:800px){.grid,.mode-cards{grid-template-columns:1fr}.months-dropdown-menu{position:static;margin-top:8px}}
    </style>

    <div class="form-card">
        @if ($errors->any())
            <div style="background:#fef2f2;color:#991b1b;padding:14px;border-radius:14px;margin-bottom:18px;font-weight:900">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin-top:8px;margin-bottom:0">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employee-deductions.update', $employeeDeduction) }}">
            @csrf
            @method('PUT')

            <div class="grid">
                <div class="field">
                    <label>الموظف</label>
                    <select name="employee_id" required>
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id', $employeeDeduction->employee_id) == $employee->id)>
                                {{ $employee->display_name }} - {{ $employee->employee_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label>نوع الاستقطاع</label>
                    <select name="deduction_type_id" required>
                        <option value="">اختر نوع الاستقطاع</option>
                        @foreach($deductionTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('deduction_type_id', $employeeDeduction->deduction_type_id) == $type->id)>
                                {{ $type->name_ar }}
                                @if(!empty($type->code)) - {{ $type->code }} @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="hint">تتم إدارة هذه القائمة من صفحة أنواع الاستقطاعات.</div>
                    @error('deduction_type_id')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field full">
                    <label>عنوان الاستقطاع</label>
                    <input name="title" value="{{ old('title', $employeeDeduction->title) }}" placeholder="مثال: خصم تأخير شهر يونيو">
                    @error('title')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field full">
                    <label>طريقة الخصم</label>
                    @php $oldMode = old('deduction_mode', $employeeDeduction->deduction_mode ?: 'one_time'); @endphp
                    <div class="mode-cards">
                        <label class="mode-card"><input type="radio" name="deduction_mode" value="one_time" @checked($oldMode === 'one_time') onchange="toggleDeductionMode()"><strong>مرة واحدة</strong><span>يتم الخصم في شهر واحد فقط</span></label>
                        <label class="mode-card"><input type="radio" name="deduction_mode" value="monthly" @checked($oldMode === 'monthly') onchange="toggleDeductionMode()"><strong>كل شهر</strong><span>خصم ثابت من شهر بداية إلى شهر نهاية</span></label>
                        <label class="mode-card"><input type="radio" name="deduction_mode" value="selected_months" @checked($oldMode === 'selected_months') onchange="toggleDeductionMode()"><strong>أشهر محددة</strong><span>اختيار أشهر معينة فقط للخصم</span></label>
                        <label class="mode-card"><input type="radio" name="deduction_mode" value="installments" @checked($oldMode === 'installments') onchange="toggleDeductionMode()"><strong>أقساط</strong><span>تقسيم مبلغ إجمالي على عدد أقساط</span></label>
                        <label class="mode-card"><input type="radio" name="deduction_mode" value="percentage" @checked($oldMode === 'percentage') onchange="toggleDeductionMode()"><strong>نسبة</strong><span>خصم نسبة من إجمالي راتب الشهر</span></label>
                    </div>
                    @error('deduction_mode')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <input type="hidden" name="calculation_type" id="calculationType" value="{{ old('calculation_type', $employeeDeduction->calculation_type ?: 'fixed') }}">

                <div class="field dynamic-section" id="amountField">
                    <label id="amountLabel">المبلغ</label>
                    <input type="number" step="0.01" min="0" name="amount" id="amountInput" value="{{ old('amount', $employeeDeduction->amount) }}" required>
                    @error('amount')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field dynamic-section" id="percentageField">
                    <label>النسبة من الراتب %</label>
                    <input type="number" step="0.01" min="0" max="100" name="percentage" id="percentageInput" value="{{ old('percentage', $employeeDeduction->percentage) }}">
                    @error('percentage')<small class="error-text">{{ $message }}</small>@enderror
                    <div class="hint">في خصم النسبة يتم احتساب المبلغ أثناء مسير الرواتب حسب إجمالي راتب ذلك الشهر.</div>
                </div>

                <div class="field dynamic-section" id="monthlyAmountField">
                    <label>قيمة الخصم الشهري</label>
                    <input type="number" step="0.01" min="0" name="monthly_amount" id="monthlyAmountInput" value="{{ old('monthly_amount', $employeeDeduction->monthly_amount) }}">
                    @error('monthly_amount')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field dynamic-section" id="installmentsCountField">
                    <label>عدد الأقساط</label>
                    <input type="number" min="1" name="installments_count" id="installmentsCountInput" value="{{ old('installments_count', $employeeDeduction->installments_count) }}">
                    @error('installments_count')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field dynamic-section" id="startMonthField">
                    <label id="startMonthLabel">شهر بداية الخصم</label>
                    <input type="month" name="start_month" id="startMonthInput" value="{{ old('start_month', $employeeDeduction->start_month ?: optional($employeeDeduction->start_date)->format('Y-m')) }}">
                    @error('start_month')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field dynamic-section" id="endMonthField">
                    <label>شهر نهاية الخصم</label>
                    <input type="month" name="end_month" id="endMonthInput" value="{{ old('end_month', $employeeDeduction->end_month ?: optional($employeeDeduction->end_date)->format('Y-m')) }}">
                    @error('end_month')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field full dynamic-section" id="selectedMonthsField">
                    <label>الأشهر المحددة للخصم</label>
                    <div class="months-dropdown-wrap" id="monthsDropdownWrap">
                        <button type="button" class="months-dropdown-btn" id="monthsDropdownBtn" onclick="toggleMonthsDropdown()">
                            <span class="selected-text" id="monthsSelectedText">اختر الأشهر</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="months-dropdown-menu" id="monthsDropdownMenu">
                            <div class="months-search-box"><input type="text" id="monthsSearchInput" placeholder="ابحث عن شهر..." onkeyup="filterMonths()" autocomplete="off"></div>
                            <div class="months-actions">
                                <button type="button" class="tiny-btn" onclick="selectVisibleMonths()">تحديد الظاهر</button>
                                <button type="button" class="tiny-btn clear" onclick="clearSelectedMonths()">مسح التحديد</button>
                            </div>
                            <div class="months-list">
                                @php
                                    $oldSelectedMonths = old('selected_months', $employeeDeduction->selected_months ?: []);
                                    $monthStart = now()->startOfMonth();
                                @endphp
                                @for($i = 0; $i < 24; $i++)
                                    @php
                                        $monthValue = $monthStart->copy()->addMonths($i)->format('Y-m');
                                        $monthLabel = $monthStart->copy()->addMonths($i)->translatedFormat('F Y');
                                    @endphp
                                    <label class="month-check-row" data-search="{{ mb_strtolower($monthValue . ' ' . $monthLabel) }}">
                                        <input type="checkbox" name="selected_months[]" value="{{ $monthValue }}" data-name="{{ $monthLabel }} - {{ $monthValue }}" @checked(in_array($monthValue, $oldSelectedMonths)) onchange="updateSelectedMonthsText()">
                                        <span class="month-name">{{ $monthLabel }}</span>
                                        <span style="color:#6b7280;font-size:12px;font-weight:900">{{ $monthValue }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="selected-tags" id="selectedMonthsTags"></div>
                    @error('selected_months')<small class="error-text">{{ $message }}</small>@enderror
                    @error('selected_months.*')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field full"><div class="summary-box" id="deductionSummary">اختر طريقة الخصم لإظهار التفاصيل.</div></div>

                <input type="hidden" name="start_date" id="startDateHidden" value="{{ old('start_date', optional($employeeDeduction->start_date)->format('Y-m-d')) }}">
                <input type="hidden" name="end_date" id="endDateHidden" value="{{ old('end_date', optional($employeeDeduction->end_date)->format('Y-m-d')) }}">

                <div class="field full">
                    <label>السبب</label>
                    <textarea name="reason">{{ old('reason', $employeeDeduction->reason) }}</textarea>
                    @error('reason')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <div class="field full">
                    <label>ملاحظات</label>
                    <textarea name="notes">{{ old('notes', $employeeDeduction->notes) }}</textarea>
                    @error('notes')<small class="error-text">{{ $message }}</small>@enderror
                </div>
            </div>

            <div style="margin-top:18px;display:flex;gap:10px">
                <button class="btn2 primary" type="submit">تحديث الاستقطاع</button>
                <a class="btn2 soft" href="{{ route('employee-deductions.index') }}">رجوع</a>
            </div>
        </form>
    </div>

    <script>
        function deductionModeValue(){return document.querySelector('input[name="deduction_mode"]:checked')?.value || 'one_time';}
        function showField(id, show=true){const el=document.getElementById(id);if(!el)return;el.style.display=show?'block':'none';}
        function setRequired(id, required=true){const el=document.getElementById(id);if(!el)return;el.required=required;}
        function toggleDeductionMode(){
            const mode=deductionModeValue();
            const calculationType=document.getElementById('calculationType');
            const amountLabel=document.getElementById('amountLabel');
            const amountInput=document.getElementById('amountInput');
            ['amountField','percentageField','monthlyAmountField','installmentsCountField','startMonthField','endMonthField','selectedMonthsField'].forEach(id=>showField(id,false));
            ['amountInput','percentageInput','monthlyAmountInput','installmentsCountInput','startMonthInput','endMonthInput'].forEach(id=>setRequired(id,false));
            calculationType.value='fixed'; amountLabel.textContent='المبلغ'; amountInput.placeholder='';
            if(mode==='one_time'){showField('amountField',true);showField('startMonthField',true);setRequired('amountInput',true);setRequired('startMonthInput',true);document.getElementById('startMonthLabel').textContent='شهر الخصم';}
            if(mode==='monthly'){showField('monthlyAmountField',true);showField('startMonthField',true);showField('endMonthField',true);setRequired('monthlyAmountInput',true);setRequired('startMonthInput',true);setRequired('endMonthInput',true);document.getElementById('startMonthLabel').textContent='شهر بداية الخصم';amountInput.value=document.getElementById('monthlyAmountInput').value || amountInput.value || '';}
            if(mode==='selected_months'){showField('amountField',true);showField('selectedMonthsField',true);setRequired('amountInput',true);document.getElementById('startMonthLabel').textContent='شهر بداية الخصم';}
            if(mode==='installments'){showField('amountField',true);showField('installmentsCountField',true);showField('monthlyAmountField',true);showField('startMonthField',true);setRequired('amountInput',true);setRequired('installmentsCountInput',true);setRequired('startMonthInput',true);amountLabel.textContent='إجمالي مبلغ الاستقطاع';document.getElementById('startMonthLabel').textContent='شهر بداية أول قسط';}
            if(mode==='percentage'){showField('percentageField',true);showField('startMonthField',true);setRequired('percentageInput',true);setRequired('startMonthInput',true);calculationType.value='percentage';amountInput.required=false;}
            syncLegacyDates();updateSelectedMonthsText();updateSummary();
        }
        function syncLegacyDates(){const startMonth=document.getElementById('startMonthInput')?.value;const endMonth=document.getElementById('endMonthInput')?.value;document.getElementById('startDateHidden').value=startMonth?startMonth+'-01':'';if(endMonth){const parts=endMonth.split('-');const lastDay=new Date(parseInt(parts[0]),parseInt(parts[1]),0).getDate();document.getElementById('endDateHidden').value=endMonth+'-'+String(lastDay).padStart(2,'0');}else{document.getElementById('endDateHidden').value='';}}
        function updateSummary(){const mode=deductionModeValue();const summary=document.getElementById('deductionSummary');const amount=document.getElementById('amountInput')?.value||0;const monthly=document.getElementById('monthlyAmountInput')?.value||0;const percentage=document.getElementById('percentageInput')?.value||0;const installments=document.getElementById('installmentsCountInput')?.value||0;const startMonth=document.getElementById('startMonthInput')?.value||'-';const endMonth=document.getElementById('endMonthInput')?.value||'-';const selectedMonthsCount=selectedMonthCheckboxes().length;if(mode==='one_time'){summary.textContent='سيتم خصم مبلغ '+amount+' مرة واحدة في شهر '+startMonth+'.';}if(mode==='monthly'){summary.textContent='سيتم خصم مبلغ '+monthly+' شهريًا من '+startMonth+' إلى '+endMonth+'.';}if(mode==='selected_months'){summary.textContent='سيتم خصم مبلغ '+amount+' في الأشهر المحددة فقط. عدد الأشهر المختارة: '+selectedMonthsCount+'.';}if(mode==='installments'){summary.textContent='سيتم تقسيم مبلغ '+amount+' على '+installments+' قسط/أقساط بداية من شهر '+startMonth+'. قيمة القسط يمكن تعديلها أو تركها فارغة ليحسبها النظام تلقائيًا.';}if(mode==='percentage'){summary.textContent='سيتم خصم نسبة '+percentage+'% من إجمالي راتب الموظف في شهر '+startMonth+'.';}}
        function toggleMonthsDropdown(){const menu=document.getElementById('monthsDropdownMenu');const btn=document.getElementById('monthsDropdownBtn');menu.classList.toggle('show');btn.classList.toggle('active');if(menu.classList.contains('show')){setTimeout(()=>document.getElementById('monthsSearchInput').focus(),50);}}
        function filterMonths(){const keyword=document.getElementById('monthsSearchInput').value.trim().toLowerCase();document.querySelectorAll('.month-check-row').forEach(row=>{const searchText=row.getAttribute('data-search')||'';row.style.display=searchText.includes(keyword)?'flex':'none';});}
        function selectedMonthCheckboxes(){return Array.from(document.querySelectorAll('input[name="selected_months[]"]:checked'));}
        function updateSelectedMonthsText(){const selected=selectedMonthCheckboxes();const textBox=document.getElementById('monthsSelectedText');const tagsBox=document.getElementById('selectedMonthsTags');tagsBox.innerHTML='';if(selected.length===0){textBox.textContent='اختر الأشهر';updateSummary();return;}textBox.textContent=selected.length===1?selected[0].dataset.name:'تم اختيار '+selected.length+' أشهر';selected.forEach(item=>{const tag=document.createElement('span');tag.className='selected-tag';tag.innerHTML='<i class="fas fa-check"></i> '+item.dataset.name;tagsBox.appendChild(tag);});updateSummary();}
        function selectVisibleMonths(){Array.from(document.querySelectorAll('.month-check-row')).filter(row=>row.style.display!=='none').forEach(row=>{const checkbox=row.querySelector('input[type="checkbox"]');if(checkbox)checkbox.checked=true;});updateSelectedMonthsText();}
        function clearSelectedMonths(){document.querySelectorAll('input[name="selected_months[]"]').forEach(item=>{item.checked=false;});updateSelectedMonthsText();}
        document.addEventListener('click',function(event){const wrap=document.getElementById('monthsDropdownWrap');const menu=document.getElementById('monthsDropdownMenu');const btn=document.getElementById('monthsDropdownBtn');if(wrap && !wrap.contains(event.target)){menu.classList.remove('show');btn.classList.remove('active');}});
        document.addEventListener('input',function(event){if(['amountInput','percentageInput','monthlyAmountInput','installmentsCountInput','startMonthInput','endMonthInput'].includes(event.target.id)){if(event.target.id==='percentageInput' && deductionModeValue()==='percentage'){document.getElementById('amountInput').value=event.target.value;}if(event.target.id==='monthlyAmountInput' && deductionModeValue()==='monthly'){document.getElementById('amountInput').value=event.target.value;}syncLegacyDates();updateSummary();}});
        document.addEventListener('DOMContentLoaded',function(){if(deductionModeValue()==='percentage'){document.getElementById('amountInput').value=document.getElementById('percentageInput').value || '{{ old('amount', $employeeDeduction->amount) }}';}toggleDeductionMode();filterMonths();updateSelectedMonthsText();});
        document.querySelector('form').addEventListener('submit',function(){const mode=deductionModeValue();const amountInput=document.getElementById('amountInput');const monthlyAmountInput=document.getElementById('monthlyAmountInput');const percentageInput=document.getElementById('percentageInput');if(mode==='monthly'){amountInput.value=monthlyAmountInput.value || 0;document.getElementById('calculationType').value='fixed';}if(mode==='percentage'){const percentage=percentageInput.value || 0;amountInput.value=percentage;document.getElementById('calculationType').value='percentage';}syncLegacyDates();});
    </script>
@endsection
