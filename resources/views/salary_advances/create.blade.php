@extends('layouts.hr')

@section('title', 'إضافة سلفة')
@section('page-title', 'إضافة سلفة')

@section('content')
    <style>
        .form-card,.card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07);margin-bottom:18px}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:7px}.field input,.field select,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:11px 12px;font-weight:800}.field textarea{min-height:90px}.full{grid-column:1/-1}.btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.green{background:#16a34a;color:#fff}.red{background:#dc2626;color:#fff}.muted{font-size:12px;color:#6b7280;margin-top:6px;font-weight:700}.months-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px}.month-card{border:1px solid #ddd6fe;border-radius:16px;padding:12px;background:#f8f6ff;display:flex;align-items:center;gap:8px;font-weight:900;color:#4c3b91;cursor:pointer}.month-card input{width:auto}.month-card.selected{background:#ede9fe;border-color:#7c3aed}.summary{background:#f8f6ff;border:1px dashed #7c3aed;border-radius:16px;padding:12px;color:#4c3b91;font-weight:900}@media(max-width:900px){.grid{grid-template-columns:1fr}.months-grid{grid-template-columns:1fr 1fr}}@media(max-width:520px){.months-grid{grid-template-columns:1fr}}
    </style>
    <div class="form-card">
        <form method="POST" action="{{ route('salary-advances.store') }}">
            @csrf
            <div class="grid">
                <div class="field"><label>الموظف</label><select name="employee_id" required><option value="">اختر الموظف</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected(old('employee_id')==$employee->id)>{{ $employee->display_name }} - {{ $employee->employee_number }}</option>@endforeach</select>@error('employee_id')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>مبلغ السلفة</label><input id="amount" type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required>@error('amount')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>عدد الأقساط</label><input id="count" type="number" min="1" max="60" name="installments_count" value="{{ old('installments_count', 1) }}" required><div class="muted">عدد الأشهر التي ستختارها بالأسفل يجب أن يساوي عدد الأقساط.</div>@error('installments_count')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>كم يخصم كل شهر؟</label><input id="monthly" type="number" step="0.01" min="0" name="installment_amount" value="{{ old('installment_amount') }}" placeholder="اتركه فارغًا للحساب التلقائي"><div class="muted">إذا تركته فارغًا: النظام يحسب مبلغ السلفة ÷ عدد الأقساط.</div>@error('installment_amount')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>بداية عرض الأشهر</label><input id="deductionStart" type="date" name="deduction_start_date" value="{{ old('deduction_start_date', now()->startOfMonth()->addMonth()->toDateString()) }}" required>@error('deduction_start_date')<small style="color:red">{{ $message }}</small>@enderror</div>
                <div class="field"><label>القسط الأخير المتوقع</label><input id="lastAmount" type="text" readonly></div>

                <div class="field full">
                    <label>اختر أشهر خصم الأقساط</label>
                    <div id="selectedSummary" class="summary"></div>
                    @error('installment_months')<small style="color:red">{{ $message }}</small>@enderror
                    <div id="monthsContainer" class="months-grid"></div>
                    <div class="muted">مثال: تقدر تختار يناير، مارس، يونيو فقط، والنظام يخصم في هذه الأشهر المختارة.</div>
                </div>

                <div class="field full"><label>سبب السلفة</label><textarea name="reason">{{ old('reason') }}</textarea></div>
                <div class="field full"><label>ملاحظات</label><textarea name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div style="margin-top:18px;display:flex;gap:10px">
                <button class="btn2 primary">حفظ السلفة وجدولة الأشهر المختارة</button>
                <a class="btn2 soft" href="{{ route('salary-advances.index') }}">رجوع</a>
            </div>
        </form>
    </div>
    <script>
        const oldSelected = @json(old('installment_months', []));
        function pad(n){ return n < 10 ? '0' + n : '' + n; }
        function monthName(date){
            return date.toLocaleDateString('ar-SA', {year:'numeric', month:'long'});
        }
        function formatValue(date){
            return date.getFullYear() + '-' + pad(date.getMonth()+1) + '-01';
        }
        function generateMonths(){
            const container = document.getElementById('monthsContainer');
            const startInput = document.getElementById('deductionStart');
            const start = startInput.value ? new Date(startInput.value + 'T00:00:00') : new Date();
            const selectedSet = new Set(oldSelected);
            container.innerHTML = '';

            for(let i=0; i<24; i++){
                const d = new Date(start.getFullYear(), start.getMonth()+i, 1);
                const value = formatValue(d);
                const label = document.createElement('label');
                label.className = 'month-card';
                label.innerHTML = `<input type="checkbox" name="installment_months[]" value="${value}"> <span>${monthName(d)}</span>`;
                const input = label.querySelector('input');
                if(selectedSet.has(value)){
                    input.checked = true;
                    label.classList.add('selected');
                }
                input.addEventListener('change', function(){
                    label.classList.toggle('selected', input.checked);
                    updateSummary();
                });
                container.appendChild(label);
            }
            updateSummary();
        }
        function calcInstallment(){
            const amount = parseFloat(document.getElementById('amount').value || 0);
            const count = parseInt(document.getElementById('count').value || 1);
            const monthlyInput = document.getElementById('monthly');
            let monthly = parseFloat(monthlyInput.value || 0);
            if(amount > 0 && count > 0){
                if(!monthly || monthly <= 0){
                    monthly = Math.round((amount / count) * 100) / 100;
                }
                const last = Math.round((amount - (monthly * (count - 1))) * 100) / 100;
                document.getElementById('lastAmount').value = last > 0 ? last.toFixed(2) : 'قيمة القسط الشهري كبيرة جدًا';
            }
            updateSummary();
        }
        function updateSummary(){
            const count = parseInt(document.getElementById('count').value || 0);
            const selected = document.querySelectorAll('input[name="installment_months[]"]:checked').length;
            document.getElementById('selectedSummary').innerText = `المطلوب اختيار ${count} شهر / المختار الآن ${selected} شهر`;
        }
        ['amount','count','monthly'].forEach(id => document.getElementById(id).addEventListener('input', calcInstallment));
        document.getElementById('deductionStart').addEventListener('change', generateMonths);
        generateMonths();
        calcInstallment();
    </script>
@endsection
