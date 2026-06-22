<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('deduction_types')) {
            Schema::create('deduction_types', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('employee_deductions') && !Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->foreignId('deduction_type_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('deduction_types')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('deduction_types') && DB::table('deduction_types')->count() === 0) {
            DB::table('deduction_types')->insert([
                ['code'=>'late','name_ar'=>'خصم تأخير','name_en'=>'Late Deduction','is_active'=>1,'sort_order'=>1,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'absence','name_ar'=>'خصم غياب','name_en'=>'Absence Deduction','is_active'=>1,'sort_order'=>2,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'disciplinary','name_ar'=>'جزاء إداري','name_en'=>'Disciplinary Deduction','is_active'=>1,'sort_order'=>3,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'custody','name_ar'=>'خصم عهدة','name_en'=>'Custody Deduction','is_active'=>1,'sort_order'=>4,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'damage','name_ar'=>'خصم تلفيات','name_en'=>'Damage Deduction','is_active'=>1,'sort_order'=>5,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'insurance','name_ar'=>'خصم تأمين','name_en'=>'Insurance Deduction','is_active'=>1,'sort_order'=>6,'created_at'=>now(),'updated_at'=>now()],
                ['code'=>'other','name_ar'=>'أخرى','name_en'=>'Other','is_active'=>1,'sort_order'=>99,'created_at'=>now(),'updated_at'=>now()],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employee_deductions') && Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('deduction_type_id');
            });
        }

        Schema::dropIfExists('deduction_types');
    }
};
