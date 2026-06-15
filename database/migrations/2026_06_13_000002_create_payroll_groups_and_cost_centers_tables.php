<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_groups')) {
            Schema::create('payroll_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('code')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cost_centers')) {
            Schema::create('cost_centers', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('code')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        $groups = [
            ['name_ar' => 'الإدارة', 'name_en' => 'Administration', 'code' => 'admin', 'sort_order' => 1],
            ['name_ar' => 'التشغيل', 'name_en' => 'Operations', 'code' => 'operations', 'sort_order' => 2],
            ['name_ar' => 'السائقين', 'name_en' => 'Drivers', 'code' => 'drivers', 'sort_order' => 3],
            ['name_ar' => 'فرع الرياض', 'name_en' => 'Riyadh Branch', 'code' => 'riyadh_branch', 'sort_order' => 4],
            ['name_ar' => 'فرع جدة', 'name_en' => 'Jeddah Branch', 'code' => 'jeddah_branch', 'sort_order' => 5],
        ];

        foreach ($groups as $group) {
            DB::table('payroll_groups')->updateOrInsert(
                ['code' => $group['code']],
                array_merge($group, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        $centers = [
            ['name_ar' => 'الإدارة', 'name_en' => 'Administration', 'code' => 'CC-001', 'sort_order' => 1],
            ['name_ar' => 'التشغيل', 'name_en' => 'Operations', 'code' => 'CC-002', 'sort_order' => 2],
            ['name_ar' => 'فرع الرياض', 'name_en' => 'Riyadh Branch', 'code' => 'CC-003', 'sort_order' => 3],
            ['name_ar' => 'فرع جدة', 'name_en' => 'Jeddah Branch', 'code' => 'CC-004', 'sort_order' => 4],
            ['name_ar' => 'التوصيل', 'name_en' => 'Delivery', 'code' => 'CC-005', 'sort_order' => 5],
        ];

        foreach ($centers as $center) {
            DB::table('cost_centers')->updateOrInsert(
                ['code' => $center['code']],
                array_merge($center, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'payroll_group_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('payroll_group_id')
                    ->nullable()
                    ->after('payroll_group')
                    ->constrained('payroll_groups')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'cost_center_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('cost_center_id')
                    ->nullable()
                    ->after('cost_center')
                    ->constrained('cost_centers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'cost_center_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropConstrainedForeignId('cost_center_id');
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'payroll_group_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payroll_group_id');
            });
        }

        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('payroll_groups');
    }
};
