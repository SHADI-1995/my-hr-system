<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * payroll_group_scope:
         * all      = مسير يشمل كل مجموعات الرواتب
         * selected = مسير يشمل مجموعات محددة فقط
         */
        if (Schema::hasTable('payroll_periods') && !Schema::hasColumn('payroll_periods', 'payroll_group_scope')) {
            Schema::table('payroll_periods', function (Blueprint $table) {
                $table->string('payroll_group_scope', 20)
                    ->default('all')
                    ->after('end_date');
            });
        }

        /*
         * جدول وسيط يسمح بأن مسير رواتب واحد يحتوي أكثر من مجموعة رواتب.
         */
        if (!Schema::hasTable('payroll_period_groups')) {
            Schema::create('payroll_period_groups', function (Blueprint $table) {
                $table->id();

                $table->foreignId('payroll_period_id')
                    ->constrained('payroll_periods')
                    ->cascadeOnDelete();

                $table->foreignId('payroll_group_id')
                    ->constrained('payroll_groups')
                    ->cascadeOnDelete();

                $table->timestamps();

                $table->unique(
                    ['payroll_period_id', 'payroll_group_id'],
                    'ppg_period_group_unique'
                );

                $table->index('payroll_period_id');
                $table->index('payroll_group_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_period_groups');

        if (Schema::hasTable('payroll_periods') && Schema::hasColumn('payroll_periods', 'payroll_group_scope')) {
            Schema::table('payroll_periods', function (Blueprint $table) {
                $table->dropColumn('payroll_group_scope');
            });
        }
    }
};
