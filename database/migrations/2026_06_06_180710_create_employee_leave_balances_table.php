<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('leave_policy_id')
                ->nullable()
                ->constrained('leave_policies')
                ->nullOnDelete();

            // سنة الرصيد، مثل 2026 أو 1448 إذا كان هجري
            $table->string('year_label')->nullable();

            // بداية دورة الإجازة
            $table->date('service_year_start');

            // نهاية دورة الإجازة
            $table->date('service_year_end');

            // الرصيد المستحق السنوي
            $table->decimal('annual_entitled_days', 8, 2)->default(0);

            // الرصيد المرحل
            $table->decimal('carried_forward_days', 8, 2)->default(0);

            // أيام مدفوعة مستخدمة
            $table->decimal('used_paid_days', 8, 2)->default(0);

            // أيام غير مدفوعة
            $table->decimal('used_unpaid_days', 8, 2)->default(0);

            // أيام أخرى
            $table->decimal('used_other_days', 8, 2)->default(0);

            // المتبقي
            $table->decimal('remaining_days', 8, 2)->default(0);

            /*
             * open     = مفتوح
             * closed   = مغلق
             * settled  = تمت تسويته
             */
            $table->enum('status', [
                'open',
                'closed',
                'settled',
            ])->default('open');

            $table->timestamps();

            $table->unique([
                'employee_id',
                'service_year_start',
                'service_year_end',
            ], 'employee_leave_balance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
