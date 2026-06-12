<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'is_fixed',
        'is_active',
        'is_system',
        'description',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function scopeEarnings($query)
    {
        return $query->where('type', 'earning');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
