<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Attendance extends Model
{
    use Auditable;
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
