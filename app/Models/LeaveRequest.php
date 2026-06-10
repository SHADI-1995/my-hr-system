<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class LeaveRequest extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',
        'leave_type_id',

        'start_date',
        'end_date',
        'days_count',

        // الحالة القديمة العامة
        'status',

        // حالات مسار الموافقة الجديد
        'workflow_status',

        'direct_manager_status',
        'direct_manager_approved_by',
        'direct_manager_approved_at',
        'direct_manager_rejected_by',
        'direct_manager_rejected_at',
        'direct_manager_reject_reason',

        'hr_status',
        'hr_approved_by',
        'hr_approved_at',
        'hr_rejected_by',
        'hr_rejected_at',
        'hr_reject_reason',

        'reason',

        // الحقول القديمة للاعتماد والرفض
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'reject_reason',

        'attachment',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',

        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',

        'direct_manager_approved_at' => 'datetime',
        'direct_manager_rejected_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'hr_rejected_at' => 'datetime',

        'days_count' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * المستخدم الذي وافق كمدير مباشر.
     */
    public function directManagerApprovedBy()
    {
        return $this->belongsTo(User::class, 'direct_manager_approved_by');
    }

    /**
     * المستخدم الذي رفض كمدير مباشر.
     */
    public function directManagerRejectedBy()
    {
        return $this->belongsTo(User::class, 'direct_manager_rejected_by');
    }

    /**
     * مستخدم الموارد البشرية الذي وافق.
     */
    public function hrApprovedBy()
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    /**
     * مستخدم الموارد البشرية الذي رفض.
     */
    public function hrRejectedBy()
    {
        return $this->belongsTo(User::class, 'hr_rejected_by');
    }

    /**
     * اسم حالة الطلب التي تظهر للموظف.
     */
    public function getWorkflowStatusNameAttribute(): string
    {
        return match ($this->workflow_status) {
            'pending_manager' => 'قيد المراجعة من المدير المباشر',
            'manager_approved_pending_hr' => 'موافقة المدير المباشر - قيد المعالجة من الموارد البشرية',
            'rejected_by_manager' => 'مرفوضة من المدير المباشر',
            'approved_by_hr' => 'مقبولة من الموارد البشرية',
            'rejected_by_hr' => 'مرفوضة من الموارد البشرية',
            'cancelled' => 'ملغاة',
            default => 'قيد المراجعة',
        };
    }

    /**
     * كلاس CSS لحالة الطلب.
     */
    public function getWorkflowStatusClassAttribute(): string
    {
        return match ($this->workflow_status) {
            'pending_manager' => 'status-pending',
            'manager_approved_pending_hr' => 'status-processing',
            'rejected_by_manager' => 'status-rejected',
            'approved_by_hr' => 'status-approved',
            'rejected_by_hr' => 'status-rejected',
            'cancelled' => 'status-cancelled',
            default => 'status-pending',
        };
    }

    /**
     * هل الطلب ينتظر موافقة المدير المباشر؟
     */
    public function isPendingManagerApproval(): bool
    {
        return $this->workflow_status === 'pending_manager'
            && $this->direct_manager_status === 'pending';
    }

    /**
     * هل الطلب ينتظر موافقة الموارد البشرية؟
     */
    public function isPendingHrApproval(): bool
    {
        return $this->workflow_status === 'manager_approved_pending_hr'
            && $this->hr_status === 'pending';
    }
}
