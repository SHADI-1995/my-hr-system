<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Payroll fields for leave types
|--------------------------------------------------------------------------
| الهدف:
| تحديد هل نوع الإجازة يؤثر على مسير الرواتب أم لا، وكم نسبة الراتب أثناء الإجازة.
|
| أمثلة:
| إجازة سنوية: affects_payroll = false أو salary_percentage = 100
| إجازة بدون راتب: affects_payroll = true, salary_percentage = 0
| إجازة مرضية نصف راتب: affects_payroll = true, salary_percentage = 50
*/

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leave_types')) {
            return;
        }

        Schema::table('leave_types', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_types', 'affects_payroll')) {
                $table->boolean('affects_payroll')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('leave_types', 'salary_percentage')) {
                $table->decimal('salary_percentage', 5, 2)->default(100)->after('affects_payroll');
            }

            if (!Schema::hasColumn('leave_types', 'payroll_policy_note')) {
                $table->text('payroll_policy_note')->nullable()->after('salary_percentage');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('leave_types')) {
            return;
        }

        Schema::table('leave_types', function (Blueprint $table) {
            if (Schema::hasColumn('leave_types', 'payroll_policy_note')) {
                $table->dropColumn('payroll_policy_note');
            }

            if (Schema::hasColumn('leave_types', 'salary_percentage')) {
                $table->dropColumn('salary_percentage');
            }

            if (Schema::hasColumn('leave_types', 'affects_payroll')) {
                $table->dropColumn('affects_payroll');
            }
        });
    }
};
