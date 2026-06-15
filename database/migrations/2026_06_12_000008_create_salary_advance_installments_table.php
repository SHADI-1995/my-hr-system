<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_advance_installments')) {
            return;
        }

        Schema::create('salary_advance_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_advance_id')->constrained('salary_advances')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->unsignedInteger('installment_number');
            $table->decimal('amount', 12, 2);

            // الشهر/التاريخ الذي سيتم الخصم فيه
            $table->date('due_date');

            $table->string('status')->default('pending'); // pending | deducted | skipped | cancelled

            // ستربط لاحقًا بمرحلة مسير الرواتب
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->unsignedBigInteger('payroll_item_id')->nullable();

            $table->date('deducted_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advance_installments');
    }
};
