<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Position extends Model
{
    use Auditable;
    protected $fillable = [
        'department_id',
        'title',
        'code',
        'min_salary',
        'max_salary',
        'is_active'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
