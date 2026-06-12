<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'deduction_number',
        'deduction_type',
        'amount',
        'deduction_mode',
        'installments_count',
        'monthly_amount',
        'start_date',
        'end_date',
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
        'monthly_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
            ->where('deduction_number', 'like', 'DED-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/DED-(\d+)/', (string) $last->deduction_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'DED-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
