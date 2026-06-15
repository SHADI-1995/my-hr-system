<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'employee_number',
        'employee_name',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowance',
        'gross_salary',
        'regular_deductions',
        'salary_advance_deductions',
        'suspension_deductions',
        'total_deductions',
        'net_salary',
        'period_days',
        'suspended_days',
        'status',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'regular_deductions' => 'decimal:2',
        'salary_advance_deductions' => 'decimal:2',
        'suspension_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function components()
    {
        return $this->hasMany(PayrollItemComponent::class);
    }
}
