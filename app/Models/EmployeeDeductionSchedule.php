<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeductionSchedule extends Model
{
    protected $fillable = [
        'employee_deduction_id',
        'employee_id',
        'payroll_month',
        'amount',
        'percentage',
        'status',
        'payroll_period_id',
        'payroll_item_id',
        'deducted_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'deducted_at' => 'datetime',
    ];

    public function deduction()
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function payrollItem()
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->where('payroll_month', $month);
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function markAsDeducted(PayrollPeriod $period, PayrollItem $item): void
    {
        $this->update([
            'status' => 'deducted',
            'payroll_period_id' => $period->id,
            'payroll_item_id' => $item->id,
            'deducted_at' => now(),
        ]);
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'معلق',
            'deducted' => 'تم الخصم',
            'skipped' => 'تم التجاوز',
            'cancelled' => 'ملغي',
            default => $this->status ?: '-',
        };
    }
}
