<?php

namespace App\Http\Controllers;

use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function edit()
    {
        abort_if(!auth()->user()->hasPermission('payroll_settings.view'), 403);

        $setting = PayrollSetting::current();

        return view('payroll_settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_settings.edit'), 403);

        $data = $request->validate([
            'salary_day_calculation' => 'required|in:fixed_30_days,actual_month_days,working_days',
            'default_payment_method' => 'required|in:bank_transfer,cash,cheque,other',
            'allow_negative_net_salary' => 'nullable|boolean',
            'rounding_decimals' => 'required|integer|min:0|max:4',
            'auto_deduct_approved_advances' => 'nullable|boolean',
            'auto_deduct_unpaid_leaves' => 'nullable|boolean',
            'auto_deduct_suspensions' => 'nullable|boolean',
        ], [
            'salary_day_calculation.required' => 'طريقة احتساب أيام الراتب مطلوبة',
            'salary_day_calculation.in' => 'طريقة احتساب أيام الراتب غير صحيحة',
            'default_payment_method.required' => 'طريقة الصرف الافتراضية مطلوبة',
            'default_payment_method.in' => 'طريقة الصرف الافتراضية غير صحيحة',
            'rounding_decimals.required' => 'عدد الكسور العشرية مطلوب',
            'rounding_decimals.integer' => 'عدد الكسور العشرية يجب أن يكون رقمًا صحيحًا',
            'rounding_decimals.min' => 'عدد الكسور العشرية لا يمكن أن يكون أقل من صفر',
            'rounding_decimals.max' => 'عدد الكسور العشرية لا يمكن أن يتجاوز 4',
        ]);

        $data['allow_negative_net_salary'] = $request->boolean('allow_negative_net_salary');
        $data['auto_deduct_approved_advances'] = $request->boolean('auto_deduct_approved_advances');
        $data['auto_deduct_unpaid_leaves'] = $request->boolean('auto_deduct_unpaid_leaves');
        $data['auto_deduct_suspensions'] = $request->boolean('auto_deduct_suspensions');

        $setting = PayrollSetting::current();
        $setting->update($data);

        return redirect()
            ->route('payroll-settings.edit')
            ->with('success', 'تم تحديث إعدادات الرواتب بنجاح');
    }
}
