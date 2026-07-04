<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Seed initial suppliers.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-AGRO',
                'name' => 'Nusa Agro',
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567001',
                'email' => 'sales@nusaagro.test',
                'address' => 'Jl. Pangan Nusantara No. 10',
                'lead_time_days' => 5,
                'description' => 'Supplier bahan baku utama.',
            ],
            [
                'code' => 'SUP-PACK',
                'name' => 'Prima Pack',
                'contact_person' => 'Rina Putri',
                'phone' => '081234567002',
                'email' => 'order@primapack.test',
                'address' => 'Jl. Kemasan Industri No. 22',
                'lead_time_days' => 3,
                'description' => 'Supplier kemasan dan perlengkapan packing.',
            ],
            [
                'code' => 'SUP-DAIRY',
                'name' => 'Dairy Fresh',
                'contact_person' => 'Agus Wijaya',
                'phone' => '081234567003',
                'email' => 'hello@dairyfresh.test',
                'address' => 'Jl. Susu Segar No. 7',
                'lead_time_days' => 4,
                'description' => 'Supplier produk dairy.',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['code' => $supplier['code']],
                $supplier + ['is_active' => true],
            );
        }
    }
}
