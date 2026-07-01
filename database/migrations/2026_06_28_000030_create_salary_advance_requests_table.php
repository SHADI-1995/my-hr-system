<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_advance_requests')) {
            return;
        }

        Schema::create('salary_advance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();

            $table->unsignedInteger('installments_count')->default(1);
            $table->decimal('installment_amount', 12, 2)->nullable();
            $table->date('deduction_start_date')->nullable();

            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();

            /*
             | نفس فكرة طلبات الإجازات:
             | status للحالة النهائية العامة
             | workflow_status لمسار الموافقات
             */
            $table->string('status')->default('pending')->index();
            $table->string('workflow_status')->default('pending_manager')->index();

            $table->string('direct_manager_status')->default('pending');
            $table->foreignId('direct_manager_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('direct_manager_approved_at')->nullable();
            $table->foreignId('direct_manager_rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('direct_manager_rejected_at')->nullable();
            $table->text('direct_manager_reject_reason')->nullable();
            $table->text('direct_manager_note')->nullable();

            $table->string('hr_status')->default('waiting_manager');
            $table->foreignId('hr_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_approved_at')->nullable();
            $table->foreignId('hr_rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_rejected_at')->nullable();
            $table->text('hr_reject_reason')->nullable();
            $table->text('hr_note')->nullable();

            $table->foreignId('registered_salary_advance_id')->nullable()->constrained('salary_advances')->nullOnDelete();

            $table->timestamps();

            $table->index(['employee_id', 'workflow_status']);
            $table->index(['status', 'workflow_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advance_requests');
    }
};
