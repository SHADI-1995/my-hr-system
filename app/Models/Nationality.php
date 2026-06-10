<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Nationality extends Model
{
    use Auditable;

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'is_active',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
