<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeavePolicy;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateLeaveBalances extends Command
{
    protected $signature = 'leaves:calculate-balances {--force : Recalculate existing open balances using the active policy}';

    protected $description = 'Calculate annual leave balances for employees based on the active leave policy';

    public function handle(): int
    {
        $policy = LeavePolicy::where('is_active', true)->first();

        if (!$policy) {
            $this->error('لا توجد سياسة إجازات مفعلة');
            return self::FAILURE;
        }

        $employees = Employee::query()
            ->whereNotNull('hire_date')
            ->where(function ($query) use ($policy) {
                $query->where('status', 'active');

                if ($policy->inactive_employee_accrual) {
                    $query->orWhere('status', 'inactive');
                }
            })
            ->get();

        foreach ($employees as $employee) {
            DB::transaction(function () use ($employee, $policy) {
                $this->calculateForEmployee($employee, $policy);
            });
        }

        $this->info('تم احتساب أرصدة الإجازات بنجاح');

        return self::SUCCESS;
    }

    private function calculateForEmployee(Employee $employee, LeavePolicy $policy): void
    {
        $hireDate = Carbon::parse($employee->hire_date)->startOfDay();
        $today = now()->startOfDay();

        [$startDate, $endDate, $yearLabel] = $this->getLeaveYearDates($hireDate, $today, $policy);

        $serviceYears = $hireDate->diffInYears($today);

        $annualDays = $serviceYears >= $policy->after_years
            ? (float) $policy->annual_days_after_5_years
            : (float) $policy->annual_days_before_5_years;

        $annualDays = $this->calculateProratedDaysIfNeeded(
            $annualDays,
            $hireDate,
            $startDate,
            $endDate,
            $policy
        );

        $previousBalance = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where('status', 'closed')
            ->latest('service_year_end')
            ->first();

        $carriedForwardDays = 0;

        if (
            $policy->carry_forward_enabled &&
            $previousBalance &&
            (float) $previousBalance->remaining_days > 0
        ) {
            $carriedForwardDays = min(
                (float) $previousBalance->remaining_days,
                (float) $policy->max_carry_forward_days
            );
        }

        $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->whereDate('service_year_start', $startDate->toDateString())
            ->whereDate('service_year_end', $endDate->toDateString())
            ->first();

        if (!$balance) {
            $balance = EmployeeLeaveBalance::create([
                'employee_id' => $employee->id,
                'leave_policy_id' => $policy->id,
                'year_label' => $yearLabel,
                'service_year_start' => $startDate->toDateString(),
                'service_year_end' => $endDate->toDateString(),
                'annual_entitled_days' => $annualDays,
                'carried_forward_days' => $carriedForwardDays,
                'used_paid_days' => 0,
                'used_unpaid_days' => 0,
                'used_other_days' => 0,
                'remaining_days' => $annualDays + $carriedForwardDays,
                'status' => 'open',
            ]);

            EmployeeLeaveTransaction::create([
                'employee_id' => $employee->id,
                'employee_leave_balance_id' => $balance->id,
                'transaction_type' => 'annual_accrual',
                'days' => $annualDays,
                'before_balance' => 0,
                'after_balance' => $annualDays,
                'description' => 'إضافة رصيد الإجازة السنوية حسب سياسة الإجازات',
                'created_by' => null,
            ]);

            if ($carriedForwardDays > 0) {
                EmployeeLeaveTransaction::create([
                    'employee_id' => $employee->id,
                    'employee_leave_balance_id' => $balance->id,
                    'transaction_type' => 'carry_forward',
                    'days' => $carriedForwardDays,
                    'before_balance' => $annualDays,
                    'after_balance' => $annualDays + $carriedForwardDays,
                    'description' => 'ترحيل رصيد إجازات من السنة السابقة',
                    'created_by' => null,
                ]);
            }

            return;
        }

        if (!$this->option('force') || $balance->status !== 'open') {
            return;
        }

        $oldRemaining = (float) $balance->remaining_days;
        $oldAnnualDays = (float) $balance->annual_entitled_days;
        $oldCarriedForward = (float) $balance->carried_forward_days;

        $usedPaidDays = (float) $balance->used_paid_days;

        $newRemaining = ($annualDays + $carriedForwardDays) - $usedPaidDays;

        if ($newRemaining < 0) {
            $newRemaining = 0;
        }

        $changed =
            $oldAnnualDays !== (float) $annualDays ||
            $oldCarriedForward !== (float) $carriedForwardDays ||
            $oldRemaining !== (float) $newRemaining ||
            (int) $balance->leave_policy_id !== (int) $policy->id ||
            $balance->year_label !== $yearLabel;

        if (!$changed) {
            return;
        }

        $balance->update([
            'leave_policy_id' => $policy->id,
            'year_label' => $yearLabel,
            'annual_entitled_days' => $annualDays,
            'carried_forward_days' => $carriedForwardDays,
            'remaining_days' => $newRemaining,
        ]);

        EmployeeLeaveTransaction::create([
            'employee_id' => $employee->id,
            'employee_leave_balance_id' => $balance->id,
            'transaction_type' => 'policy_recalculation',
            'days' => $newRemaining - $oldRemaining,
            'before_balance' => $oldRemaining,
            'after_balance' => $newRemaining,
            'description' => 'إعادة احتساب الرصيد بسبب تعديل سياسة الإجازات',
            'created_by' => auth()->id(),
        ]);
    }

    private function getLeaveYearDates(Carbon $hireDate, Carbon $today, LeavePolicy $policy): array
    {
        if ($policy->leave_year_type === 'gregorian') {
            $startDate = Carbon::create($today->year, 1, 1)->startOfDay();
            $endDate = Carbon::create($today->year, 12, 31)->endOfDay();
            $yearLabel = (string) $today->year;

            return [$startDate, $endDate, $yearLabel];
        }

        if ($policy->leave_year_type === 'hijri') {
            /*
             * حاليًا يتم استخدام السنة الميلادية كتاريخ فعلي مع وسم هجري.
             * لاحقًا يمكن إضافة جدول leave_calendar_years لدعم السنة الهجرية بدقة.
             */
            $startDate = Carbon::create($today->year, 1, 1)->startOfDay();
            $endDate = Carbon::create($today->year, 12, 31)->endOfDay();
            $yearLabel = 'هجري-' . $today->year;

            return [$startDate, $endDate, $yearLabel];
        }

        $startDate = Carbon::create(
            $today->year,
            $hireDate->month,
            min($hireDate->day, Carbon::create($today->year, $hireDate->month, 1)->daysInMonth)
        )->startOfDay();

        if ($startDate->greaterThan($today)) {
            $startDate->subYear();
        }

        $endDate = $startDate->copy()->addYear()->subDay()->endOfDay();
        $yearLabel = $startDate->format('Y') . '/' . $endDate->format('Y');

        return [$startDate, $endDate, $yearLabel];
    }

    private function calculateProratedDaysIfNeeded(
        float $annualDays,
        Carbon $hireDate,
        Carbon $startDate,
        Carbon $endDate,
        LeavePolicy $policy
    ): float {
        if ($policy->leave_year_type === 'hire_date') {
            return $annualDays;
        }

        if ($hireDate->lessThanOrEqualTo($startDate)) {
            return $annualDays;
        }

        if ($hireDate->between($startDate, $endDate)) {
            $totalDaysInYear = $startDate->diffInDays($endDate) + 1;
            $remainingDaysInYear = $hireDate->diffInDays($endDate) + 1;

            return round(($annualDays / $totalDaysInYear) * $remainingDaysInYear, 2);
        }

        return $annualDays;
    }
}
