<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_advances')) {
            return;
        }

        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->string('advance_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->unsignedInteger('installments_count')->default(1);
            $table->decimal('installment_amount', 12, 2)->default(0);

            $table->date('request_date')->nullable();

            // هذا تاريخ بداية افتراضي فقط لاقتراح الأشهر
            $table->date('deduction_start_date');

            $table->string('status')->default('pending'); // pending | approved | cancelled | completed
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
            $table->index('deduction_start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
