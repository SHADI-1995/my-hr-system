<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('employee_leave_balance_id')
                ->nullable()
                ->constrained('employee_leave_balances')
                ->nullOnDelete();

            /*
             * annual_accrual
             * carry_forward
             * paid_leave_deduction
             * unpaid_leave_record
             * official_leave_record
             * manual_adjustment
             * cancellation_restore
             * end_service_settlement
             */
            $table->string('transaction_type');

            $table->decimal('days', 8, 2)->default(0);

            $table->decimal('before_balance', 8, 2)->default(0);
            $table->decimal('after_balance', 8, 2)->default(0);

            // مثال: LeaveRequest
            $table->string('reference_type')->nullable();

            // مثال: رقم طلب الإجازة
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['employee_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_transactions');
    }
};
