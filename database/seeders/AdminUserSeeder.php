<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['code' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'is_active' => 1,
            ]
        );

        $permissions = Permission::pluck('id')->toArray();

        $adminRole->permissions()->sync($permissions);

        User::updateOrCreate(
            ['email' => 'admin@hr.com'],
            [
                'name' => 'System Admin',
                'username' => 'admin',
                'role_id' => $adminRole->id,
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
