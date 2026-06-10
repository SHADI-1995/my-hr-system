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
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'deduct_from_annual_balance' => 'boolean',
        'requires_attachment' => 'boolean',
        'requires_approval' => 'boolean',
        'auto_approved' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
