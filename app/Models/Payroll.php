<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Payroll extends Model
{
    use Auditable;
    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
