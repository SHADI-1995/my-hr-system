<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceRequestLog extends Model
{
    protected $fillable = [
        'salary_advance_request_id',
        'user_id',
        'transaction_type',
        'old_status',
        'new_status',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function request()
    {
        return $this->belongsTo(SalaryAdvanceRequest::class, 'salary_advance_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
