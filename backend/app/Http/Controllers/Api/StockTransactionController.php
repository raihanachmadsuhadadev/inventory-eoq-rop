<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\StockTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data transaksi stok berhasil dimuat',
            'data' => StockTransaction::query()
                ->with([
                    'product:id,code,name,unit',
                    'hub:id,name,code',
                    'creator:id,name,email',
                ])
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'hub_id' => ['required', 'integer', 'exists:hubs,id'],
            'type' => ['required', 'string', Rule::in(['in', 'out', 'adjustment'])],
            'quantity' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if (in_array($data['type'], ['in', 'out'], true) && $data['quantity'] < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity untuk transaksi masuk dan keluar minimal 1.',
            ]);
        }

        $transaction = DB::transaction(function () use ($data, $request): StockTransaction {
            $inventory = Inventory::query()
                ->where('product_id', $data['product_id'])
                ->where('hub_id', $data['hub_id'])
                ->lockForUpdate()
                ->first();

            if (! $inventory && $data['type'] === 'out') {
                throw ValidationException::withMessages([
                    'product_id' => 'Inventory untuk produk dan hub ini belum tersedia.',
                ]);
            }

            if (! $inventory) {
                $inventory = Inventory::create([
                    'product_id' => $data['product_id'],
                    'hub_id' => $data['hub_id'],
                    'current_stock' => 0,
                    'reserved_stock' => 0,
                    'available_stock' => 0,
                ]);
            }

            $stockBefore = $inventory->current_stock;
            $stockAfter = match ($data['type']) {
                'in' => $stockBefore + $data['quantity'],
                'out' => $stockBefore - $data['quantity'],
                'adjustment' => $data['quantity'],
            };

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi untuk transaksi keluar.',
                ]);
            }

            $inventory->update([
                'current_stock' => $stockAfter,
                'available_stock' => $stockAfter - $inventory->reserved_stock,
                'last_updated_at' => now(),
            ]);

            return StockTransaction::create([
                'product_id' => $data['product_id'],
                'hub_id' => $data['hub_id'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi stok berhasil disimpan',
            'data' => $transaction->load([
                'product:id,code,name,unit',
                'hub:id,name,code',
                'creator:id,name,email',
            ]),
        ], 201);
    }

    public function show(StockTransaction $stockTransaction): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi stok berhasil dimuat',
            'data' => $stockTransaction->load([
                'product:id,code,name,unit',
                'hub:id,name,code',
                'creator:id,name,email',
            ]),
        ]);
    }
}
