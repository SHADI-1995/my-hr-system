@extends('layouts.hr')

@section('title', 'دفعات تحويل الرواتب')
@section('page-title', 'دفعات تحويل الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}.hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}.card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .grid{display:grid;grid-template-columns:2fr 1fr;gap:14px;align-items:start}.field label{display:block;margin-bottom:7px;color:#4c3b91;font-size:12px;font-weight:900}.field select,.field textarea{width:100%;border:1px solid #ddd6fe;border-radius:14px;padding:10px 12px;font-size:12px;font-weight:800;outline:none;background:#fff}.field select{height:44px}.field textarea{min-height:80px;resize:vertical}
        .actions{display:flex;gap:8px;flex-wrap:wrap}.btn{height:38px;border:0;border-radius:12px;padding:0 13px;font-size:11px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}.primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.green{background:#16a34a;color:#fff}.orange{background:#f59e0b;color:#fff}.red{background:#dc2626;color:#fff}.dark{background:#111827;color:#fff}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word;vertical-align:middle}
        .pill{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:900;white-space:nowrap}.generated{background:#dbeafe;color:#1d4ed8}.sent{background:#fef3c7;color:#92400e}.confirmed{background:#dcfce7;color:#166534}.cancelled{background:#fee2e2;color:#991b1b}.amount{direction:ltr;font-weight:900;color:#166534}.warn{color:#991b1b;font-weight:900}.empty{text-align:center;color:#6b7280;font-weight:900;padding:28px}
        @media(max-width:1000px){.grid{grid-template-columns:1fr}}
    </style>

    <div class="page">
        <div class="hero">
            <div>
                <h1>دفعات تحويل الرواتب</h1>
                <p>متابعة حالة ملف التحويل بعد التجهيز: تم الإرسال للبنك، تم التأكيد، أو إلغاء الدفعة.</p>
            </div>

            <a href="{{ route('payroll-bank-transfers.index') }}" class="btn soft">
                <i class="fas fa-building-columns"></i>
                كشف التحويل
            </a>
        </div>

        <div class="grid">
            <div class="card">
                <h3 style="color:#4c3b91;margin:0 0 14px;font-weight:900">إنشاء دفعة تحويل جديدة</h3>

                <form method="POST" action="{{ route('payroll-bank-transfer-batches.store') }}">
                    @csrf

                    <div class="field">
                        <label>مسير الرواتب</label>
                        <select name="payroll_period_id" required>
                            <option value="">اختر مسير معتمد أو مدفوع</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}">
                                    {{ $period->period_number }} - {{ $period->month }} - {{ $period->status_text ?? $period->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field" style="margin-top:12px">
                        <label>ملاحظات</label>
                        <textarea name="notes" placeholder="اختياري"></textarea>
                    </div>

                    <div class="actions" style="margin-top:14px">
                        <button class="btn primary">
                            <i class="fas fa-plus"></i>
                            إنشاء دفعة
                        </button>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3 style="color:#4c3b91;margin:0 0 14px;font-weight:900">مراحل الدفعة</h3>
                <div style="font-size:12px;font-weight:900;line-height:2;color:#374151">
                    <div>1. تم التجهيز</div>
                    <div>2. تم الإرسال للبنك</div>
                    <div>3. تم تأكيد التحويل</div>
                    <div>أو إلغاء الدفعة قبل التأكيد</div>
                </div>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                <tr>
                    <th>رقم الدفعة</th><th>المسير</th><th>الشهر</th><th>الحالة</th><th>عدد الموظفين</th><th>إجمالي التحويل</th><th>نواقص بنكية</th><th>أنشئت بواسطة</th><th style="width:22%">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td>{{ $batch->batch_number }}</td>
                        <td>{{ $batch->payrollPeriod?->period_number ?? '-' }}</td>
                        <td>{{ $batch->payrollPeriod?->month ?? '-' }}</td>
                        <td><span class="pill {{ $batch->status_badge_class }}">{{ $batch->status_text }}</span></td>
                        <td>{{ $batch->employees_count }}</td>
                        <td class="amount">{{ number_format((float) $batch->total_amount, 2) }}</td>
                        <td>@if($batch->missing_bank_data_count > 0)<span class="warn">{{ $batch->missing_bank_data_count }}</span>@else 0 @endif</td>
                        <td>{{ $batch->generatedBy?->name ?? '-' }}</td>
                        <td>
                            <div class="actions" style="justify-content:center">
                                <a href="{{ route('payroll-bank-transfer-batches.show', $batch) }}" class="btn soft">عرض</a>

                                @if($batch->can_send && auth()->user()->hasPermission('payroll_bank_transfer_batches.send'))
                                    <form method="POST" action="{{ route('payroll-bank-transfer-batches.mark-sent', $batch) }}">
                                        @csrf
                                        <button class="btn orange" onclick="return confirm('تأكيد تسجيل إرسال الدفعة للبنك؟')">إرسال</button>
                                    </form>
                                @endif

                                @if($batch->can_confirm && auth()->user()->hasPermission('payroll_bank_transfer_batches.confirm'))
                                    <form method="POST" action="{{ route('payroll-bank-transfer-batches.confirm', $batch) }}">
                                        @csrf
                                        <button class="btn green" onclick="return confirm('تأكيد أن البنك نفذ التحويل؟')">تأكيد</button>
                                    </form>
                                @endif

                                @if($batch->can_cancel && auth()->user()->hasPermission('payroll_bank_transfer_batches.cancel'))
                                    <form method="POST" action="{{ route('payroll-bank-transfer-batches.cancel', $batch) }}">
                                        @csrf
                                        <button class="btn red" onclick="return confirm('هل تريد إلغاء هذه الدفعة؟')">إلغاء</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty">لا توجد دفعات تحويل حتى الآن.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div style="margin-top:14px">{{ $batches->links() }}</div>
        </div>
    </div>
@endsection
