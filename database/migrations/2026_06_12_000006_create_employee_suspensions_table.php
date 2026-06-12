<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_suspensions')) {
            return;
        }

        Schema::create('employee_suspensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->date('start_date');
            $table->date('resume_date')->nullable();
            $table->decimal('salary_percentage', 5, 2)->default(0); // 0 = بدون راتب، 50 = نصف راتب، 100 = براتب كامل

            $table->string('status')->default('active'); // active | resumed | cancelled
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('resumed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resumed_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['start_date', 'resume_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_suspensions');
    }
};
