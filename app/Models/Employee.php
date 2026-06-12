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
        'direct_manager_user_id',

        // بيانات بوابة الموظف
        'portal_password',
        'portal_registered_at',
        'portal_last_login_at',

        'hire_date',
        'termination_date',
        'termination_reason',

        // بيانات الراتب الحالية
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowance',

        // بيانات البنك
        'bank_name',
        'iban',

        // بيانات نظام الرواتب ومسير الرواتب
        'salary_payment_method',
        'payroll_status',
        'salary_effective_date',
        'bank_account_name',
        'payroll_group',
        'cost_center',

        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary_effective_date' => 'date',
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

                if ($lastEmployee && preg_match('/EMP-(\d+)/', (string) $lastEmployee->employee_number, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                $employee->employee_number = 'EMP-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
            }

            if (empty($employee->payroll_status)) {
                $employee->payroll_status = 'included';
            }

            if (empty($employee->salary_payment_method)) {
                $employee->salary_payment_method = 'bank_transfer';
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
            $salaryFields = [
                'basic_salary',
                'housing_allowance',
                'transport_allowance',
                'food_allowance',
                'other_allowance',
            ];

            if (!$employee->wasChanged($salaryFields)) {
                return;
            }

            $oldBasicSalary = (float) $employee->getOriginal('basic_salary');
            $newBasicSalary = (float) $employee->basic_salary;
            $changeAmount = $newBasicSalary - $oldBasicSalary;

            $changePercentage = 0;

            if ($oldBasicSalary > 0) {
                $changePercentage = ($changeAmount / $oldBasicSalary) * 100;
            }

            $totalSalary =
                (float) ($employee->basic_salary ?? 0)
                + (float) ($employee->housing_allowance ?? 0)
                + (float) ($employee->transport_allowance ?? 0)
                + (float) ($employee->food_allowance ?? 0)
                + (float) ($employee->other_allowance ?? 0);

            EmployeeSalaryHistory::create([
                'employee_id' => $employee->id,

                // الحقول القديمة الموجودة عندك
                'old_basic_salary' => $oldBasicSalary,
                'new_basic_salary' => $newBasicSalary,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'effective_date' => $employee->salary_effective_date ?: now()->toDateString(),
                'reason' => 'تعديل بيانات الراتب من ملف الموظف',
                'notes' => null,

                // الحقول الجديدة المطلوبة لمسير الرواتب
                'basic_salary' => $employee->basic_salary ?? 0,
                'housing_allowance' => $employee->housing_allowance ?? 0,
                'transport_allowance' => $employee->transport_allowance ?? 0,
                'food_allowance' => $employee->food_allowance ?? 0,
                'other_allowance' => $employee->other_allowance ?? 0,
                'total_salary' => $totalSalary,
                'effective_from' => $employee->salary_effective_date ?: now()->toDateString(),
                'effective_to' => null,
                'change_reason' => 'تعديل بيانات الراتب من ملف الموظف',

                'changed_by' => auth()->id(),
            ]);
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

    public function getCurrentTotalSalaryAttribute()
    {
        return (float) ($this->basic_salary ?? 0)
            + (float) ($this->housing_allowance ?? 0)
            + (float) ($this->transport_allowance ?? 0)
            + (float) ($this->food_allowance ?? 0)
            + (float) ($this->other_allowance ?? 0);
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

    public function latestSalaryHistory()
    {
        return $this->hasOne(EmployeeSalaryHistory::class)->latestOfMany();
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function suspensions()
    {
        return $this->hasMany(EmployeeSuspension::class);
    }

    public function activeSuspensions()
    {
        return $this->hasMany(EmployeeSuspension::class)->where('status', 'active');
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
