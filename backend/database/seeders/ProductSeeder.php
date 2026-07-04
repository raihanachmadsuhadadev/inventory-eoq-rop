<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed initial products.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'code');
        $suppliers = Supplier::query()->pluck('id', 'code');

        $products = [
            [
                'category_id' => $categories['BB'] ?? null,
                'supplier_id' => $suppliers['SUP-AGRO'] ?? null,
                'code' => 'PRD-KOPI-001',
                'name' => 'Kopi Arabica 1kg',
                'unit' => 'kg',
                'minimum_stock' => 20,
                'description' => 'Biji kopi arabica kemasan 1kg.',
            ],
            [
                'category_id' => $categories['KMS'] ?? null,
                'supplier_id' => $suppliers['SUP-PACK'] ?? null,
                'code' => 'PRD-CUP-012',
                'name' => 'Cup Paper 12oz',
                'unit' => 'pcs',
                'minimum_stock' => 120,
                'description' => 'Cup paper ukuran 12oz.',
            ],
            [
                'category_id' => $categories['BB'] ?? null,
                'supplier_id' => $suppliers['SUP-DAIRY'] ?? null,
                'code' => 'PRD-SUSU-001',
                'name' => 'Susu UHT 1L',
                'unit' => 'liter',
                'minimum_stock' => 25,
                'description' => 'Susu UHT kemasan 1 liter.',
            ],
        ];

        foreach ($products as $product) {
            if (! $product['category_id']) {
                continue;
            }

            Product::updateOrCreate(
                ['code' => $product['code']],
                $product + ['is_active' => true],
            );
        }
    }
}
