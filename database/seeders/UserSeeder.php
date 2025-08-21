<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Super Admin', 'User'];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
            ]);
        }

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mailinator.com',
            'username' => 'superadmin',
        ]);

        $superAdmin->assignRole('Super Admin');

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@mailinator.com',
        ]);
    }
}
