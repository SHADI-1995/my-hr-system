<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'first_name')) {
                $table->string('first_name')->nullable()->after('employee_number');
            }

            if (!Schema::hasColumn('employees', 'second_name')) {
                $table->string('second_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('employees', 'last_name')) {
                $table->string('last_name')->nullable()->after('second_name');
            }

            if (!Schema::hasColumn('employees', 'full_name')) {
                $table->string('full_name')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('employees', 'nationality_id')) {
                $table->foreignId('nationality_id')
                    ->nullable()
                    ->after('full_name')
                    ->constrained('nationalities')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('employees', 'termination_date')) {
                $table->date('termination_date')->nullable()->after('hire_date');
            }

            if (!Schema::hasColumn('employees', 'termination_reason')) {
                $table->string('termination_reason')->nullable()->after('termination_date');
            }

            if (!Schema::hasColumn('employees', 'housing_allowance')) {
                $table->decimal('housing_allowance', 10, 2)->default(0)->after('basic_salary');
            }

            if (!Schema::hasColumn('employees', 'transport_allowance')) {
                $table->decimal('transport_allowance', 10, 2)->default(0)->after('housing_allowance');
            }

            if (!Schema::hasColumn('employees', 'food_allowance')) {
                $table->decimal('food_allowance', 10, 2)->default(0)->after('transport_allowance');
            }

            if (!Schema::hasColumn('employees', 'other_allowance')) {
                $table->decimal('other_allowance', 10, 2)->default(0)->after('food_allowance');
            }

            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('other_allowance');
            }

            if (!Schema::hasColumn('employees', 'iban')) {
                $table->string('iban')->nullable()->after('bank_name');
            }

            if (!Schema::hasColumn('employees', 'notes')) {
                $table->text('notes')->nullable()->after('iban');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'nationality_id')) {
                $table->dropConstrainedForeignId('nationality_id');
            }

            $columns = [
                'first_name',
                'second_name',
                'last_name',
                'full_name',
                'termination_date',
                'termination_reason',
                'housing_allowance',
                'transport_allowance',
                'food_allowance',
                'other_allowance',
                'bank_name',
                'iban',
                'notes',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
