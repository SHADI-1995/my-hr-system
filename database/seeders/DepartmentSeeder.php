<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $hr = Department::updateOrCreate(
            ['code' => 'HR'],
            ['name' => 'الموارد البشرية']
        );

        $it = Department::updateOrCreate(
            ['code' => 'IT'],
            ['name' => 'تقنية المعلومات']
        );

        $acc = Department::updateOrCreate(
            ['code' => 'ACC'],
            ['name' => 'المحاسبة']
        );

        Position::updateOrCreate(
            ['code' => 'HR-MGR'],
            ['department_id' => $hr->id, 'title' => 'مدير موارد بشرية']
        );

        Position::updateOrCreate(
            ['code' => 'DEV-LAR'],
            ['department_id' => $it->id, 'title' => 'مطور Laravel']
        );

        Position::updateOrCreate(
            ['code' => 'ACC-001'],
            ['department_id' => $acc->id, 'title' => 'محاسب']
        );
    }
}
