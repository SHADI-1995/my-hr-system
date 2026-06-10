<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Employee extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_number',

        'first_name',
        'second_name',
        'last_name',
        'full_name',

        'email',
        'phone',

        'department_id',
        'position_id',
        'nationality_id',

        // المدير المباشر للموظف
        'direct_manager_user_id',

        // بيانات بوابة الموظف
        'portal_password',
        'portal_registered_at',
        'portal_last_login_at',

        'hire_date',
        'termination_date',
        'termination_reason',

        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowance',

        'bank_name',
        'iban',

        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'portal_registered_at' => 'datetime',
        'portal_last_login_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($employee) {
            if (empty($employee->employee_number)) {
                $lastEmployee = Employee::whereNotNull('employee_number')
                    ->where('employee_number', 'like', 'EMP-%')
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;

                if ($lastEmployee && preg_match('/EMP-(\d+)/', $employee->employee_number, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                $employee->employee_number = 'EMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function ($employee) {
            $fullName = trim(
                ($employee->first_name ?? '') . ' ' .
                ($employee->second_name ?? '') . ' ' .
                ($employee->last_name ?? '')
            );

            if ($fullName !== '') {
                $employee->full_name = $fullName;
            }
        });

        static::updated(function ($employee) {
            if ($employee->wasChanged('basic_salary')) {
                $oldSalary = (float) $employee->getOriginal('basic_salary');
                $newSalary = (float) $employee->basic_salary;
                $changeAmount = $newSalary - $oldSalary;

                $changePercentage = 0;

                if ($oldSalary > 0) {
                    $changePercentage = ($changeAmount / $oldSalary) * 100;
                }

                EmployeeSalaryHistory::create([
                    'employee_id' => $employee->id,
                    'old_basic_salary' => $oldSalary,
                    'new_basic_salary' => $newSalary,
                    'change_amount' => $changeAmount,
                    'change_percentage' => $changePercentage,
                    'effective_date' => now()->toDateString(),
                    'reason' => 'تعديل الراتب الأساسي من ملف الموظف',
                    'changed_by' => auth()->id(),
                    'notes' => null,
                ]);
            }
        });
    }

    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->full_name ?: trim(
            ($this->first_name ?? '') . ' ' .
            ($this->second_name ?? '') . ' ' .
            ($this->last_name ?? '')
        );
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function directManagerUser()
    {
        return $this->belongsTo(User::class, 'direct_manager_user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function iqamas()
    {
        return $this->hasMany(EmployeeIqama::class);
    }

    public function latestIqama()
    {
        return $this->hasOne(EmployeeIqama::class)->latestOfMany();
    }

    public function passports()
    {
        return $this->hasMany(EmployeePassport::class);
    }

    public function latestPassport()
    {
        return $this->hasOne(EmployeePassport::class)->latestOfMany();
    }

    public function healthCards()
    {
        return $this->hasMany(EmployeeHealthCard::class);
    }

    public function latestHealthCard()
    {
        return $this->hasOne(EmployeeHealthCard::class)->latestOfMany();
    }

    public function salaryHistories()
    {
        return $this->hasMany(EmployeeSalaryHistory::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function currentLeaveBalance()
    {
        return $this->hasOne(EmployeeLeaveBalance::class)
            ->where('status', 'open')
            ->latestOfMany();
    }

    public function leaveTransactions()
    {
        return $this->hasMany(EmployeeLeaveTransaction::class);
    }
}
