<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssignPayrollCancelApprovalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            $this->command?->warn('جدول permissions أو roles غير موجود.');
            return;
        }

        $pivotTable = null;

        foreach (['permission_role', 'role_permission', 'permission_role'] as $table) {
            if (Schema::hasTable($table)) {
                $pivotTable = $table;
                break;
            }
        }

        if (!$pivotTable) {
            $this->command?->warn('لم يتم العثور على جدول الربط بين roles و permissions.');
            return;
        }

        $cancelPermission = DB::table('permissions')
            ->where('code', 'payroll_periods.cancel_approval')
            ->first();

        if (!$cancelPermission) {
            $this->command?->warn('صلاحية payroll_periods.cancel_approval غير موجودة. شغل PermissionSeeder أولاً.');
            return;
        }

        $approvePermission = DB::table('permissions')
            ->where('code', 'payroll_periods.approve')
            ->first();

        if (!$approvePermission) {
            $this->command?->warn('صلاحية payroll_periods.approve غير موجودة.');
            return;
        }

        $roleIds = DB::table($pivotTable)
            ->where('permission_id', $approvePermission->id)
            ->pluck('role_id')
            ->unique()
            ->values();

        if ($roleIds->isEmpty()) {
            $this->command?->warn('لا توجد أدوار لديها صلاحية اعتماد مسير الرواتب.');
            return;
        }

        foreach ($roleIds as $roleId) {
            $exists = DB::table($pivotTable)
                ->where('role_id', $roleId)
                ->where('permission_id', $cancelPermission->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $data = [
                'role_id' => $roleId,
                'permission_id' => $cancelPermission->id,
            ];

            if (Schema::hasColumn($pivotTable, 'created_at')) {
                $data['created_at'] = now();
            }

            if (Schema::hasColumn($pivotTable, 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table($pivotTable)->insert($data);
        }

        $this->command?->info('تم ربط صلاحية إلغاء اعتماد المسير بكل دور لديه صلاحية اعتماد المسير.');
    }
}
