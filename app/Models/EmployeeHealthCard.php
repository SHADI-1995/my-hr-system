<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class EmployeeHealthCard extends Model
{
    protected $fillable = [
        'employee_id',
        'card_number',
        'issue_date',
        'expiry_date',
        'issuer',
        'notes',
        'photo',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected $appends = [
        'remaining_days',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected function remainingDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->expiry_date) {
                    return null;
                }

                $today = Carbon::today();
                $expiryDate = Carbon::parse($this->expiry_date)->startOfDay();

                return $today->diffInDays($expiryDate, false);
            }
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->expiry_date) {
                    return 'missing';
                }

                $remainingDays = $this->remaining_days;

                if ($remainingDays < 0) {
                    return 'expired';
                }

                if ($remainingDays <= 30) {
                    return 'near_expiry';
                }

                return 'valid';
            }
        );
    }
}
