<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBankTransferBatch extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'batch_number',
        'status',
        'employees_count',
        'total_amount',
        'missing_bank_data_count',
        'generated_by',
        'generated_at',
        'sent_by',
        'sent_at',
        'confirmed_by',
        'confirmed_at',

        /*
         * بيانات تأكيد التحويل من البنك
         */
        'bank_reference',
        'bank_transfer_date',
        'confirmation_file',

        'cancelled_by',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'employees_count' => 'integer',
        'total_amount' => 'decimal:2',
        'missing_bank_data_count' => 'integer',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'bank_transfer_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /*
     |--------------------------------------------------------------------------
     | Display Accessors
     |--------------------------------------------------------------------------
     */

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'generated' => 'تم التجهيز',
            'sent' => 'تم الإرسال للبنك',
            'confirmed' => 'تم تأكيد التحويل',
            'cancelled' => 'ملغي',
            default => $this->status ?: '-',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'generated' => 'generated',
            'sent' => 'sent',
            'confirmed' => 'confirmed',
            'cancelled' => 'cancelled',
            default => 'generated',
        };
    }

    /*
     * رابط ملف إثبات التحويل المرفوع.
     * يجب تنفيذ php artisan storage:link حتى يفتح الرابط من المتصفح.
     */
    public function getConfirmationFileUrlAttribute(): ?string
    {
        if (!$this->confirmation_file) {
            return null;
        }

        return asset('storage/' . ltrim($this->confirmation_file, '/'));
    }

    /*
     |--------------------------------------------------------------------------
     | Workflow Helpers
     |--------------------------------------------------------------------------
     */

    public function getCanSendAttribute(): bool
    {
        return $this->status === 'generated';
    }

    public function getCanConfirmAttribute(): bool
    {
        return $this->status === 'sent';
    }

    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['generated', 'sent'], true);
    }

    /*
     |--------------------------------------------------------------------------
     | Numbering
     |--------------------------------------------------------------------------
     */

    public static function generateNumber(): string
    {
        $last = static::query()
            ->where('batch_number', 'like', 'BT-%')
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($last && preg_match('/BT-(\d+)/', (string) $last->batch_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'BT-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
