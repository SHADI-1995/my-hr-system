@extends('layouts.hr')

@section('title', 'تفاصيل السلفة')
@section('page-title', 'تفاصيل السلفة')

@section('content')
    <style>
        .card{background:#fff;border:1px solid #eeeafc;border-radius:22px;padding:20px;margin-bottom:18px;box-shadow:0 16px 40px rgba(76,59,145,.07)}.info{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.box{background:#f8f6ff;border:1px solid #eeeafc;border-radius:16px;padding:14px}.box b{display:block;color:#4c3b91;margin-bottom:6px}.btn2{border:0;border-radius:13px;padding:10px 13px;font-weight:900;text-decoration:none;display:inline-flex;background:#ede9fe;color:#4c3b91}.primary{background:#6d5bd0;color:#fff}table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #eeeafc;border-radius:18px;overflow:hidden}th{background:#f1edff;color:#4c3b91;font-size:11px;font-weight:900;padding:10px;text-align:center}td{border-top:1px solid #f1eefb;padding:10px;font-size:11px;font-weight:800;text-align:center}@media(max-width:900px){.info{grid-template-columns:1fr}}
    </style>
    @if(session('success'))<div class="card" style="background:#ecfdf5;color:#166534">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="card" style="background:#fef2f2;color:#991b1b">{{ session('error') }}</div>@endif

    <div class="card" style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn2" href="{{ route('salary-advances.index') }}">رجوع</a>
        @if($salaryAdvance->can_edit_schedule)
            <a class="btn2 primary" href="{{ route('salary-advances.edit', $salaryAdvance) }}">تعديل الأشهر / الأقساط</a>
        @endif
    </div>
    <div class="card">
        <div class="info">
            <div class="box"><b>رقم السلفة</b>{{ $salaryAdvance->advance_number }}</div>
            <div class="box"><b>الموظف</b>{{ $salaryAdvance->employee->display_name ?? '-' }}</div>
            <div class="box"><b>مبلغ السلفة</b>{{ number_format($salaryAdvance->amount, 2) }}</div>
            <div class="box"><b>الحالة</b>{{ $salaryAdvance->status }}</div>
            <div class="box"><b>عدد الأقساط</b>{{ $salaryAdvance->installments_count }}</div>
            <div class="box"><b>كم يخصم كل شهر؟</b>{{ number_format($salaryAdvance->installment_amount, 2) }}</div>
            <div class="box"><b>أول شهر خصم</b>{{ optional($salaryAdvance->deduction_start_date)->format('Y-m') }}</div>
            <div class="box"><b>المتبقي</b>{{ number_format($salaryAdvance->remaining_amount, 2) }}</div>
        </div>
    </div>
    <div class="card">
        <h3 style="margin-top:0;color:#4c3b91">الأشهر المختارة للخصم</h3>
        <table>
            <thead><tr><th>رقم القسط</th><th>المبلغ الذي سيخصم</th><th>شهر الخصم</th><th>الحالة</th><th>تاريخ الخصم</th><th>ملاحظات</th></tr></thead>
            <tbody>
            @forelse($salaryAdvance->installments as $installment)
                <tr>
                    <td>{{ $installment->installment_number }}</td>
                    <td>{{ number_format($installment->amount, 2) }}</td>
                    <td>{{ optional($installment->due_date)->format('Y-m') }}</td>
                    <td>{{ $installment->status }}</td>
                    <td>{{ optional($installment->deducted_date)->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $installment->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">لا توجد أقساط.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
