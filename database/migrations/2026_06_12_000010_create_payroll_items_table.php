<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_items')) {
            return;
        }

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            // Snapshot من بيانات الموظف وقت الاحتساب
            $table->string('employee_number')->nullable();
            $table->string('employee_name')->nullable();

            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('food_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);

            $table->decimal('gross_salary', 12, 2)->default(0);

            $table->decimal('regular_deductions', 12, 2)->default(0);
            $table->decimal('salary_advance_deductions', 12, 2)->default(0);
            $table->decimal('suspension_deductions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);

            $table->decimal('net_salary', 12, 2)->default(0);

            $table->unsignedInteger('period_days')->default(0);
            $table->unsignedInteger('suspended_days')->default(0);

            $table->string('status')->default('calculated'); // calculated | approved | paid
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id'], 'payroll_period_employee_unique');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
