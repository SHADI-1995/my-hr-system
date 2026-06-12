<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = [
        'salary_day_calculation',
        'default_payment_method',
        'allow_negative_net_salary',
        'rounding_decimals',
        'auto_deduct_approved_advances',
        'auto_deduct_unpaid_leaves',
        'auto_deduct_suspensions',
    ];

    protected $casts = [
        'allow_negative_net_salary' => 'boolean',
        'auto_deduct_approved_advances' => 'boolean',
        'auto_deduct_unpaid_leaves' => 'boolean',
        'auto_deduct_suspensions' => 'boolean',
        'rounding_decimals' => 'integer',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'salary_day_calculation' => 'fixed_30_days',
            'default_payment_method' => 'bank_transfer',
            'allow_negative_net_salary' => false,
            'rounding_decimals' => 2,
            'auto_deduct_approved_advances' => true,
            'auto_deduct_unpaid_leaves' => true,
            'auto_deduct_suspensions' => true,
        ]);
    }
}
