<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPaymentMethod extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
