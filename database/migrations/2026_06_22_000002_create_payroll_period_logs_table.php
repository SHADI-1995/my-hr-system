<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_period_logs')) {
            return;
        }

        Schema::create('payroll_period_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')
                ->constrained('payroll_periods')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action'); // created | calculated | approved | paid | deleted
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();

            $table->text('description')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['payroll_period_id', 'action']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_period_logs');
    }
};
