@extends('layouts.hr')

@section('title', 'طرق صرف الراتب')
@section('page-title', 'طرق صرف الراتب')

@section('content')
    <style>
        .spm-header {
            background: linear-gradient(135deg, #4c3b91, #7c3aed);
            color: white;
            border-radius: 22px;
            padding: 26px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .spm-header h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 900;
        }

        .spm-header p {
            margin: 0;
            opacity: .9;
            font-weight: 700;
        }

        .filters-card {
            background: #fff;
            border: 1px solid #eeeafc;
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 18px;
            box-shadow: 0 12px 30px rgba(76, 59, 145, .06);
        }

        .filters-row {
            display: grid;
            grid-template-columns: 1fr 220px auto;
            gap: 12px;
            align-items: end;
        }

        .btn2 {
            border: 0;
            border-radius: 13px;
            padding: 11px 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
        }

        .primary { background: #6d5bd0; color: #fff; }
        .green { background: #16a34a; color: #fff; }
        .red { background: #dc2626; color: #fff; }
        .soft { background: #ede9fe; color: #4c3b91; }

        .badge-on {
            background: #dcfce7;
            color: #166534;
            padding: 6px 11px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 12px;
        }

        .badge-off {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 11px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 7px;
            justify-content: center;
            flex-wrap: wrap;
        }

        @media(max-width: 800px) {
            .filters-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="spm-header">
        <div>
            <h1>طرق صرف الراتب</h1>
            <p>إدارة طرق صرف الراتب بشكل ديناميكي بدل القيم الثابتة في الكود.</p>
        </div>

        @if(auth()->user()->hasPermission('salary_payment_methods.create'))
            <a href="{{ route('salary-payment-methods.create') }}" class="btn2 green">
                <i class="fas fa-plus"></i>
                إضافة طريقة صرف
            </a>
        @endif
    </div>

    <div class="filters-card">
        <form method="GET" action="{{ route('salary-payment-methods.index') }}">
            <div class="filters-row">
                <div>
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الكود">
                </div>

                <div>
                    <label>الحالة</label>
                    <select name="is_active">
                        <option value="">الكل</option>
                        <option value="1" @selected(request('is_active') === '1')>مفعل</option>
                        <option value="0" @selected(request('is_active') === '0')>غير مفعل</option>
                    </select>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button class="btn2 primary">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <a href="{{ route('salary-payment-methods.index') }}" class="btn2 soft">مسح</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>الترتيب</th>
                <th>الاسم العربي</th>
                <th>الاسم الإنجليزي</th>
                <th>الكود</th>
                <th>الحالة</th>
                <th>عدد الموظفين</th>
                <th>الإجراءات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($salaryPaymentMethods as $method)
                <tr>
                    <td>{{ $method->sort_order }}</td>
                    <td>{{ $method->name_ar }}</td>
                    <td>{{ $method->name_en ?? '-' }}</td>
                    <td><strong>{{ $method->code }}</strong></td>
                    <td>
                        @if($method->is_active)
                            <span class="badge-on">مفعل</span>
                        @else
                            <span class="badge-off">غير مفعل</span>
                        @endif
                    </td>
                    <td>{{ $method->employees_count }}</td>
                    <td>
                        <div class="actions">
                            @if(auth()->user()->hasPermission('salary_payment_methods.edit'))
                                <a href="{{ route('salary-payment-methods.edit', $method) }}" class="btn2 soft">
                                    تعديل
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('salary_payment_methods.delete'))
                                <form method="POST" action="{{ route('salary-payment-methods.destroy', $method) }}" onsubmit="return confirm('هل تريد حذف طريقة الصرف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn2 red">حذف</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">لا توجد طرق صرف راتب.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">
            {{ $salaryPaymentMethods->links() }}
        </div>
    </div>
@endsection
