<?php

namespace Database\Seeders;

use App\Models\Hub;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockTransactionSeeder extends Seeder
{
    /**
     * Seed initial stock transactions.
     */
    public function run(): void
    {
        $hub = Hub::query()->where('code', 'HUB-PST')->first();
        $user = User::query()->where('email', 'superadmin@inventory.test')->first();

        if (! $hub) {
            return;
        }

        $transactions = [
            ['code' => 'PRD-KOPI-001', 'quantity' => 35, 'notes' => 'Stok awal kopi arabica'],
            ['code' => 'PRD-CUP-012', 'quantity' => 150, 'notes' => 'Stok awal cup paper'],
            ['code' => 'PRD-SUSU-001', 'quantity' => 18, 'notes' => 'Stok awal susu UHT'],
        ];

        foreach ($transactions as $transaction) {
            $product = Product::query()->where('code', $transaction['code'])->first();

            if (! $product) {
                continue;
            }

            $inventory = Inventory::query()
                ->where('product_id', $product->id)
                ->where('hub_id', $hub->id)
                ->first();

            if (! $inventory) {
                continue;
            }

            StockTransaction::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'hub_id' => $hub->id,
                    'type' => 'in',
                    'notes' => $transaction['notes'],
                ],
                [
                    'quantity' => $transaction['quantity'],
                    'stock_before' => 0,
                    'stock_after' => $inventory->current_stock,
                    'created_by' => $user?->id,
                ],
            );
        }
    }
}
