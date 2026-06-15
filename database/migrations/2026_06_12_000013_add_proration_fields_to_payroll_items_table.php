<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Proration fields for payroll items
|--------------------------------------------------------------------------
| هذه الحقول تحفظ تفاصيل أهلية الموظف داخل الشهر:
| - أيام الاستحقاق حسب تاريخ التعيين والفصل
| - خصم الإجازات غير المدفوعة
| - ملاحظات حالة الموظف
*/

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_items')) {
            return;
        }

        Schema::table('payroll_items', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_items', 'eligible_start_date')) {
                $table->date('eligible_start_date')->nullable()->after('employee_name');
            }

            if (!Schema::hasColumn('payroll_items', 'eligible_end_date')) {
                $table->date('eligible_end_date')->nullable()->after('eligible_start_date');
            }

            if (!Schema::hasColumn('payroll_items', 'payable_days')) {
                $table->unsignedInteger('payable_days')->default(0)->after('period_days');
            }

            if (!Schema::hasColumn('payroll_items', 'unpaid_leave_days')) {
                $table->unsignedInteger('unpaid_leave_days')->default(0)->after('suspended_days');
            }

            if (!Schema::hasColumn('payroll_items', 'unpaid_leave_deductions')) {
                $table->decimal('unpaid_leave_deductions', 12, 2)->default(0)->after('suspension_deductions');
            }

            if (!Schema::hasColumn('payroll_items', 'employment_status_note')) {
                $table->string('employment_status_note')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payroll_items')) {
            return;
        }

        Schema::table('payroll_items', function (Blueprint $table) {
            foreach ([
                'employment_status_note',
                'unpaid_leave_deductions',
                'unpaid_leave_days',
                'payable_days',
                'eligible_end_date',
                'eligible_start_date',
            ] as $column) {
                if (Schema::hasColumn('payroll_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
