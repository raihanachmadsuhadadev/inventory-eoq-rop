<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Akses penuh untuk konfigurasi dan pengelolaan sistem.',
            ],
            [
                'name' => 'Admin Gudang',
                'slug' => 'admin_gudang',
                'description' => 'Mengelola operasional stok dan transaksi gudang.',
            ],
            [
                'name' => 'Manager Gudang',
                'slug' => 'manager_gudang',
                'description' => 'Memantau laporan, rekomendasi, dan performa inventaris.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role,
            );
        }
    }
}
