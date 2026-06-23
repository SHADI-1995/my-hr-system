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
        'employees_count' => 'integer',
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

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function components()
    {
        return $this->hasMany(PayrollItemComponent::class);
    }

    /*
     * سجل حركات مسير الرواتب.
     * يستخدم لعرض: من أنشأ، من احتسب، من اعتمد، من صرف، ومتى تمت العملية.
     */
    public function logs()
    {
        return $this->hasMany(PayrollPeriodLog::class)->latest();
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

    /*
     |--------------------------------------------------------------------------
     | Numbering
     |--------------------------------------------------------------------------
     */

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

    /*
     |--------------------------------------------------------------------------
     | Display Accessors
     |--------------------------------------------------------------------------
     */

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

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'calculated' => 'محسوب',
            'approved' => 'معتمد',
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
            default => $this->status ?: '-',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'draft',
            'calculated' => 'calculated',
            'approved' => 'approved',
            'paid' => 'paid',
            'cancelled' => 'cancelled',
            default => 'draft',
        };
    }

    public function getStatusDescriptionAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'المسير ما زال مسودة ويمكن احتسابه.',
            'calculated' => 'تم احتساب المسير ويمكن اعتماده أو إعادة احتسابه.',
            'approved' => 'تم اعتماد المسير ولا يمكن إعادة احتسابه إلا بعد إلغاء الاعتماد من صلاحية خاصة.',
            'paid' => 'تم صرف المسير وهو مقفل بالكامل.',
            'cancelled' => 'تم إلغاء المسير.',
            default => '-',
        };
    }

    /*
     |--------------------------------------------------------------------------
     | Workflow / Locks
     |--------------------------------------------------------------------------
     */

    public function getCanCalculateAttribute(): bool
    {
        /*
         * يسمح بالاحتساب في حالتين فقط:
         * draft      => أول احتساب
         * calculated => إعادة احتساب قبل الاعتماد
         *
         * لا يسمح بعد approved أو paid.
         */
        return in_array($this->status, ['draft', 'calculated'], true);
    }

    public function getCanRecalculateAttribute(): bool
    {
        return $this->status === 'calculated';
    }

    public function getCanApproveAttribute(): bool
    {
        return $this->status === 'calculated' && $this->items()->exists();
    }

    public function getCanPayAttribute(): bool
    {
        return $this->status === 'approved' && $this->items()->exists();
    }

    public function getCanDeleteAttribute(): bool
    {
        /*
         * لا نحذف مسير معتمد أو مدفوع حتى نحافظ على السجلات المالية.
         */
        return in_array($this->status, ['draft', 'calculated'], true);
    }

    public function getCanEditAttribute(): bool
    {
        return in_array($this->status, ['draft', 'calculated'], true);
    }

    public function getIsLockedAttribute(): bool
    {
        /*
         * بمجرد الاعتماد أو الصرف يعتبر المسير مقفل.
         */
        return in_array($this->status, ['approved', 'paid'], true);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsCalculatedAttribute(): bool
    {
        return $this->status === 'calculated';
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /*
     |--------------------------------------------------------------------------
     | payroll_period_bank_transfer_batches_relation
     |--------------------------------------------------------------------------
     */

    public function bankTransferBatches()
    {
        return $this->hasMany(\App\Models\PayrollBankTransferBatch::class);
    }


    /*
     |--------------------------------------------------------------------------
     | Workflow Helpers
     |--------------------------------------------------------------------------
     */

    public function markAsCalculated(?int $userId = null): bool
    {
        return $this->update([
            'status' => 'calculated',
            'calculated_by' => $userId,
            'calculated_at' => now(),
        ]);
    }

    public function markAsApproved(?int $userId = null): bool
    {
        if (!$this->can_approve) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(?int $userId = null): bool
    {
        if (!$this->can_pay) {
            return false;
        }

        return $this->update([
            'status' => 'paid',
            'paid_by' => $userId,
            'paid_at' => now(),
        ]);
    }
}
