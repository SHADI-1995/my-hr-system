<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployeeSuspension extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'resume_date',
        'salary_percentage',
        'status',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'resumed_by',
        'resumed_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'resume_date' => 'date',
        'salary_percentage' => 'decimal:2',
        'approved_at' => 'datetime',
        'resumed_at' => 'datetime',
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

    public function resumedBy()
    {
        return $this->belongsTo(User::class, 'resumed_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function getSuspensionDaysAttribute(): int
    {
        if (!$this->start_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date)->startOfDay();

        /*
         * resume_date هو أول يوم عودة للعمل.
         * لذلك أيام الإيقاف تكون إلى اليوم السابق لتاريخ العودة.
         */
        $end = $this->resume_date
            ? Carbon::parse($this->resume_date)->subDay()->startOfDay()
            : now()->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        return $start->diffInDays($end) + 1;
    }
}
