<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_item_components')) {
            return;
        }

        Schema::create('payroll_item_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_item_id')->constrained('payroll_items')->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->string('type'); // earning | deduction | salary_advance | suspension
            $table->string('name');
            $table->decimal('amount', 12, 2)->default(0);

            // مصدر الحركة: EmployeeDeduction / SalaryAdvanceInstallment / EmployeeSuspension
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payroll_period_id', 'type']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_item_components');
    }
};
