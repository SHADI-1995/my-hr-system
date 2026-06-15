<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    protected $fillable = [
        'employee_id',
        'advance_number',
        'amount',
        'installments_count',
        'installment_amount',
        'request_date',
        'deduction_start_date',
        'status',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'request_date' => 'date',
        'deduction_start_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function installments()
    {
        return $this->hasMany(SalaryAdvanceInstallment::class)->orderBy('installment_number');
    }

    public function pendingInstallments()
    {
        return $this->hasMany(SalaryAdvanceInstallment::class)->where('status', 'pending');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public static function generateNumber(): string
    {
        $last = static::query()
            ->where('advance_number', 'like', 'ADV-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/ADV-(\d+)/', (string) $last->advance_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'ADV-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->installments()->where('status', 'deducted')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }

    public function getCanEditScheduleAttribute(): bool
    {
        return !$this->installments()->where('status', 'deducted')->exists()
            && in_array($this->status, ['pending', 'approved'], true);
    }
}
