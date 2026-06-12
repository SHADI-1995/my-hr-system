<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_salary_histories')) {
            Schema::create('employee_salary_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('basic_salary', 12, 2)->default(0);
                $table->decimal('housing_allowance', 12, 2)->default(0);
                $table->decimal('transport_allowance', 12, 2)->default(0);
                $table->decimal('food_allowance', 12, 2)->default(0);
                $table->decimal('other_allowance', 12, 2)->default(0);
                $table->decimal('total_salary', 12, 2)->default(0);
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->string('change_reason')->nullable();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            return;
        }

        Schema::table('employee_salary_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_salary_histories', 'basic_salary')) {
                $table->decimal('basic_salary', 12, 2)->default(0)->after('employee_id');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'housing_allowance')) {
                $table->decimal('housing_allowance', 12, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'transport_allowance')) {
                $table->decimal('transport_allowance', 12, 2)->default(0)->after('housing_allowance');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'food_allowance')) {
                $table->decimal('food_allowance', 12, 2)->default(0)->after('transport_allowance');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'other_allowance')) {
                $table->decimal('other_allowance', 12, 2)->default(0)->after('food_allowance');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'total_salary')) {
                $table->decimal('total_salary', 12, 2)->default(0)->after('other_allowance');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('total_salary');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'effective_to')) {
                $table->date('effective_to')->nullable()->after('effective_from');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'change_reason')) {
                $table->string('change_reason')->nullable()->after('effective_to');
            }
            if (!Schema::hasColumn('employee_salary_histories', 'changed_by')) {
                $table->foreignId('changed_by')->nullable()->after('change_reason')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_histories');
    }
};
