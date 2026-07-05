<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EoqCalculation;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\RopCalculation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function inventory(Request $request): JsonResponse
    {
        $query = Inventory::query()
            ->with([
                'product:id,category_id,supplier_id,code,name,unit,minimum_stock',
                'product.category:id,name,code',
                'product.supplier:id,name,code',
                'hub:id,name,code',
            ]);

        if ($request->filled('hub_id')) {
            $query->where('hub_id', $request->integer('hub_id'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', fn ($productQuery) => $productQuery
                ->where('category_id', $request->integer('category_id')));
        }

        $inventories = $query->latest('last_updated_at')->get()->map(function (Inventory $inventory) {
            $status = $inventory->current_stock <= ($inventory->product?->minimum_stock ?? 0)
                ? 'critical'
                : 'safe';

            return [
                'id' => $inventory->id,
                'product' => $inventory->product,
                'hub' => $inventory->hub,
                'current_stock' => $inventory->current_stock,
                'available_stock' => $inventory->available_stock,
                'minimum_stock' => $inventory->product?->minimum_stock ?? 0,
                'status' => $status,
                'last_updated_at' => $inventory->last_updated_at,
            ];
        });

        if ($request->filled('status')) {
            $inventories = $inventories
                ->where('status', $request->string('status')->toString())
                ->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan inventaris berhasil dimuat',
            'data' => $inventories,
        ]);
    }

    public function eoqRop(): JsonResponse
    {
        $latestEoqs = EoqCalculation::query()
            ->with(['product:id,code,name,unit'])
            ->latest('calculated_at')
            ->get()
            ->unique('product_id')
            ->keyBy('product_id');
        $latestRops = RopCalculation::query()
            ->with(['product:id,code,name,unit', 'hub:id,name,code'])
            ->latest('calculated_at')
            ->get()
            ->unique('product_id')
            ->keyBy('product_id');

        $rows = Product::query()
            ->select(['id', 'code', 'name', 'unit'])
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) use ($latestEoqs, $latestRops) {
                $latestEoq = $latestEoqs->get($product->id);
                $latestRop = $latestRops->get($product->id);
                $currentStock = $latestRop
                    ? $latestRop->current_stock
                    : Inventory::query()->where('product_id', $product->id)->sum('current_stock');

                return [
                    'product' => $product,
                    'latest_eoq' => $latestEoq,
                    'latest_rop' => $latestRop,
                    'current_stock' => $currentStock,
                    'status' => $latestRop?->stock_status,
                    'eoq_calculated_at' => $latestEoq?->calculated_at,
                    'rop_calculated_at' => $latestRop?->calculated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Laporan EOQ dan ROP berhasil dimuat',
            'data' => $rows,
        ]);
    }
}
