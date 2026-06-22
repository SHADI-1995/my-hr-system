<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * تحديث جدول employee_deductions ليصبح جاهزًا للجدولة الشهرية.
         * نستخدم hasColumn حتى لا يحدث خطأ إذا كانت بعض الحقول موجودة مسبقًا.
         */
        if (Schema::hasTable('employee_deductions')) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
                    $table->unsignedBigInteger('deduction_type_id')->nullable()->after('employee_id');
                }

                if (!Schema::hasColumn('employee_deductions', 'title')) {
                    $table->string('title')->nullable()->after('deduction_type');
                }

                if (!Schema::hasColumn('employee_deductions', 'calculation_type')) {
                    $table->string('calculation_type', 30)->default('fixed')->after('deduction_mode');
                }

                if (!Schema::hasColumn('employee_deductions', 'percentage')) {
                    $table->decimal('percentage', 8, 2)->nullable()->after('amount');
                }

                if (!Schema::hasColumn('employee_deductions', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->nullable()->after('percentage');
                }

                if (!Schema::hasColumn('employee_deductions', 'start_month')) {
                    $table->string('start_month', 7)->nullable()->after('end_date');
                }

                if (!Schema::hasColumn('employee_deductions', 'end_month')) {
                    $table->string('end_month', 7)->nullable()->after('start_month');
                }

                if (!Schema::hasColumn('employee_deductions', 'selected_months')) {
                    $table->json('selected_months')->nullable()->after('end_month');
                }
            });

            /*
             * ترحيل بسيط من النظام القديم:
             * start_date/end_date إلى start_month/end_month
             * total_amount من amount
             * calculation_type حسب deduction_mode
             */
            if (
                Schema::hasColumn('employee_deductions', 'start_date') &&
                Schema::hasColumn('employee_deductions', 'start_month')
            ) {
                DB::statement("
                    UPDATE employee_deductions
                    SET start_month = DATE_FORMAT(start_date, '%Y-%m')
                    WHERE start_month IS NULL AND start_date IS NOT NULL
                ");
            }

            if (
                Schema::hasColumn('employee_deductions', 'end_date') &&
                Schema::hasColumn('employee_deductions', 'end_month')
            ) {
                DB::statement("
                    UPDATE employee_deductions
                    SET end_month = DATE_FORMAT(end_date, '%Y-%m')
                    WHERE end_month IS NULL AND end_date IS NOT NULL
                ");
            }

            if (
                Schema::hasColumn('employee_deductions', 'amount') &&
                Schema::hasColumn('employee_deductions', 'total_amount')
            ) {
                DB::statement("
                    UPDATE employee_deductions
                    SET total_amount = amount
                    WHERE total_amount IS NULL
                ");
            }

            if (Schema::hasColumn('employee_deductions', 'calculation_type')) {
                DB::statement("
                    UPDATE employee_deductions
                    SET calculation_type = CASE
                        WHEN deduction_mode = 'percentage' THEN 'percentage'
                        ELSE 'fixed'
                    END
                    WHERE calculation_type IS NULL OR calculation_type = ''
                ");
            }
        }

        /*
         * جدول جدولة الاستقطاعات:
         * كل سجل يمثل خصم شهر محدد للموظف.
         */
        if (!Schema::hasTable('employee_deduction_schedules')) {
            Schema::create('employee_deduction_schedules', function (Blueprint $table) {
                $table->id();

                $table->foreignId('employee_deduction_id')
                    ->constrained('employee_deductions')
                    ->cascadeOnDelete();

                $table->foreignId('employee_id')
                    ->constrained('employees')
                    ->cascadeOnDelete();

                // مثال: 2026-06
                $table->string('payroll_month', 7);

                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('percentage', 8, 2)->nullable();

                /*
                 * pending   = جاهز للخصم
                 * deducted  = تم خصمه في مسير
                 * skipped   = تم تجاوزه
                 * cancelled = ملغي
                 */
                $table->string('status', 30)->default('pending');

                $table->foreignId('payroll_period_id')
                    ->nullable()
                    ->constrained('payroll_periods')
                    ->nullOnDelete();

                $table->foreignId('payroll_item_id')
                    ->nullable()
                    ->constrained('payroll_items')
                    ->nullOnDelete();

                $table->timestamp('deducted_at')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->unique(
                    ['employee_deduction_id', 'payroll_month'],
                    'eds_deduction_month_unique'
                );

                $table->index(['employee_id', 'payroll_month', 'status'], 'eds_employee_month_status_index');
                $table->index(['payroll_period_id', 'status'], 'eds_period_status_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_deduction_schedules');

        if (Schema::hasTable('employee_deductions')) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                if (Schema::hasColumn('employee_deductions', 'selected_months')) {
                    $table->dropColumn('selected_months');
                }

                if (Schema::hasColumn('employee_deductions', 'end_month')) {
                    $table->dropColumn('end_month');
                }

                if (Schema::hasColumn('employee_deductions', 'start_month')) {
                    $table->dropColumn('start_month');
                }

                if (Schema::hasColumn('employee_deductions', 'total_amount')) {
                    $table->dropColumn('total_amount');
                }

                if (Schema::hasColumn('employee_deductions', 'percentage')) {
                    $table->dropColumn('percentage');
                }

                if (Schema::hasColumn('employee_deductions', 'calculation_type')) {
                    $table->dropColumn('calculation_type');
                }

                if (Schema::hasColumn('employee_deductions', 'title')) {
                    $table->dropColumn('title');
                }

                if (Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
                    $table->dropColumn('deduction_type_id');
                }
            });
        }
    }
};
