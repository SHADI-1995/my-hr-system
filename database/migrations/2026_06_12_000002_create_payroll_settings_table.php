<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_settings')) {
            Schema::create('payroll_settings', function (Blueprint $table) {
                $table->id();
                $table->string('salary_day_calculation')->default('fixed_30_days');
                $table->string('default_payment_method')->default('bank_transfer');
                $table->boolean('allow_negative_net_salary')->default(false);
                $table->unsignedTinyInteger('rounding_decimals')->default(2);
                $table->boolean('auto_deduct_approved_advances')->default(true);
                $table->boolean('auto_deduct_unpaid_leaves')->default(true);
                $table->boolean('auto_deduct_suspensions')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('payroll_settings') && DB::table('payroll_settings')->count() === 0) {
            DB::table('payroll_settings')->insert([
                'salary_day_calculation' => 'fixed_30_days',
                'default_payment_method' => 'bank_transfer',
                'allow_negative_net_salary' => false,
                'rounding_decimals' => 2,
                'auto_deduct_approved_advances' => true,
                'auto_deduct_unpaid_leaves' => true,
                'auto_deduct_suspensions' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
