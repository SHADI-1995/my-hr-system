<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new
class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_periods')) {
            return;
        }

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->string('period_number')->unique();
            $table->string('month')->unique(); // مثال: 2026-06
            $table->date('start_date');
            $table->date('end_date');

            $table->string('status')->default('draft'); // draft | calculated | approved | paid | cancelled

            $table->unsignedInteger('employees_count')->default(0);
            $table->decimal('total_gross_salary', 14, 2)->default(0);
            $table->decimal('total_regular_deductions', 14, 2)->default(0);
            $table->decimal('total_salary_advances', 14, 2)->default(0);
            $table->decimal('total_suspension_deductions', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('total_net_salary', 14, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('calculated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('calculated_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
