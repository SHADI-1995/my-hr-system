<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class EmployeeSalaryHistory extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',
        'old_basic_salary',
        'new_basic_salary',
        'change_amount',
        'change_percentage',
        'effective_date',
        'reason',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
