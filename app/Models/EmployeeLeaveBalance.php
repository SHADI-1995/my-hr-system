<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class EmployeeLeaveBalance extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',
        'leave_policy_id',
        'year_label',
        'service_year_start',
        'service_year_end',
        'annual_entitled_days',
        'carried_forward_days',
        'used_paid_days',
        'used_unpaid_days',
        'used_other_days',
        'remaining_days',
        'status',
    ];

    protected $casts = [
        'service_year_start' => 'date',
        'service_year_end' => 'date',
        'annual_entitled_days' => 'decimal:2',
        'carried_forward_days' => 'decimal:2',
        'used_paid_days' => 'decimal:2',
        'used_unpaid_days' => 'decimal:2',
        'used_other_days' => 'decimal:2',
        'remaining_days' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leavePolicy()
    {
        return $this->belongsTo(LeavePolicy::class);
    }

    public function transactions()
    {
        return $this->hasMany(EmployeeLeaveTransaction::class);
    }
}
