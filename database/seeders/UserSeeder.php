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
            'username' => 'superadmin',
            'email' => 'superadmin@mailinator.com',
        ]);

        $superAdmin->assignRole('Super Admin');

        $user = User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@mailinator.com',
        ]);

        $user->assignRole('User');
    }
}
