<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id();

            $table->string('name')->default('سياسة الإجازات الافتراضية');

            // 21 يوم قبل 5 سنوات
            $table->unsignedInteger('annual_days_before_5_years')->default(21);

            // 30 يوم بعد 5 سنوات
            $table->unsignedInteger('annual_days_after_5_years')->default(30);

            // بعد كم سنة تزيد الإجازة
            $table->unsignedInteger('after_years')->default(5);

            /*
             * hire_date  = حسب تاريخ مباشرة الموظف
             * gregorian  = حسب السنة الميلادية
             * hijri      = حسب السنة الهجرية
             */
            $table->enum('leave_year_type', [
                'hire_date',
                'gregorian',
                'hijri',
            ])->default('hire_date');

            // هل يتم ترحيل الرصيد؟
            $table->boolean('carry_forward_enabled')->default(false);

            // أقصى عدد أيام يمكن ترحيلها
            $table->unsignedInteger('max_carry_forward_days')->default(0);

            // هل يتم استبعاد الويكند من أيام الإجازة؟
            $table->boolean('exclude_weekends')->default(true);

            // هل يتم استبعاد الإجازات الرسمية؟
            $table->boolean('exclude_official_holidays')->default(true);

            // هل الموظف غير النشط يستمر احتساب إجازاته؟
            $table->boolean('inactive_employee_accrual')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_policies');
    }
};
