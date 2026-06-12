<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_components')) {
            Schema::create('salary_components', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('type'); // earning | deduction
                $table->boolean('is_fixed')->default(true);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        $components = [
            ['name' => 'الراتب الأساسي', 'code' => 'basic_salary', 'type' => 'earning', 'is_fixed' => true, 'is_system' => true],
            ['name' => 'بدل السكن', 'code' => 'housing_allowance', 'type' => 'earning', 'is_fixed' => true, 'is_system' => true],
            ['name' => 'بدل النقل', 'code' => 'transport_allowance', 'type' => 'earning', 'is_fixed' => true, 'is_system' => true],
            ['name' => 'بدل الطعام', 'code' => 'food_allowance', 'type' => 'earning', 'is_fixed' => true, 'is_system' => true],
            ['name' => 'بدلات أخرى', 'code' => 'other_allowance', 'type' => 'earning', 'is_fixed' => true, 'is_system' => true],
            ['name' => 'خصم سلفة', 'code' => 'salary_advance_deduction', 'type' => 'deduction', 'is_fixed' => false, 'is_system' => true],
            ['name' => 'خصم إجازة غير مدفوعة', 'code' => 'unpaid_leave_deduction', 'type' => 'deduction', 'is_fixed' => false, 'is_system' => true],
            ['name' => 'خصم إيقاف', 'code' => 'suspension_deduction', 'type' => 'deduction', 'is_fixed' => false, 'is_system' => true],
            ['name' => 'خصم يدوي', 'code' => 'manual_deduction', 'type' => 'deduction', 'is_fixed' => false, 'is_system' => true],
            ['name' => 'مكافأة', 'code' => 'bonus', 'type' => 'earning', 'is_fixed' => false, 'is_system' => true],
        ];

        foreach ($components as $component) {
            DB::table('salary_components')->updateOrInsert(
                ['code' => $component['code']],
                array_merge($component, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
