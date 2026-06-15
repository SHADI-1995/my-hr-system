<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceInstallment extends Model
{
    protected $fillable = [
        'salary_advance_id',
        'employee_id',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'payroll_period_id',
        'payroll_item_id',
        'deducted_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'deducted_date' => 'date',
    ];

    public function salaryAdvance()
    {
        return $this->belongsTo(SalaryAdvance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
