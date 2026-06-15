<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'period_number',
        'month',
        'start_date',
        'end_date',
        'payroll_group_scope',
        'status',
        'employees_count',
        'total_gross_salary',
        'total_regular_deductions',
        'total_salary_advances',
        'total_suspension_deductions',
        'total_deductions',
        'total_net_salary',
        'created_by',
        'calculated_by',
        'calculated_at',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payroll_group_scope' => 'string',
        'total_gross_salary' => 'decimal:2',
        'total_regular_deductions' => 'decimal:2',
        'total_salary_advances' => 'decimal:2',
        'total_suspension_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net_salary' => 'decimal:2',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function components()
    {
        return $this->hasMany(PayrollItemComponent::class);
    }

    /*
     * مجموعات الرواتب المرتبطة بهذا المسير.
     * تستخدم عندما يكون payroll_group_scope = selected.
     */
    public function payrollGroups()
    {
        return $this->belongsToMany(
            PayrollGroup::class,
            'payroll_period_groups',
            'payroll_period_id',
            'payroll_group_id'
        )->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculatedBy()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public static function generateNumber(): string
    {
        $last = static::query()
            ->where('period_number', 'like', 'PAY-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/PAY-(\d+)/', (string) $last->period_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'PAY-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function getPayrollGroupScopeTextAttribute(): string
    {
        return match ($this->payroll_group_scope ?? 'all') {
            'selected' => 'مجموعات محددة',
            'all' => 'كل المجموعات',
            default => $this->payroll_group_scope ?: 'كل المجموعات',
        };
    }

    public function getPayrollGroupsTextAttribute(): string
    {
        if (($this->payroll_group_scope ?? 'all') === 'all') {
            return 'كل مجموعات الرواتب';
        }

        $groups = $this->relationLoaded('payrollGroups')
            ? $this->payrollGroups
            : $this->payrollGroups()->get();

        if ($groups->isEmpty()) {
            return '-';
        }

        return $groups->pluck('name_ar')->implode('، ');
    }

    public function getCanCalculateAttribute(): bool
    {
        return in_array($this->status, ['draft', 'calculated'], true);
    }

    public function getCanApproveAttribute(): bool
    {
        return $this->status === 'calculated' && $this->items()->exists();
    }

    public function getCanPayAttribute(): bool
    {
        return $this->status === 'approved' && $this->items()->exists();
    }
}
