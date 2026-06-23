@extends('layouts.hr')

@section('title', 'كشف تحويل الرواتب')
@section('page-title', 'كشف تحويل الرواتب')

@section('content')
    <style>
        .page{max-width:100%;overflow-x:hidden}
        .hero{background:linear-gradient(135deg,#4c3b91,#7c3aed);color:#fff;border-radius:24px;padding:26px;margin-bottom:18px;box-shadow:0 20px 45px rgba(76,59,145,.20);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        .hero h1{margin:0 0 8px;font-size:28px;font-weight:900}.hero p{margin:0;font-weight:700;opacity:.9}
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .filters{display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end}
        .field label{display:block;margin-bottom:7px;color:#4c3b91;font-size:12px;font-weight:900}
        .field select{width:100%;height:44px;border:1px solid #ddd6fe;border-radius:14px;padding:0 12px;font-size:12px;font-weight:800;outline:none;background:#fff}
        .actions{display:flex;gap:8px;flex-wrap:wrap}
        .btn{height:44px;border:0;border-radius:14px;padding:0 16px;font-size:12px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer}
        .primary{background:#6d5bd0;color:#fff}.soft{background:#ede9fe;color:#4c3b91}.green{background:#16a34a;color:#fff}.dark{background:#111827;color:#fff}.orange{background:#f59e0b;color:#fff}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px}
        .stat{background:#faf9ff;border:1px solid #eeeafc;border-radius:18px;padding:16px}
        .stat span{display:block;color:#6b7280;font-size:11px;font-weight:800;margin-bottom:8px}
        .stat strong{display:block;color:#4c3b91;font-size:18px;font-weight:900}
        table{width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}
        th{background:#f1edff;color:#4c3b91;font-size:10px;font-weight:900;padding:9px 5px;text-align:center}
        td{border-top:1px solid #f1eefb;padding:9px 5px;font-size:10px;font-weight:800;text-align:center;word-break:break-word}
        .amount{direction:ltr;font-weight:900;color:#166534}
        .missing{background:#fee2e2;color:#991b1b;border-radius:999px;padding:4px 8px;font-size:9px;font-weight:900}
        .ok{background:#dcfce7;color:#166534;border-radius:999px;padding:4px 8px;font-size:9px;font-weight:900}
        .warning-box{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:18px;padding:14px;margin-bottom:14px;font-size:12px;font-weight:900;line-height:1.8}
        .empty{text-align:center;color:#6b7280;font-weight:900;padding:30px}
        @media(max-width:900px){.filters{grid-template-columns:1fr}.stats{grid-template-columns:1fr 1fr}}
        @media(max-width:600px){.stats{grid-template-columns:1fr}.hero h1{font-size:22px}}
    </style>

    <div class="page">
        <div class="hero">
            <div>
                <h1>كشف تحويل الرواتب</h1>
                <p>تجهيز ملف تحويل الرواتب للبنك من المسيرات المعتمدة أو المدفوعة.</p>
            </div>

            <a href="{{ route('payroll-periods.index') }}" class="btn soft">
                <i class="fas fa-arrow-right"></i>
                العودة للمسيرات
            </a>
        </div>

        <div class="card">
            <form method="GET" action="{{ route('payroll-bank-transfers.index') }}">
                <div class="filters">
                    <div class="field">
                        <label>اختر مسير الرواتب</label>
                        <select name="payroll_period_id" required>
                            <option value="">اختر المسير</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected((string) request('payroll_period_id') === (string) $period->id)>
                                    {{ $period->period_number }} - {{ $period->month }} - {{ $period->status_text ?? $period->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="actions">
                        <button class="btn primary" type="submit">
                            <i class="fas fa-eye"></i>
                            عرض الكشف
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($selectedPeriod)
            @php
                $totalNet = (float) $items->sum('net_salary');
                $missingIban = $items->filter(fn($item) => empty($item->employee?->iban))->count();
                $missingBank = $items->filter(fn($item) => empty($item->employee?->bank_name))->count();
                $missingTotal = $missingIban + $missingBank;
            @endphp

            <div class="stats">
                <div class="stat">
                    <span>رقم المسير</span>
                    <strong>{{ $selectedPeriod->period_number }}</strong>
                </div>
                <div class="stat">
                    <span>عدد الموظفين</span>
                    <strong>{{ $items->count() }}</strong>
                </div>
                <div class="stat">
                    <span>إجمالي صافي التحويل</span>
                    <strong>{{ number_format($totalNet, 2) }}</strong>
                </div>
                <div class="stat">
                    <span>نواقص بيانات بنكية</span>
                    <strong>{{ $missingTotal }}</strong>
                </div>
            </div>

            @if($missingTotal > 0)
                <div class="warning-box">
                    يوجد موظفون لديهم نقص في اسم البنك أو الآيبان. ملف CSV سيصدرهم بحالة MISSING_BANK_DATA حتى تتم مراجعة البيانات قبل رفع الملف للبنك.
                </div>
            @endif

            <div class="card">
                <div class="actions" style="margin-bottom:14px">
                    @if(auth()->user()->hasPermission('payroll_bank_transfers.export'))
                        <a href="{{ route('payroll-bank-transfers.export-excel', $selectedPeriod) }}" class="btn green">
                            <i class="fas fa-file-excel"></i>
                            Excel
                        </a>

                        <a href="{{ route('payroll-bank-transfers.export-csv', $selectedPeriod) }}" class="btn orange">
                            <i class="fas fa-file-csv"></i>
                            CSV البنك
                        </a>

                        <a href="{{ route('payroll-bank-transfers.print-pdf', $selectedPeriod) }}" class="btn dark" target="_blank">
                            <i class="fas fa-print"></i>
                            PDF
                        </a>
                    @endif
                </div>

                <table>
                    <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>الرقم الوظيفي</th>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>البنك</th>
                        <th>IBAN</th>
                        <th>طريقة الصرف</th>
                        <th>صافي الراتب</th>
                        <th>التحقق</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $index => $item)
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
                            <td>
                                @if($isReady)
                                    <span class="ok">جاهز</span>
                                @else
                                    <span class="missing">ناقص بيانات</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">لا توجد رواتب صافية للتحويل في هذا المسير.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
