<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItemComponent extends Model
{
    protected $fillable = [
        'payroll_item_id',
        'payroll_period_id',
        'employee_id',
        'type',
        'name',
        'amount',
        'source_type',
        'source_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payrollItem()
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
