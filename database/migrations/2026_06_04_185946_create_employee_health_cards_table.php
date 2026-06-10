<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_health_cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('card_number')->unique();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date');
            $table->string('issuer')->nullable();

            $table->enum('status', [
                'valid',
                'near_expiry',
                'expired',
            ])->default('valid');

            $table->integer('remaining_days')->default(0);
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'expiry_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_health_cards');
    }
};
