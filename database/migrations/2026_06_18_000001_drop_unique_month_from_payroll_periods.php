<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_periods')) {
            return;
        }

        /*
         * كان يوجد unique على عمود month باسم:
         * payroll_periods_month_unique
         *
         * هذا يمنع إنشاء أكثر من مسير لنفس الشهر، حتى لو كانت مجموعات الرواتب مختلفة.
         * بعد دعم payroll_group_scope و payroll_period_groups يجب إزالة unique.
         */
        Schema::table('payroll_periods', function (Blueprint $table) {
            try {
                $table->dropUnique('payroll_periods_month_unique');
            } catch (\Throwable $e) {
                // إذا كان محذوف مسبقًا، تجاهل.
            }
        });

        Schema::table('payroll_periods', function (Blueprint $table) {
            try {
                $table->index('month', 'payroll_periods_month_index');
            } catch (\Throwable $e) {
                // إذا كان الفهرس موجود مسبقًا، تجاهل.
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payroll_periods')) {
            return;
        }

        Schema::table('payroll_periods', function (Blueprint $table) {
            try {
                $table->dropIndex('payroll_periods_month_index');
            } catch (\Throwable $e) {
                // تجاهل.
            }
        });

        /*
         * إرجاع unique القديم قد يفشل إذا أصبح لديك أكثر من مسير لنفس الشهر.
         */
        Schema::table('payroll_periods', function (Blueprint $table) {
            try {
                $table->unique('month', 'payroll_periods_month_unique');
            } catch (\Throwable $e) {
                // تجاهل.
            }
        });
    }
};
