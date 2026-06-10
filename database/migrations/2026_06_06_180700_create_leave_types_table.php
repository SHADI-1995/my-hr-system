<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            /*
             * annual      = سنوية
             * unpaid      = غير مدفوعة
             * official    = رسمية
             * sick        = مرضية
             * marriage    = زواج
             * newborn     = مولود
             * death       = وفاة
             * other       = أخرى
             */
            $table->string('code')->unique();

            // هل الإجازة مدفوعة؟
            $table->boolean('is_paid')->default(true);

            // هل تخصم من رصيد الإجازة السنوية؟
            $table->boolean('deduct_from_annual_balance')->default(false);

            // هل تحتاج مرفق؟
            $table->boolean('requires_attachment')->default(false);

            // هل تحتاج موافقة؟
            $table->boolean('requires_approval')->default(true);

            // هل تعتمد تلقائيًا؟
            $table->boolean('auto_approved')->default(false);

            // حد أقصى في السنة، اختياري
            $table->unsignedInteger('max_days_per_year')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
