<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call([
            DepartmentSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User']
        );
    }
}
