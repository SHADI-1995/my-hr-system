<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceRequest extends Model
{
    protected $fillable = [
        'request_number',
        'employee_id',
        'created_by',
        'amount',
        'approved_amount',
        'installments_count',
        'installment_amount',
        'deduction_start_date',
        'reason',
        'notes',
        'attachment',
        'status',
        'workflow_status',
        'direct_manager_status',
        'direct_manager_approved_by',
        'direct_manager_approved_at',
        'direct_manager_rejected_by',
        'direct_manager_rejected_at',
        'direct_manager_reject_reason',
        'direct_manager_note',
        'hr_status',
        'hr_approved_by',
        'hr_approved_at',
        'hr_rejected_by',
        'hr_rejected_at',
        'hr_reject_reason',
        'hr_note',
        'registered_salary_advance_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'deduction_start_date' => 'date',
        'direct_manager_approved_at' => 'datetime',
        'direct_manager_rejected_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'hr_rejected_at' => 'datetime',
    ];

    public static function generateNumber(): string
    {
        $last = static::query()
            ->where('request_number', 'like', 'ADVREQ-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/ADVREQ-(\d+)/', (string) $last->request_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'ADVREQ-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function directManagerApprovedBy()
    {
        return $this->belongsTo(User::class, 'direct_manager_approved_by');
    }

    public function directManagerRejectedBy()
    {
        return $this->belongsTo(User::class, 'direct_manager_rejected_by');
    }

    public function hrApprovedBy()
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    public function hrRejectedBy()
    {
        return $this->belongsTo(User::class, 'hr_rejected_by');
    }

    public function registeredSalaryAdvance()
    {
        return $this->belongsTo(SalaryAdvance::class, 'registered_salary_advance_id');
    }

    public function logs()
    {
        return $this->hasMany(SalaryAdvanceRequestLog::class)->latest();
    }

    public function addLog(string $type, ?string $oldStatus, ?string $newStatus, string $description, array $meta = []): void
    {
        $this->logs()->create([
            'user_id' => auth()->id(),
            'transaction_type' => $type,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }

    public function getWorkflowStatusTextAttribute(): string
    {
        return match ($this->workflow_status) {
            'pending_manager' => 'بانتظار موافقة المدير المباشر',
            'manager_approved_pending_hr' => 'بانتظار موافقة الموارد البشرية',
            'rejected_by_manager' => 'مرفوض من المدير المباشر',
            'approved_by_hr' => 'معتمد من الموارد البشرية',
            'rejected_by_hr' => 'مرفوض من الموارد البشرية',
            'registered' => 'تم تسجيل السلفة',
            'cancelled' => 'ملغي',
            default => $this->workflow_status ?? '-',
        };
    }

    public function getCanEmployeeCancelAttribute(): bool
    {
        return $this->workflow_status === 'pending_manager';
    }

    public function getCanManagerApproveAttribute(): bool
    {
        return $this->workflow_status === 'pending_manager'
            && $this->direct_manager_status === 'pending'
            && $this->status === 'pending';
    }

    public function getCanHrApproveAttribute(): bool
    {
        return $this->workflow_status === 'manager_approved_pending_hr'
            && $this->direct_manager_status === 'approved'
            && $this->hr_status === 'pending'
            && $this->status === 'pending';
    }
}
