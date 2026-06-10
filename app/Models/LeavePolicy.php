<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class LeavePolicy extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'annual_days_before_5_years',
        'annual_days_after_5_years',
        'after_years',
        'leave_year_type',
        'carry_forward_enabled',
        'max_carry_forward_days',
        'exclude_weekends',
        'exclude_official_holidays',
        'inactive_employee_accrual',
        'is_active',
    ];

    protected $casts = [
        'annual_days_before_5_years' => 'integer',
        'annual_days_after_5_years' => 'integer',
        'after_years' => 'integer',
        'carry_forward_enabled' => 'boolean',
        'exclude_weekends' => 'boolean',
        'exclude_official_holidays' => 'boolean',
        'inactive_employee_accrual' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }
}
