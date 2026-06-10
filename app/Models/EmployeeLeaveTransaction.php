<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class EmployeeLeaveTransaction extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',
        'employee_leave_balance_id',
        'transaction_type',
        'days',
        'before_balance',
        'after_balance',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
    ];

    protected $casts = [
        'days' => 'decimal:2',
        'before_balance' => 'decimal:2',
        'after_balance' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveBalance()
    {
        return $this->belongsTo(EmployeeLeaveBalance::class, 'employee_leave_balance_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }
}
