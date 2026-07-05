<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseRecommendation;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Ringkasan dashboard berhasil dimuat',
            'data' => [
                'total_products' => Product::query()->count(),
                'total_suppliers' => Supplier::query()->count(),
                'total_inventory_stock' => Inventory::query()->sum('current_stock'),
                'critical_stock_count' => $this->criticalStockQuery()->count(),
                'reorder_recommendation_count' => PurchaseRecommendation::query()->count(),
                'pending_recommendation_count' => PurchaseRecommendation::query()
                    ->where('status', 'pending')
                    ->count(),
            ],
        ]);
    }

    public function criticalStock(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);

        return response()->json([
            'success' => true,
            'message' => 'Data stok kritis berhasil dimuat',
            'data' => $this->criticalStockQuery()
                ->with([
                    'product:id,category_id,supplier_id,code,name,unit,minimum_stock',
                    'product.category:id,name,code',
                    'product.supplier:id,name,code',
                    'hub:id,name,code',
                ])
                ->limit($limit)
                ->get(),
        ]);
    }

    public function reorderAlerts(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);

        return response()->json([
            'success' => true,
            'message' => 'Data rekomendasi pending berhasil dimuat',
            'data' => PurchaseRecommendation::query()
                ->with([
                    'product:id,code,name,unit',
                    'hub:id,name,code',
                    'eoqCalculation:id,eoq_result',
                    'ropCalculation:id,rop_result,stock_status',
                ])
                ->where('status', 'pending')
                ->latest()
                ->limit($limit)
                ->get(),
        ]);
    }

    private function criticalStockQuery()
    {
        return Inventory::query()
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereColumn('inventories.current_stock', '<=', 'products.minimum_stock')
            ->select('inventories.*')
            ->latest('inventories.last_updated_at');
    }
}
