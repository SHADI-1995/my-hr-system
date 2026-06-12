<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class EmployeeSalaryHistory extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',

        // الحقول القديمة الموجودة عندك
        'old_basic_salary',
        'new_basic_salary',
        'change_amount',
        'change_percentage',
        'effective_date',
        'reason',
        'notes',

        // الحقول الجديدة المطلوبة لنظام مسير الرواتب
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowance',
        'total_salary',
        'effective_from',
        'effective_to',
        'change_reason',

        'changed_by',
    ];

    protected $casts = [
        // الحقول القديمة
        'old_basic_salary' => 'decimal:2',
        'new_basic_salary' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'effective_date' => 'date',

        // الحقول الجديدة
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
