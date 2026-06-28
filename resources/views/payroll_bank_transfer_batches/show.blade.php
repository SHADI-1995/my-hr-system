@extends('layouts.hr')

@section('title', 'تفاصيل دفعة تحويل الرواتب')
@section('page-title', 'تفاصيل دفعة تحويل الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}.hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}.card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .actions{display:flex;gap:8px;flex-wrap:wrap}.btn{height:40px;border:0;border-radius:12px;padding:0 14px;font-size:11px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}.soft{background:#ede9fe;color:#4c3b91}.green{background:#16a34a;color:#fff}.orange{background:#f59e0b;color:#fff}.dark{background:#111827;color:#fff}.red{background:#dc2626;color:#fff}.primary{background:#6d5bd0;color:#fff}
        .stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:18px}.stat{background:#faf9ff;border:1px solid #eeeafc;border-radius:18px;padding:16px}.stat span{display:block;color:#6b7280;font-size:11px;font-weight:800;margin-bottom:8px}.stat strong{display:block;color:#4c3b91;font-size:17px;font-weight:900}
        .field label{display:block;margin-bottom:7px;color:#4c3b91;font-size:12px;font-weight:900}.field input{width:100%;height:42px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-size:12px;font-weight:800;outline:none;background:#fff}.confirm-grid{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}.amount{direction:ltr;font-weight:900;color:#166534}.missing{background:#fee2e2;color:#991b1b;border-radius:999px;padding:4px 8px;font-size:9px;font-weight:900}.ok{background:#dcfce7;color:#166534;border-radius:999px;padding:4px 8px;font-size:9px;font-weight:900}.pill{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:900}.generated{background:#dbeafe;color:#1d4ed8}.sent{background:#fef3c7;color:#92400e}.confirmed{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}
        @media(max-width:1000px){.stats{grid-template-columns:1fr 1fr}.confirm-grid{grid-template-columns:1fr}}@media(max-width:600px){.stats{grid-template-columns:1fr}}
    </style>

    <div class="page">
        <div class="hero">
            <div>
                <h1>دفعة تحويل: {{ $batch->batch_number }}</h1>
                <p>المسير: {{ $batch->payrollPeriod?->period_number }} | الشهر: {{ $batch->payrollPeriod?->month }}</p>
            </div>

            <div class="actions">
                <a href="{{ route('payroll-bank-transfer-batches.index') }}" class="btn soft">العودة للدفعات</a>

                @if(auth()->user()->hasPermission('payroll_bank_transfer_batches.export'))
                    <a href="{{ route('payroll-bank-transfer-batches.export-excel', $batch) }}" class="btn green">Excel</a>
                    <a href="{{ route('payroll-bank-transfer-batches.export-csv', $batch) }}" class="btn orange">CSV البنك</a>
                    <a href="{{ route('payroll-bank-transfer-batches.print-pdf', $batch) }}" class="btn dark" target="_blank">PDF</a>
                @endif
            </div>
        </div>

        <div class="stats">
            <div class="stat"><span>الحالة</span><strong><span class="pill {{ $batch->status_badge_class }}">{{ $batch->status_text }}</span></strong></div>
            <div class="stat"><span>عدد الموظفين</span><strong>{{ $batch->employees_count }}</strong></div>
            <div class="stat"><span>إجمالي التحويل</span><strong>{{ number_format((float) $batch->total_amount, 2) }}</strong></div>
            <div class="stat"><span>نواقص بنكية</span><strong>{{ $batch->missing_bank_data_count }}</strong></div>
            <div class="stat"><span>تاريخ التجهيز</span><strong>{{ optional($batch->generated_at)->format('Y-m-d') ?? '-' }}</strong></div>
        </div>

        @if($batch->status === 'confirmed')
            <div class="card">
                <h3 style="color:#4c3b91;margin:0 0 14px;font-weight:900">بيانات تأكيد البنك</h3>
                <div class="stats" style="margin-bottom:0">
                    <div class="stat"><span>مرجع البنك</span><strong>{{ $batch->bank_reference ?: '-' }}</strong></div>
                    <div class="stat"><span>تاريخ التحويل</span><strong>{{ optional($batch->bank_transfer_date)->format('Y-m-d') ?? '-' }}</strong></div>
                    <div class="stat"><span>تم التأكيد بواسطة</span><strong>{{ $batch->confirmedBy?->name ?? '-' }}</strong></div>
                    <div class="stat"><span>وقت التأكيد</span><strong>{{ optional($batch->confirmed_at)->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    <div class="stat">
                        <span>ملف الإثبات</span>
                        <strong>
                            @if($batch->confirmation_file_url)
                                <a href="{{ $batch->confirmation_file_url }}" target="_blank" style="color:#4c3b91">فتح الملف</a>
                            @else
                                -
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="actions" style="margin-bottom:14px">
                @if($batch->can_send && auth()->user()->hasPermission('payroll_bank_transfer_batches.send'))
                    <form method="POST" action="{{ route('payroll-bank-transfer-batches.mark-sent', $batch) }}">
                        @csrf
                        <button class="btn orange" onclick="return confirm('تأكيد تسجيل إرسال الدفعة للبنك؟')">تسجيل الإرسال للبنك</button>
                    </form>
                @endif

                @if($batch->can_cancel && auth()->user()->hasPermission('payroll_bank_transfer_batches.cancel'))
                    <form method="POST" action="{{ route('payroll-bank-transfer-batches.cancel', $batch) }}">
                        @csrf
                        <button class="btn red" onclick="return confirm('هل تريد إلغاء هذه الدفعة؟')">إلغاء الدفعة</button>
                    </form>
                @endif
            </div>

            @if($batch->can_confirm && auth()->user()->hasPermission('payroll_bank_transfer_batches.confirm'))
                <form method="POST" action="{{ route('payroll-bank-transfer-batches.confirm', $batch) }}" enctype="multipart/form-data" style="margin-bottom:18px">
                    @csrf

                    <div class="confirm-grid">
                        <div class="field">
                            <label>رقم مرجع البنك</label>
                            <input type="text" name="bank_reference" placeholder="مثال: TRX-123456">
                        </div>

                        <div class="field">
                            <label>تاريخ التحويل</label>
                            <input type="date" name="bank_transfer_date" value="{{ now()->toDateString() }}">
                        </div>

                        <div class="field">
                            <label>إرفاق إثبات التحويل PDF / صورة</label>
                            <input type="file" name="confirmation_file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <button class="btn green" onclick="return confirm('تأكيد أن البنك نفذ التحويل؟')">تأكيد التحويل</button>
                    </div>
                </form>
            @endif

            <table>
                <thead>
                <tr>
                    <th>#</th><th>الرقم الوظيفي</th><th>الموظف</th><th>القسم</th><th>البنك</th><th>IBAN</th><th>طريقة الصرف</th><th>صافي الراتب</th><th>التحقق</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $index => $item)
                    @php
                        $employee = $item->employee;
                        $bankName = $employee?->bank_name ?: '-';
                        $iban = $employee?->iban ?: '-';
                        $method = $item->salary_payment_method_name
                            ?? $employee?->salaryPaymentMethod?->name_ar
                            ?? $employee?->salary_payment_method
                            ?? '-';
                        $isReady = !empty($employee?->bank_name) && !empty($employee?->iban);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->employee_number }}</td>
                        <td>{{ $item->employee_name }}</td>
                        <td>{{ $item->employee_department ?? $employee?->department?->name ?? '-' }}</td>
                        <td>{{ $bankName }}</td>
                        <td dir="ltr">{{ $iban }}</td>
                        <td>{{ $method }}</td>
                        <td class="amount">{{ number_format((float) $item->net_salary, 2) }}</td>
                        <td>@if($isReady)<span class="ok">جاهز</span>@else<span class="missing">ناقص بيانات</span>@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
