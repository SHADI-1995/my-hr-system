<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'employee_id',

        // اختياري للمرحلة القادمة: نوع الاستقطاع الديناميكي
        'deduction_type_id',

        'deduction_number',
        'deduction_type',
        'title',

        /*
         * deduction_mode:
         * one_time        = مرة واحدة
         * monthly         = كل شهر
         * selected_months = أشهر محددة
         * installments    = أقساط
         * percentage      = نسبة من الراتب
         */
        'deduction_mode',

        /*
         * calculation_type:
         * fixed      = مبلغ ثابت
         * percentage = نسبة
         */
        'calculation_type',

        'amount',
        'percentage',
        'total_amount',
        'installments_count',
        'monthly_amount',

        // حقول التوافق القديمة
        'start_date',
        'end_date',

        // الحقول الشهرية الجديدة
        'start_month',
        'end_month',
        'selected_months',

        'status',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'installments_count' => 'integer',
        'selected_months' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($deduction) {
            if (empty($deduction->deduction_number)) {
                $deduction->deduction_number = static::generateNumber();
            }

            if (empty($deduction->status)) {
                $deduction->status = 'pending';
            }

            if (empty($deduction->calculation_type)) {
                $deduction->calculation_type = $deduction->deduction_mode === 'percentage'
                    ? 'percentage'
                    : 'fixed';
            }

            if (empty($deduction->start_month) && !empty($deduction->start_date)) {
                $deduction->start_month = Carbon::parse($deduction->start_date)->format('Y-m');
            }

            if (empty($deduction->end_month) && !empty($deduction->end_date)) {
                $deduction->end_month = Carbon::parse($deduction->end_date)->format('Y-m');
            }

            /*
             * عند اختيار نوع استقطاع من جدول deduction_types
             * ننسخ الاسم إلى الحقل النصي القديم deduction_type للتوافق مع التقارير والصفحات القديمة.
             */
            if (
                empty($deduction->deduction_type) &&
                !empty($deduction->deduction_type_id) &&
                Schema::hasTable('deduction_types')
            ) {
                $typeName = DeductionType::query()
                    ->where('id', $deduction->deduction_type_id)
                    ->value('name_ar');

                if ($typeName) {
                    $deduction->deduction_type = $typeName;
                }
            }

            if (empty($deduction->title)) {
                $deduction->title = $deduction->deduction_type;
            }

            if (empty($deduction->total_amount)) {
                $deduction->total_amount = $deduction->amount ?? 0;
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /*
     * يحتاج موديل DeductionType وجدول deduction_types إذا فعّلنا أنواع الاستقطاعات الديناميكية.
     */
    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class, 'deduction_type_id');
    }

    public function getDeductionTypeNameAttribute(): string
    {
        if ($this->relationLoaded('deductionType') && $this->deductionType) {
            return $this->deductionType->name_ar;
        }

        if ($this->deductionType) {
            return $this->deductionType->name_ar;
        }

        return $this->deduction_type ?: '-';
    }

    /*
     * جدول جدولة الاستقطاعات على الأشهر.
     * كل سطر يمثل خصم شهر محدد.
     */
    public function schedules()
    {
        return $this->hasMany(EmployeeDeductionSchedule::class);
    }

    public function pendingSchedules()
    {
        return $this->hasMany(EmployeeDeductionSchedule::class)
            ->where('status', 'pending');
    }

    public function deductedSchedules()
    {
        return $this->hasMany(EmployeeDeductionSchedule::class)
            ->where('status', 'deducted');
    }

    public function cancelledSchedules()
    {
        return $this->hasMany(EmployeeDeductionSchedule::class)
            ->where('status', 'cancelled');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public static function generateNumber(): string
    {
        $last = static::query()
            ->where('deduction_number', 'like', 'DED-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/DED-(\d+)/', (string) $last->deduction_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'DED-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function getDeductionModeNameAttribute(): string
    {
        return match ($this->deduction_mode) {
            'one_time' => 'مرة واحدة',
            'monthly' => 'كل شهر',
            'selected_months' => 'أشهر محددة',
            'installments' => 'أقساط',
            'percentage' => 'نسبة من الراتب',
            default => $this->deduction_mode ?: '-',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'بانتظار الاعتماد',
            'approved' => 'معتمد',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status ?: '-',
        };
    }

    public function getRemainingAmountAttribute(): float
    {
        $deducted = $this->schedules()
            ->where('status', 'deducted')
            ->sum('amount');

        return max(0, round((float) ($this->total_amount ?? $this->amount ?? 0) - (float) $deducted, 2));
    }

    public function getDeductedAmountAttribute(): float
    {
        return round((float) $this->schedules()
            ->where('status', 'deducted')
            ->sum('amount'), 2);
    }

    public function getProgressPercentageAttribute(): float
    {
        $total = (float) ($this->total_amount ?? $this->amount ?? 0);

        if ($total <= 0) {
            return 0;
        }

        return round(($this->deducted_amount / $total) * 100, 2);
    }

    /*
     * توليد جدول الخصومات الشهرية حسب طريقة الاستقطاع.
     * يفضل استدعاؤها من الكنترولر بعد اعتماد الاستقطاع داخل Transaction.
     */
    public function generateSchedules(bool $replaceExisting = true): void
    {
        if ($replaceExisting) {
            $this->schedules()
                ->where('status', 'pending')
                ->delete();
        }

        $months = $this->scheduleMonths();

        if (empty($months)) {
            return;
        }

        $amounts = $this->scheduleAmounts(count($months));

        foreach ($months as $index => $month) {
            $amount = $amounts[$index] ?? 0;

            if ($amount <= 0 && $this->calculation_type !== 'percentage') {
                continue;
            }

            $this->schedules()->updateOrCreate(
                [
                    'employee_deduction_id' => $this->id,
                    'payroll_month' => $month,
                ],
                [
                    'employee_id' => $this->employee_id,
                    'amount' => $amount,
                    'percentage' => $this->calculation_type === 'percentage'
                        ? ($this->percentage ?? $this->amount)
                        : null,
                    'status' => 'pending',
                    'notes' => $this->reason,
                ]
            );
        }
    }

    private function scheduleMonths(): array
    {
        return match ($this->deduction_mode) {
            'one_time' => [$this->start_month ?: optional($this->start_date)->format('Y-m') ?: now()->format('Y-m')],
            'monthly' => $this->monthsBetween($this->start_month, $this->end_month),
            'selected_months' => $this->selected_months ?: [],
            'installments' => $this->installmentMonths(),
            'percentage' => $this->end_month
                ? $this->monthsBetween($this->start_month ?: optional($this->start_date)->format('Y-m') ?: now()->format('Y-m'), $this->end_month)
                : [$this->start_month ?: optional($this->start_date)->format('Y-m') ?: now()->format('Y-m')],
            default => [$this->start_month ?: optional($this->start_date)->format('Y-m') ?: now()->format('Y-m')],
        };
    }

    private function scheduleAmounts(int $monthsCount): array
    {
        if ($monthsCount <= 0) {
            return [];
        }

        if ($this->calculation_type === 'percentage' || $this->deduction_mode === 'percentage') {
            return array_fill(0, $monthsCount, 0);
        }

        if ($this->deduction_mode === 'installments') {
            $total = (float) ($this->total_amount ?: $this->amount);
            $installments = max(1, (int) ($this->installments_count ?: $monthsCount));
            $monthly = (float) ($this->monthly_amount ?: round($total / $installments, 2));

            $amounts = array_fill(0, $monthsCount, $monthly);

            $sumBeforeLast = array_sum(array_slice($amounts, 0, -1));
            $amounts[$monthsCount - 1] = round(max(0, $total - $sumBeforeLast), 2);

            return $amounts;
        }

        $amount = (float) ($this->monthly_amount ?: $this->amount);

        return array_fill(0, $monthsCount, round($amount, 2));
    }

    private function installmentMonths(): array
    {
        $startMonth = $this->start_month ?: optional($this->start_date)->format('Y-m') ?: now()->format('Y-m');
        $count = max(1, (int) ($this->installments_count ?: 1));

        $months = [];
        $date = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();

        for ($i = 0; $i < $count; $i++) {
            $months[] = $date->copy()->addMonths($i)->format('Y-m');
        }

        return $months;
    }

    private function monthsBetween(?string $startMonth, ?string $endMonth): array
    {
        if (!$startMonth) {
            return [];
        }

        $start = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $end = $endMonth
            ? Carbon::createFromFormat('Y-m', $endMonth)->startOfMonth()
            : $start->copy();

        if ($end->lt($start)) {
            return [];
        }

        $months = [];

        while ($start->lte($end)) {
            $months[] = $start->format('Y-m');
            $start->addMonth();
        }

        return $months;
    }
}
