<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'salary_payment_method')) {
                $table->string('salary_payment_method')->default('bank_transfer')->after('iban');
            }

            if (!Schema::hasColumn('employees', 'payroll_status')) {
                $table->string('payroll_status')->default('included')->after('salary_payment_method');
            }

            if (!Schema::hasColumn('employees', 'salary_effective_date')) {
                $table->date('salary_effective_date')->nullable()->after('payroll_status');
            }

            if (!Schema::hasColumn('employees', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('salary_effective_date');
            }

            if (!Schema::hasColumn('employees', 'payroll_group')) {
                $table->string('payroll_group')->nullable()->after('bank_account_name');
            }

            if (!Schema::hasColumn('employees', 'cost_center')) {
                $table->string('cost_center')->nullable()->after('payroll_group');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'salary_payment_method',
                'payroll_status',
                'salary_effective_date',
                'bank_account_name',
                'payroll_group',
                'cost_center',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
