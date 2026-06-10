<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Department extends Model
{
    use Auditable;
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
