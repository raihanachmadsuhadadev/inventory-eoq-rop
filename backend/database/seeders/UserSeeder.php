<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed demo users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@inventory.test',
                'role_slug' => 'super_admin',
            ],
            [
                'name' => 'Admin Gudang',
                'email' => 'admingudang@inventory.test',
                'role_slug' => 'admin_gudang',
            ],
            [
                'name' => 'Manager Gudang',
                'email' => 'managergudang@inventory.test',
                'role_slug' => 'manager_gudang',
            ],
        ];

        foreach ($users as $user) {
            $role = Role::where('slug', $user['role_slug'])->firstOrFail();

            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'role_id' => $role->id,
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
            );
        }
    }
}
