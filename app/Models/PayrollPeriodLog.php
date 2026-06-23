<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriodLog extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionTextAttribute(): string
    {
        return match ($this->action) {
            'created' => 'إنشاء المسير',
            'calculated' => 'احتساب المسير',
            'approved' => 'اعتماد المسير',
            'approval_cancelled' => 'إلغاء الاعتماد',
            'paid' => 'صرف المسير',
            'deleted' => 'حذف المسير',
            default => $this->action ?: '-',
        };
    }
}
