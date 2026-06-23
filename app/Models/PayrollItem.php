<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',

        // Snapshot بيانات الموظف وقت احتساب المسير
        'employee_number',
        'employee_name',
        'employee_nationality',
        'employee_position',
        'employee_department',
        'employee_status_text',
        'salary_payment_method_name',
        'payroll_group_name',
        'cost_center_name',

        // فترة الاستحقاق
        'period_days',
        'payable_days',
        'eligible_start_date',
        'eligible_end_date',
        'employment_status_note',

        // الراتب والبدلات
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowance',
        'gross_salary',

        // الاستقطاعات
        'regular_deductions',
        'salary_advance_deductions',
        'suspension_deductions',
        'unpaid_leave_deductions',
        'unpaid_leave_days',
        'total_deductions',

        // الصافي
        'net_salary',

        'suspended_days',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_days' => 'integer',
        'payable_days' => 'integer',
        'suspended_days' => 'integer',
        'unpaid_leave_days' => 'integer',

        'eligible_start_date' => 'date',
        'eligible_end_date' => 'date',

        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',

        'regular_deductions' => 'decimal:2',
        'salary_advance_deductions' => 'decimal:2',
        'suspension_deductions' => 'decimal:2',
        'unpaid_leave_deductions' => 'decimal:2',
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
