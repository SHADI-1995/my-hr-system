@extends('layouts.hr')

@section('title', 'تعديل راتب')
@section('page-title', 'تعديل راتب')

@section('content')

    <div class="card">

        <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if(auth()->user()->hasPermission('payrolls.edit.employee_id'))
                <div style="margin-bottom:15px;">
                    <label>الموظف</label>
                    <select name="employee_id" required style="width:100%;padding:10px;margin-top:5px;">
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $payroll->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الموظف</label>
                    <input
                        type="text"
                        value="{{ $payroll->employee->name ?? '-' }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('payrolls.edit.month'))
                <div style="margin-bottom:15px;">
                    <label>الشهر</label>
                    <input
                        type="month"
                        name="month"
                        value="{{ old('month', $payroll->month) }}"
                        required
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الشهر</label>
                    <input
                        type="text"
                        value="{{ $payroll->month }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('payrolls.edit.basic_salary'))
                <div style="margin-bottom:15px;">
                    <label>الراتب الأساسي</label>
                    <input
                        type="number"
                        name="basic_salary"
                        value="{{ old('basic_salary', $payroll->basic_salary) }}"
                        required
                        min="0"
                        step="0.01"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الراتب الأساسي</label>
                    <input
                        type="text"
                        value="{{ number_format($payroll->basic_salary, 2) }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('payrolls.edit.allowances'))
                <div style="margin-bottom:15px;">
                    <label>البدلات</label>
                    <input
                        type="number"
                        name="allowances"
                        value="{{ old('allowances', $payroll->allowances) }}"
                        min="0"
                        step="0.01"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>البدلات</label>
                    <input
                        type="text"
                        value="{{ number_format($payroll->allowances, 2) }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            @if(auth()->user()->hasPermission('payrolls.edit.deductions'))
                <div style="margin-bottom:15px;">
                    <label>الخصومات</label>
                    <input
                        type="number"
                        name="deductions"
                        value="{{ old('deductions', $payroll->deductions) }}"
                        min="0"
                        step="0.01"
                        style="width:100%;padding:10px;margin-top:5px;">
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الخصومات</label>
                    <input
                        type="text"
                        value="{{ number_format($payroll->deductions, 2) }}"
                        disabled
                        style="width:100%;padding:10px;margin-top:5px;background:#f3f4f6;">
                </div>
            @endif

            <div style="margin-bottom:15px;">
                <label>الصافي الحالي</label>
                <input
                    type="text"
                    value="{{ number_format($payroll->net_salary, 2) }}"
                    disabled
                    style="width:100%;padding:10px;margin-top:5px;background:#eef2ff;">
            </div>

            @if(auth()->user()->hasPermission('payrolls.edit.status'))
                <div style="margin-bottom:15px;">
                    <label>الحالة</label>
                    <select name="status" required style="width:100%;padding:10px;margin-top:5px;">
                        <option value="draft" {{ old('status', $payroll->status) == 'draft' ? 'selected' : '' }}>
                            مسودة
                        </option>
                        <option value="paid" {{ old('status', $payroll->status) == 'paid' ? 'selected' : '' }}>
                            مدفوع
                        </option>
                    </select>
                </div>
            @else
                <div style="margin-bottom:15px;">
                    <label>الحالة</label>
                    <div style="margin-top:5px;">
                        @if($payroll->status == 'paid')
                            <span class="badge badge-active">مدفوع</span>
                        @else
                            <span class="badge badge-inactive">مسودة</span>
                        @endif
                    </div>
                </div>
            @endif

            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                تحديث
            </button>

            <a href="{{ route('payrolls.index') }}" class="btn">
                رجوع
            </a>
        </form>

    </div>

@endsection
