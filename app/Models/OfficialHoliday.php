<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class OfficialHoliday extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'year_label',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
