<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionSchedule;
use App\Models\EmployeeSuspension;
use App\Models\LeaveRequest;
use App\Models\PayrollItem;
use App\Models\PayrollItemComponent;
use App\Models\PayrollPeriod;
use App\Models\PayrollGroup;
use App\Models\SalaryAdvanceInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.view'), 403);

        $query = PayrollPeriod::query()->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->month) {
            $query->where('month', $request->month);
        }

        $periods = $query->paginate(20);

        return view('payroll_periods.index', compact('periods'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.create'), 403);

        $payrollGroups = PayrollGroup::where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();

        return view('payroll_periods.create', compact('payrollGroups'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.create'), 403);

        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'payroll_group_scope' => 'required|in:all,selected',
            'payroll_group_ids' => 'required_if:payroll_group_scope,selected|array',
            'payroll_group_ids.*' => 'integer|exists:payroll_groups,id',
            'notes' => 'nullable|string',
        ], [
            'month.required' => 'شهر المسير مطلوب',
            'month.date_format' => 'صيغة الشهر يجب أن تكون مثل 2026-06',
            'payroll_group_scope.required' => 'نطاق مجموعات الرواتب مطلوب',
            'payroll_group_scope.in' => 'نطاق مجموعات الرواتب غير صحيح',
            'payroll_group_ids.required_if' => 'يجب اختيار مجموعة رواتب واحدة على الأقل',
            'payroll_group_ids.array' => 'مجموعات الرواتب يجب أن تكون قائمة',
            'payroll_group_ids.*.exists' => 'إحدى مجموعات الرواتب المحددة غير صحيحة',
        ]);

        if (
            !Schema::hasColumn('payroll_periods', 'payroll_group_scope') ||
            !Schema::hasTable('payroll_period_groups')
        ) {
            return back()
                ->withInput()
                ->with('error', 'يجب تشغيل Migration الخاص بدعم مجموعات الرواتب في مسير الرواتب قبل إنشاء المسير.');
        }

        $selectedGroupIds = collect($request->payroll_group_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($data['payroll_group_scope'] === 'selected' && $selectedGroupIds->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'يجب اختيار مجموعة رواتب واحدة على الأقل.');
        }

        $conflictMessage = $this->payrollGroupConflictMessage(
            $data['month'],
            $data['payroll_group_scope'],
            $selectedGroupIds->all()
        );

        if ($conflictMessage) {
            return back()
                ->withInput()
                ->with('error', $conflictMessage);
        }

        $start = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $period = PayrollPeriod::create([
            'period_number' => PayrollPeriod::generateNumber(),
            'month' => $data['month'],
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'payroll_group_scope' => $data['payroll_group_scope'],
            'status' => 'draft',
            'created_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);

        if ($data['payroll_group_scope'] === 'selected') {
            foreach ($selectedGroupIds as $groupId) {
                DB::table('payroll_period_groups')->insert([
                    'payroll_period_id' => $period->id,
                    'payroll_group_id' => $groupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('payroll-periods.show', $period)
            ->with('success', 'تم إنشاء فترة مسير الرواتب بنجاح');
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.view'), 403);

        $payrollPeriod->load([
            'items.employee.department',
            'items.components',
            'createdBy',
            'calculatedBy',
            'approvedBy',
            'paidBy',
        ]);

        $payrollGroups = $this->payrollPeriodGroups($payrollPeriod);

        return view('payroll_periods.show', compact('payrollPeriod', 'payrollGroups'));
    }

    public function calculate(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.calculate'), 403);

        if (!$payrollPeriod->can_calculate) {
            return back()->with('error', 'لا يمكن إعادة احتساب هذا المسير بعد الاعتماد أو الدفع');
        }

        if (
            ($payrollPeriod->payroll_group_scope ?? 'all') === 'selected' &&
            (!Schema::hasTable('payroll_period_groups') || $this->selectedPayrollGroupIds($payrollPeriod)->isEmpty())
        ) {
            return back()->with('error', 'لا يمكن احتساب المسير لأنه لا توجد مجموعات رواتب محددة لهذا المسير.');
        }

        DB::transaction(function () use ($payrollPeriod) {
            $payrollPeriod->items()->delete();

            $periodStart = Carbon::parse($payrollPeriod->start_date)->startOfDay();
            $periodEnd = Carbon::parse($payrollPeriod->end_date)->startOfDay();
            $periodDays = $this->inclusiveDays($periodStart, $periodEnd);

            $employees = $this->eligibleEmployees($periodStart, $periodEnd, $payrollPeriod);

            foreach ($employees as $employee) {
                $eligibility = $this->employmentEligibility($employee, $periodStart, $periodEnd);

                if (!$eligibility['eligible']) {
                    continue;
                }

                $payableDays = $eligibility['payable_days'];

                $basic = $this->prorate((float) ($employee->basic_salary ?? 0), $payableDays, $periodDays);
                $housing = $this->prorate((float) ($employee->housing_allowance ?? 0), $payableDays, $periodDays);
                $transport = $this->prorate((float) ($employee->transport_allowance ?? 0), $payableDays, $periodDays);
                $food = $this->prorate((float) ($employee->food_allowance ?? 0), $payableDays, $periodDays);
                $other = $this->prorate((float) ($employee->other_allowance ?? 0), $payableDays, $periodDays);

                $gross = round($basic + $housing + $transport + $food + $other, 2);
                $dailySalary = $payableDays > 0 ? round($gross / $payableDays, 6) : 0;

                [$regularDeductions, $deductionComponents] = $this->calculateRegularDeductions($employee, $gross, $payrollPeriod);
                [$advanceDeductions, $advanceComponents] = $this->calculateSalaryAdvances($employee, $payrollPeriod);
                [$suspensionDeductions, $suspendedDays, $suspensionComponents] = $this->calculateSuspensions(
                    $employee,
                    $dailySalary,
                    $payrollPeriod,
                    Carbon::parse($eligibility['eligible_start_date']),
                    Carbon::parse($eligibility['eligible_end_date'])
                );

                [$unpaidLeaveDeductions, $unpaidLeaveDays, $leaveComponents] = $this->calculateUnpaidLeaves(
                    $employee,
                    $dailySalary,
                    $payrollPeriod,
                    Carbon::parse($eligibility['eligible_start_date']),
                    Carbon::parse($eligibility['eligible_end_date'])
                );

                $totalDeductions = round(
                    $regularDeductions
                    + $advanceDeductions
                    + $suspensionDeductions
                    + $unpaidLeaveDeductions,
                    2
                );

                $netSalary = max(0, round($gross - $totalDeductions, 2));

                $item = PayrollItem::create([
                    'payroll_period_id' => $payrollPeriod->id,
                    'employee_id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                    'employee_name' => $employee->display_name,

                    'eligible_start_date' => $eligibility['eligible_start_date'],
                    'eligible_end_date' => $eligibility['eligible_end_date'],

                    'basic_salary' => $basic,
                    'housing_allowance' => $housing,
                    'transport_allowance' => $transport,
                    'food_allowance' => $food,
                    'other_allowance' => $other,
                    'gross_salary' => $gross,

                    'regular_deductions' => $regularDeductions,
                    'salary_advance_deductions' => $advanceDeductions,
                    'suspension_deductions' => $suspensionDeductions,
                    'unpaid_leave_deductions' => $unpaidLeaveDeductions,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $netSalary,

                    'period_days' => $periodDays,
                    'payable_days' => $payableDays,
                    'suspended_days' => $suspendedDays,
                    'unpaid_leave_days' => $unpaidLeaveDays,
                    'employment_status_note' => $eligibility['note'],

                    'status' => 'calculated',
                ]);

                $components = array_merge(
                    $this->earningComponents($basic, $housing, $transport, $food, $other),
                    $deductionComponents,
                    $advanceComponents,
                    $suspensionComponents,
                    $leaveComponents
                );

                foreach ($components as $component) {
                    $component['payroll_item_id'] = $item->id;
                    $component['payroll_period_id'] = $payrollPeriod->id;
                    $component['employee_id'] = $employee->id;

                    PayrollItemComponent::create($component);
                }
            }

            $this->refreshTotals($payrollPeriod);

            $payrollPeriod->update([
                'status' => 'calculated',
                'calculated_by' => auth()->id(),
                'calculated_at' => now(),
            ]);
        });

        return redirect()
            ->route('payroll-periods.show', $payrollPeriod)
            ->with('success', 'تم احتساب مسير الرواتب حسب حالات الموظفين بنجاح');
    }

    public function approve(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.approve'), 403);

        if (!$payrollPeriod->can_approve) {
            return back()->with('error', 'لا يمكن اعتماد هذا المسير في حالته الحالية');
        }

        $payrollPeriod->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $payrollPeriod->items()->update(['status' => 'approved']);

        return back()->with('success', 'تم اعتماد مسير الرواتب بنجاح');
    }

    public function markAsPaid(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.pay'), 403);

        if (!$payrollPeriod->can_pay) {
            return back()->with('error', 'لا يمكن صرف هذا المسير في حالته الحالية');
        }

        DB::transaction(function () use ($payrollPeriod) {
            $payrollPeriod->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            $payrollPeriod->items()->update(['status' => 'paid']);

            $this->markSalaryAdvanceInstallmentsAsDeducted($payrollPeriod);
            $this->markCompletedDeductions($payrollPeriod);
        });

        return back()->with('success', 'تم صرف مسير الرواتب وتحديث أقساط السلف والاستقطاعات بنجاح');
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_periods.delete'), 403);

        if ($payrollPeriod->status === 'paid') {
            return back()->with('error', 'لا يمكن حذف مسير مدفوع');
        }

        $payrollPeriod->delete();

        return redirect()
            ->route('payroll-periods.index')
            ->with('success', 'تم حذف فترة المسير بنجاح');
    }

    private function eligibleEmployees(Carbon $periodStart, Carbon $periodEnd, PayrollPeriod $payrollPeriod)
    {
        /*
         * مهم:
         * لا نعتمد هنا على فلترة SQL بتواريخ التعيين/نهاية الخدمة فقط؛
         * لأن أسماء الأعمدة قد تختلف أو تكون بعض القيم غير مهيأة.
         * لذلك نجلب الموظفين ضمن نطاق مجموعة الرواتب ثم نفلتر فعليًا داخل employmentEligibility().
         */
        $query = Employee::query()
            ->where(function ($query) {
                $query->whereNull('payroll_status')
                    ->orWhere('payroll_status', 'included');
            })
            ->orderBy('full_name');

        if (($payrollPeriod->payroll_group_scope ?? 'all') === 'selected') {
            $selectedGroupIds = $this->selectedPayrollGroupIds($payrollPeriod);

            if ($selectedGroupIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('payroll_group_id', $selectedGroupIds->all());
            }
        }

        return $query->get()
            ->filter(function (Employee $employee) use ($periodStart, $periodEnd) {
                return $this->employmentEligibility($employee, $periodStart, $periodEnd)['eligible'];
            })
            ->values();
    }


    private function payrollGroupConflictMessage(string $month, string $scope, array $selectedGroupIds): ?string
    {
        if ($scope === 'all') {
            $exists = PayrollPeriod::where('month', $month)->exists();

            return $exists
                ? 'لا يمكن إنشاء مسير شامل لكل المجموعات لأن هناك مسير رواتب موجود لنفس الشهر.'
                : null;
        }

        $allPeriodExists = PayrollPeriod::where('month', $month)
            ->where('payroll_group_scope', 'all')
            ->exists();

        if ($allPeriodExists) {
            return 'لا يمكن إنشاء مسير لمجموعات محددة لأن هناك مسير شامل لكل المجموعات في نفس الشهر.';
        }

        $conflictingGroupIds = DB::table('payroll_period_groups')
            ->join('payroll_periods', 'payroll_period_groups.payroll_period_id', '=', 'payroll_periods.id')
            ->where('payroll_periods.month', $month)
            ->whereIn('payroll_period_groups.payroll_group_id', $selectedGroupIds)
            ->pluck('payroll_period_groups.payroll_group_id')
            ->unique()
            ->values();

        if ($conflictingGroupIds->isEmpty()) {
            return null;
        }

        $names = PayrollGroup::whereIn('id', $conflictingGroupIds)
            ->orderBy('name_ar')
            ->pluck('name_ar')
            ->implode('، ');

        return 'يوجد مسير رواتب سابق لنفس الشهر للمجموعات التالية: ' . $names;
    }

    private function selectedPayrollGroupIds(PayrollPeriod $payrollPeriod)
    {
        if (!Schema::hasTable('payroll_period_groups')) {
            return collect();
        }

        return DB::table('payroll_period_groups')
            ->where('payroll_period_id', $payrollPeriod->id)
            ->pluck('payroll_group_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function payrollPeriodGroups(PayrollPeriod $payrollPeriod)
    {
        $ids = $this->selectedPayrollGroupIds($payrollPeriod);

        if ($ids->isEmpty()) {
            return collect();
        }

        return PayrollGroup::whereIn('id', $ids)
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();
    }

    private function employmentEligibility(Employee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        /*
         * نقرأ تاريخ بداية ونهاية خدمة الموظف بطريقة مرنة.
         * الأساسي غالبًا: hire_date و termination_date.
         * وأضفنا بدائل حتى لو كانت الأعمدة عندك باسم مختلف.
         */
        $hireDate = $this->employeeDateValue($employee, [
            'hire_date',
            'joining_date',
            'join_date',
            'start_date',
            'employment_start_date',
            'work_start_date',
            'started_at',
        ]);

        $terminationDate = $this->employeeDateValue($employee, [
            'termination_date',
            'end_of_service_date',
            'employment_end_date',
            'work_end_date',
            'last_working_day',
            'last_working_date',
            'resignation_date',
        ]);

        if ($employee->payroll_status === 'excluded') {
            return [
                'eligible' => false,
                'payable_days' => 0,
                'eligible_start_date' => null,
                'eligible_end_date' => null,
                'note' => 'مستبعد من مسير الرواتب',
            ];
        }

        if ($hireDate && $hireDate->gt($periodEnd)) {
            return [
                'eligible' => false,
                'payable_days' => 0,
                'eligible_start_date' => null,
                'eligible_end_date' => null,
                'note' => 'تاريخ التعيين بعد نهاية فترة المسير',
            ];
        }

        if ($terminationDate && $terminationDate->lt($periodStart)) {
            return [
                'eligible' => false,
                'payable_days' => 0,
                'eligible_start_date' => null,
                'eligible_end_date' => null,
                'note' => 'انتهت خدمته قبل بداية فترة المسير',
            ];
        }

        if (
            in_array((string) ($employee->status ?? ''), ['terminated', 'resigned', 'inactive'], true)
            && !$terminationDate
        ) {
            return [
                'eligible' => false,
                'payable_days' => 0,
                'eligible_start_date' => null,
                'eligible_end_date' => null,
                'note' => 'حالة الموظف غير نشطة ولا يوجد تاريخ نهاية خدمة',
            ];
        }

        $eligibleStart = $hireDate && $hireDate->gt($periodStart)
            ? $hireDate->copy()
            : $periodStart->copy();

        $eligibleEnd = $terminationDate && $terminationDate->lt($periodEnd)
            ? $terminationDate->copy()
            : $periodEnd->copy();

        if ($eligibleEnd->lt($eligibleStart)) {
            return [
                'eligible' => false,
                'payable_days' => 0,
                'eligible_start_date' => null,
                'eligible_end_date' => null,
                'note' => 'لا توجد أيام مستحقة داخل الفترة',
            ];
        }

        $days = $this->inclusiveDays($eligibleStart, $eligibleEnd);

        $note = 'نشط طوال الفترة';

        if ($hireDate && $hireDate->betweenIncluded($periodStart, $periodEnd)) {
            $note = 'تم تعيينه داخل فترة المسير';
        }

        if ($terminationDate && $terminationDate->betweenIncluded($periodStart, $periodEnd)) {
            $note = 'تم إنهاء خدمته داخل فترة المسير';
        }

        if ($hireDate && $hireDate->betweenIncluded($periodStart, $periodEnd) && $terminationDate && $terminationDate->betweenIncluded($periodStart, $periodEnd)) {
            $note = 'تعيين وإنهاء خدمة داخل فترة المسير';
        }

        if (($employee->status ?? null) === 'suspended') {
            $note = 'موظف موقوف خلال الفترة';
        }

        if (($employee->status ?? null) === 'on_leave') {
            $note = 'موظف في إجازة خلال الفترة';
        }

        return [
            'eligible' => true,
            'payable_days' => $days,
            'eligible_start_date' => $eligibleStart->toDateString(),
            'eligible_end_date' => $eligibleEnd->toDateString(),
            'note' => $note,
        ];
    }


    private function employeeDateValue(Employee $employee, array $columns): ?Carbon
    {
        foreach ($columns as $column) {
            $value = $employee->getAttribute($column);

            if (!$value) {
                continue;
            }

            try {
                return Carbon::parse($value)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    private function inclusiveDays(Carbon $start, Carbon $end): int
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        /*
         * لا نستخدم endOfDay مع diffInDays لأن ذلك قد يعطي نتيجة غير صحيحة
         * مثل 32 يوم لشهر مكون من 31 يوم.
         */
        return (int) $start->diffInDays($end) + 1;
    }

    private function prorate(float $amount, int $payableDays, int $periodDays): float
    {
        if ($amount <= 0 || $payableDays <= 0 || $periodDays <= 0) {
            return 0;
        }

        return round($amount * ($payableDays / $periodDays), 2);
    }

    private function earningComponents(float $basic, float $housing, float $transport, float $food, float $other): array
    {
        $components = [];

        foreach ([
                     'الراتب الأساسي المستحق' => $basic,
                     'بدل السكن المستحق' => $housing,
                     'بدل النقل المستحق' => $transport,
                     'بدل الطعام المستحق' => $food,
                     'بدلات أخرى مستحقة' => $other,
                 ] as $name => $amount) {
            if ($amount > 0) {
                $components[] = [
                    'type' => 'earning',
                    'name' => $name,
                    'amount' => round($amount, 2),
                ];
            }
        }

        return $components;
    }

    private function calculateRegularDeductions(Employee $employee, float $gross, PayrollPeriod $period): array
    {
        /*
         * النظام الجديد للاستقطاعات:
         * نقرأ من جدول employee_deduction_schedules.
         * كل سجل يمثل خصم شهر محدد، وهذا يمنع تكرار الخصم ويدعم:
         * مرة واحدة / شهري / أشهر محددة / أقساط / نسبة.
         */
        if (Schema::hasTable('employee_deduction_schedules')) {
            $schedules = EmployeeDeductionSchedule::query()
                ->where('employee_id', $employee->id)
                ->where('payroll_month', $period->month)
                ->where('status', 'pending')
                ->with('deduction')
                ->get();

            $total = 0;
            $components = [];

            foreach ($schedules as $schedule) {
                $deduction = $schedule->deduction;

                if (!$deduction || !in_array($deduction->status, ['approved', 'active'], true)) {
                    continue;
                }

                $amount = 0;

                if (($deduction->calculation_type ?? null) === 'percentage' || $schedule->percentage !== null) {
                    $percentage = (float) ($schedule->percentage ?? $deduction->percentage ?? $deduction->amount ?? 0);
                    $amount = round($gross * ($percentage / 100), 2);
                } else {
                    $amount = round((float) $schedule->amount, 2);
                }

                if ($amount <= 0) {
                    continue;
                }

                $total += $amount;

                $components[] = [
                    'type' => 'deduction',
                    'name' => 'استقطاع: ' . ($deduction->title ?: $deduction->deduction_type ?: $deduction->deduction_number),
                    'amount' => $amount,
                    'source_type' => EmployeeDeductionSchedule::class,
                    'source_id' => $schedule->id,
                    'notes' => $deduction->reason,
                ];
            }

            return [round($total, 2), $components];
        }

        /*
         * توافق احتياطي مع النظام القديم إذا لم يتم تشغيل migration الجديد بعد.
         */
        $periodStart = Carbon::parse($period->start_date)->startOfDay();
        $periodEnd = Carbon::parse($period->end_date)->endOfDay();

        $deductions = EmployeeDeduction::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->where(function ($query) use ($periodStart) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $periodStart->toDateString());
            })
            ->get();

        $total = 0;
        $components = [];

        foreach ($deductions as $deduction) {
            $amount = 0;

            if ($deduction->deduction_mode === 'one_time') {
                if (!Carbon::parse($deduction->start_date)->betweenIncluded($periodStart, $periodEnd)) {
                    continue;
                }

                $alreadyApplied = PayrollItemComponent::query()
                    ->where('source_type', EmployeeDeduction::class)
                    ->where('source_id', $deduction->id)
                    ->exists();

                if ($alreadyApplied) {
                    continue;
                }

                $amount = (float) $deduction->amount;
            }

            if ($deduction->deduction_mode === 'monthly') {
                $amount = (float) ($deduction->monthly_amount ?: $deduction->amount);
            }

            if ($deduction->deduction_mode === 'percentage') {
                $amount = round($gross * ((float) $deduction->amount / 100), 2);
            }

            if ($deduction->deduction_mode === 'installments') {
                $appliedCount = PayrollItemComponent::query()
                    ->where('source_type', EmployeeDeduction::class)
                    ->where('source_id', $deduction->id)
                    ->count();

                if ($deduction->installments_count && $appliedCount >= (int) $deduction->installments_count) {
                    continue;
                }

                $alreadyPaid = (float) PayrollItemComponent::query()
                    ->where('source_type', EmployeeDeduction::class)
                    ->where('source_id', $deduction->id)
                    ->sum('amount');

                $remaining = max(0, (float) $deduction->amount - $alreadyPaid);
                $monthly = (float) ($deduction->monthly_amount ?: ((float) $deduction->amount / max(1, (int) $deduction->installments_count)));

                $amount = min($remaining, round($monthly, 2));
            }

            $amount = round(max(0, $amount), 2);

            if ($amount <= 0) {
                continue;
            }

            $total += $amount;

            $components[] = [
                'type' => 'deduction',
                'name' => 'استقطاع: ' . $deduction->deduction_type,
                'amount' => $amount,
                'source_type' => EmployeeDeduction::class,
                'source_id' => $deduction->id,
                'notes' => $deduction->reason,
            ];
        }

        return [round($total, 2), $components];
    }


    private function calculateSalaryAdvances(Employee $employee, PayrollPeriod $period): array
    {
        $installments = SalaryAdvanceInstallment::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->whereDate('due_date', '>=', $period->start_date)
            ->whereDate('due_date', '<=', $period->end_date)
            ->whereHas('salaryAdvance', function ($query) {
                $query->where('status', 'approved');
            })
            ->with('salaryAdvance')
            ->get();

        $total = 0;
        $components = [];

        foreach ($installments as $installment) {
            $amount = round((float) $installment->amount, 2);
            $total += $amount;

            $components[] = [
                'type' => 'salary_advance',
                'name' => 'قسط سلفة: ' . ($installment->salaryAdvance->advance_number ?? '-'),
                'amount' => $amount,
                'source_type' => SalaryAdvanceInstallment::class,
                'source_id' => $installment->id,
                'notes' => 'قسط رقم ' . $installment->installment_number,
            ];
        }

        return [round($total, 2), $components];
    }

    private function calculateSuspensions(Employee $employee, float $dailySalary, PayrollPeriod $period, Carbon $eligibleStart, Carbon $eligibleEnd): array
    {
        $suspensions = EmployeeSuspension::query()
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['active', 'resumed'])
            ->whereDate('start_date', '<=', $eligibleEnd->toDateString())
            ->where(function ($query) use ($eligibleStart) {
                $query->whereNull('resume_date')
                    ->orWhereDate('resume_date', '>=', $eligibleStart->toDateString());
            })
            ->get();

        $total = 0;
        $days = 0;
        $components = [];

        foreach ($suspensions as $suspension) {
            $start = Carbon::parse($suspension->start_date)->startOfDay()->max($eligibleStart);

            $effectiveEnd = $suspension->resume_date
                ? Carbon::parse($suspension->resume_date)->subDay()->startOfDay()
                : $eligibleEnd->copy();

            $end = $effectiveEnd->min($eligibleEnd);

            if ($end->lt($start)) {
                continue;
            }

            $suspensionDays = $this->inclusiveDays($start, $end);
            $unpaidPercentage = max(0, 100 - (float) $suspension->salary_percentage);
            $amount = round($dailySalary * $suspensionDays * ($unpaidPercentage / 100), 2);

            if ($amount <= 0) {
                continue;
            }

            $days += $suspensionDays;
            $total += $amount;

            $components[] = [
                'type' => 'suspension',
                'name' => 'خصم إيقاف موظف',
                'amount' => $amount,
                'source_type' => EmployeeSuspension::class,
                'source_id' => $suspension->id,
                'notes' => 'أيام الإيقاف: ' . $suspensionDays . ' - نسبة الراتب أثناء الإيقاف: ' . $suspension->salary_percentage . '%',
            ];
        }

        return [round($total, 2), $days, $components];
    }

    private function calculateUnpaidLeaves(Employee $employee, float $dailySalary, PayrollPeriod $period, Carbon $eligibleStart, Carbon $eligibleEnd): array
    {
        $query = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'hr_approved'])
            ->whereDate('start_date', '<=', $eligibleEnd->toDateString())
            ->whereDate('end_date', '>=', $eligibleStart->toDateString());

        if (method_exists(LeaveRequest::class, 'leaveType')) {
            $query->with('leaveType');
        }

        $leaves = $query->get();

        $total = 0;
        $days = 0;
        $components = [];

        foreach ($leaves as $leave) {
            $salaryPercentage = $this->leaveSalaryPercentage($leave);

            if ($salaryPercentage >= 100) {
                continue;
            }

            $start = Carbon::parse($leave->start_date)->startOfDay()->max($eligibleStart);
            $end = Carbon::parse($leave->end_date)->startOfDay()->min($eligibleEnd);

            if ($end->lt($start)) {
                continue;
            }

            $leaveDays = $this->inclusiveDays($start, $end);
            $unpaidPercentage = max(0, 100 - $salaryPercentage);
            $amount = round($dailySalary * $leaveDays * ($unpaidPercentage / 100), 2);

            if ($amount <= 0) {
                continue;
            }

            $days += $leaveDays;
            $total += $amount;

            $components[] = [
                'type' => 'deduction',
                'name' => 'خصم إجازة غير مدفوعة',
                'amount' => $amount,
                'source_type' => LeaveRequest::class,
                'source_id' => $leave->id,
                'notes' => 'أيام الإجازة: ' . $leaveDays . ' - نسبة الراتب أثناء الإجازة: ' . $salaryPercentage . '%',
            ];
        }

        return [round($total, 2), $days, $components];
    }

    private function leaveSalaryPercentage(LeaveRequest $leave): float
    {
        $leaveType = null;

        try {
            if ($leave->relationLoaded('leaveType')) {
                $leaveType = $leave->leaveType;
            }
        } catch (\Throwable $e) {
            $leaveType = null;
        }

        if ($leaveType) {
            $affectsPayroll = (bool) ($leaveType->affects_payroll ?? false);

            if (!$affectsPayroll) {
                return 100;
            }

            if ($leaveType->salary_percentage !== null) {
                return max(0, min(100, (float) $leaveType->salary_percentage));
            }
        }

        $name = mb_strtolower((string) ($leave->leave_type ?? ''));

        if (
            str_contains($name, 'بدون راتب') ||
            str_contains($name, 'غير مدفوعة') ||
            str_contains($name, 'غياب') ||
            str_contains($name, 'unpaid')
        ) {
            return 0;
        }

        return 100;
    }

    private function refreshTotals(PayrollPeriod $period): void
    {
        $period->refresh();

        $period->update([
            'employees_count' => $period->items()->count(),
            'total_gross_salary' => $period->items()->sum('gross_salary'),
            'total_regular_deductions' => $period->items()->sum('regular_deductions'),
            'total_salary_advances' => $period->items()->sum('salary_advance_deductions'),
            'total_suspension_deductions' => $period->items()->sum('suspension_deductions') + $period->items()->sum('unpaid_leave_deductions'),
            'total_deductions' => $period->items()->sum('total_deductions'),
            'total_net_salary' => $period->items()->sum('net_salary'),
        ]);
    }

    private function markSalaryAdvanceInstallmentsAsDeducted(PayrollPeriod $period): void
    {
        $components = PayrollItemComponent::query()
            ->where('payroll_period_id', $period->id)
            ->where('source_type', SalaryAdvanceInstallment::class)
            ->get();

        foreach ($components as $component) {
            $installment = SalaryAdvanceInstallment::find($component->source_id);

            if (!$installment || $installment->status !== 'pending') {
                continue;
            }

            $installment->update([
                'status' => 'deducted',
                'payroll_period_id' => $period->id,
                'payroll_item_id' => $component->payroll_item_id,
                'deducted_date' => now()->toDateString(),
            ]);

            $advance = $installment->salaryAdvance;

            if ($advance && !$advance->installments()->where('status', 'pending')->exists()) {
                $advance->update(['status' => 'completed']);
            }
        }
    }

    private function markCompletedDeductions(PayrollPeriod $period): void
    {
        /*
         * النظام الجديد:
         * عند صرف المسير نحدث جدول employee_deduction_schedules
         * من pending إلى deducted، ونربطه بالمسير وبسطر الراتب.
         */
        if (Schema::hasTable('employee_deduction_schedules')) {
            $scheduleComponents = PayrollItemComponent::query()
                ->where('payroll_period_id', $period->id)
                ->where('source_type', EmployeeDeductionSchedule::class)
                ->get();

            foreach ($scheduleComponents as $component) {
                $schedule = EmployeeDeductionSchedule::with('deduction')->find($component->source_id);

                if (!$schedule || $schedule->status !== 'pending') {
                    continue;
                }

                $schedule->update([
                    'status' => 'deducted',
                    'payroll_period_id' => $period->id,
                    'payroll_item_id' => $component->payroll_item_id,
                    'deducted_at' => now(),
                ]);

                $deduction = $schedule->deduction;

                if ($deduction && !$deduction->schedules()->where('status', 'pending')->exists()) {
                    $deduction->update(['status' => 'completed']);
                }
            }

            return;
        }

        /*
         * توافق احتياطي مع النظام القديم.
         */
        $deductionIds = PayrollItemComponent::query()
            ->where('payroll_period_id', $period->id)
            ->where('source_type', EmployeeDeduction::class)
            ->pluck('source_id')
            ->unique()
            ->filter();

        foreach ($deductionIds as $deductionId) {
            $deduction = EmployeeDeduction::find($deductionId);

            if (!$deduction || $deduction->status !== 'approved') {
                continue;
            }

            if ($deduction->deduction_mode === 'one_time') {
                $deduction->update(['status' => 'completed']);
                continue;
            }

            if ($deduction->deduction_mode === 'installments') {
                $appliedCount = PayrollItemComponent::query()
                    ->where('source_type', EmployeeDeduction::class)
                    ->where('source_id', $deduction->id)
                    ->count();

                if ($deduction->installments_count && $appliedCount >= (int) $deduction->installments_count) {
                    $deduction->update(['status' => 'completed']);
                }
            }

            if ($deduction->deduction_mode === 'monthly' && $deduction->end_date && Carbon::parse($deduction->end_date)->lte(Carbon::parse($period->end_date))) {
                $deduction->update(['status' => 'completed']);
            }
        }
    }

}

