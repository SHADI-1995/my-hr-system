<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class LeaveType extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'code',
        'is_paid',
        'deduct_from_annual_balance',
        'requires_attachment',
        'requires_approval',
        'auto_approved',
        'max_days_per_year',
        'is_active',

        // إعدادات تأثير نوع الإجازة على مسير الرواتب
        'affects_payroll',
        'salary_percentage',
        'payroll_policy_note',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'deduct_from_annual_balance' => 'boolean',
        'requires_attachment' => 'boolean',
        'requires_approval' => 'boolean',
        'auto_approved' => 'boolean',
        'is_active' => 'boolean',
        'affects_payroll' => 'boolean',
        'salary_percentage' => 'decimal:2',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
