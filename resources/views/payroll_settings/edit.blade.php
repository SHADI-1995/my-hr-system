@extends('layouts.hr')

@section('title', 'إعدادات الرواتب')
@section('page-title', 'إعدادات الرواتب')

@section('content')
    <style>
        .settings-page{max-width:1100px;margin:0 auto}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20)}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}
        .hero p{margin:0;font-weight:700;opacity:.9;line-height:1.8}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:22px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
        .field label{display:block;color:#4c3b91;font-weight:900;margin-bottom:8px}
        .field select,.field input{width:100%;height:46px;border:1px solid #ddd6fe;border-radius:14px;padding:0 13px;font-weight:900;outline:none;background:#fff;color:#111827}
        .field select:focus,.field input:focus{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.10)}
        .hint{margin-top:8px;color:#6b7280;font-size:12px;font-weight:800;line-height:1.8}
        .switch-list{display:grid;gap:12px}
        .switch-row{border:1px solid #eeeafc;background:#faf9ff;border-radius:16px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:14px}
        .switch-row strong{display:block;color:#3b2b80;font-size:14px;margin-bottom:4px}
        .switch-row small{color:#6b7280;font-weight:800;line-height:1.6}
        .switch-row input{width:22px;height:22px;accent-color:#6d5bd0}
        .btn2{border:0;border-radius:13px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}
        .alert-success{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0;border-radius:16px;padding:14px;font-weight:900;margin-bottom:18px}
        .alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:16px;padding:14px;font-weight:900;margin-bottom:18px}
        @media(max-width:800px){.grid{grid-template-columns:1fr}.hero h1{font-size:22px}}
    </style>

    <div class="settings-page">
        <div class="hero">
            <h1>إعدادات الرواتب</h1>
            <p>تحكم في طريقة احتساب أيام الراتب، طريقة الصرف الافتراضية، الخصومات التلقائية، وطريقة تقريب المبالغ.</p>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <strong>يوجد أخطاء في البيانات:</strong>
                <ul style="margin:8px 0 0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('payroll-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="grid">
                    <div class="field">
                        <label>طريقة احتساب أيام الراتب</label>
                        <select name="salary_day_calculation" required>
                            <option value="fixed_30_days" @selected(old('salary_day_calculation', $setting->salary_day_calculation) === 'fixed_30_days')>30 يوم ثابت</option>
                            <option value="actual_month_days" @selected(old('salary_day_calculation', $setting->salary_day_calculation) === 'actual_month_days')>أيام الشهر الفعلية 28 / 29 / 30 / 31</option>
                            <option value="working_days" @selected(old('salary_day_calculation', $setting->salary_day_calculation) === 'working_days')>أيام العمل فقط</option>
                        </select>
                        <div class="hint">هذا الخيار سيتم استخدامه لاحقًا داخل احتساب مسير الرواتب.</div>
                    </div>

                    <div class="field">
                        <label>طريقة الصرف الافتراضية</label>
                        <select name="default_payment_method" required>
                            <option value="bank_transfer" @selected(old('default_payment_method', $setting->default_payment_method) === 'bank_transfer')>تحويل بنكي</option>
                            <option value="cash" @selected(old('default_payment_method', $setting->default_payment_method) === 'cash')>نقدي</option>
                            <option value="cheque" @selected(old('default_payment_method', $setting->default_payment_method) === 'cheque')>شيك</option>
                            <option value="other" @selected(old('default_payment_method', $setting->default_payment_method) === 'other')>أخرى</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>عدد الكسور العشرية</label>
                        <input type="number" min="0" max="4" name="rounding_decimals" value="{{ old('rounding_decimals', $setting->rounding_decimals) }}" required>
                        <div class="hint">مثال: 2 تعني عرض وحفظ المبالغ مثل 1450.25</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="switch-list">
                    <label class="switch-row">
                        <span><strong>السماح بصافي راتب سالب</strong><small>إذا كانت الاستقطاعات أكبر من الراتب.</small></span>
                        <input type="checkbox" name="allow_negative_net_salary" value="1" @checked(old('allow_negative_net_salary', $setting->allow_negative_net_salary))>
                    </label>

                    <label class="switch-row">
                        <span><strong>خصم السلف المعتمدة تلقائيًا</strong><small>يدخل أقساط السلف المستحقة تلقائيًا عند الاحتساب.</small></span>
                        <input type="checkbox" name="auto_deduct_approved_advances" value="1" @checked(old('auto_deduct_approved_advances', $setting->auto_deduct_approved_advances))>
                    </label>

                    <label class="switch-row">
                        <span><strong>خصم الإجازات غير المدفوعة تلقائيًا</strong><small>يخصم الإجازات التي تؤثر على الراتب.</small></span>
                        <input type="checkbox" name="auto_deduct_unpaid_leaves" value="1" @checked(old('auto_deduct_unpaid_leaves', $setting->auto_deduct_unpaid_leaves))>
                    </label>

                    <label class="switch-row">
                        <span><strong>خصم الإيقافات تلقائيًا</strong><small>يخصم أيام الإيقاف حسب نسبة الراتب أثناء الإيقاف.</small></span>
                        <input type="checkbox" name="auto_deduct_suspensions" value="1" @checked(old('auto_deduct_suspensions', $setting->auto_deduct_suspensions))>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button class="btn2 primary" type="submit"><i class="fas fa-save"></i> حفظ الإعدادات</button>
                <a class="btn2 soft" href="{{ route('payroll-periods.index') }}">رجوع لمسير الرواتب</a>
            </div>
        </form>
    </div>
@endsection

