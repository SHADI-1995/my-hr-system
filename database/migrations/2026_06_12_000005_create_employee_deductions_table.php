<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_deductions')) {
            return;
        }

        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->string('deduction_number')->unique();
            $table->string('deduction_type');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('deduction_mode')->default('one_time'); // one_time | monthly | installments | percentage
            $table->unsignedInteger('installments_count')->nullable();
            $table->decimal('monthly_amount', 12, 2)->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->string('status')->default('pending'); // pending | approved | cancelled | completed | stopped
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
