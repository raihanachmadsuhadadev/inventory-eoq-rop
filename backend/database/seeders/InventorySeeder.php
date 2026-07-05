<?php

namespace Database\Seeders;

use App\Models\Hub;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Seed initial inventories.
     */
    public function run(): void
    {
        $hub = Hub::query()->where('code', 'HUB-PST')->first();

        if (! $hub) {
            return;
        }

        $stocks = [
            'PRD-KOPI-001' => 35,
            'PRD-CUP-012' => 150,
            'PRD-SUSU-001' => 18,
        ];

        foreach ($stocks as $productCode => $stock) {
            $product = Product::query()->where('code', $productCode)->first();

            if (! $product) {
                continue;
            }

            Inventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'hub_id' => $hub->id,
                ],
                [
                    'current_stock' => $stock,
                    'reserved_stock' => 0,
                    'available_stock' => $stock,
                    'last_updated_at' => now(),
                ],
            );
        }
    }
}
