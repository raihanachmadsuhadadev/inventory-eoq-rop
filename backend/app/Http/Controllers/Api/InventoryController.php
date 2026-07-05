<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data inventaris berhasil dimuat',
            'data' => Inventory::query()
                ->with([
                    'product:id,category_id,supplier_id,code,name,unit,minimum_stock,is_active',
                    'product.category:id,name,code',
                    'product.supplier:id,name,code',
                    'hub:id,name,code',
                ])
                ->latest('last_updated_at')
                ->get(),
        ]);
    }

    public function show(Inventory $inventory): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail inventaris berhasil dimuat',
            'data' => $inventory->load([
                'product:id,category_id,supplier_id,code,name,unit,minimum_stock,is_active',
                'product.category:id,name,code',
                'product.supplier:id,name,code',
                'hub:id,name,code',
            ]),
        ]);
    }
}
