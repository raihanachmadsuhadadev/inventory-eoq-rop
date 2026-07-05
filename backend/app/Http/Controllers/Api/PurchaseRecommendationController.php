<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EoqCalculation;
use App\Models\Inventory;
use App\Models\PurchaseRecommendation;
use App\Models\RopCalculation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseRecommendationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data rekomendasi pemesanan berhasil dimuat',
            'data' => $this->queryWithRelations()->latest()->get(),
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $latestRops = RopCalculation::query()
            ->with('product:id,code,name,minimum_stock')
            ->latest('calculated_at')
            ->get()
            ->unique(fn (RopCalculation $rop) => $rop->product_id.'|'.($rop->hub_id ?? 'none'));

        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        foreach ($latestRops as $rop) {
            $currentStock = $this->currentStock($rop);
            $ropValue = (int) ceil((float) $rop->rop_result);

            if ($currentStock > $ropValue) {
                $summary['skipped']++;
                continue;
            }

            $eoq = EoqCalculation::query()
                ->where('product_id', $rop->product_id)
                ->latest('calculated_at')
                ->first();
            $recommendedQuantity = $eoq
                ? $eoq->eoq_result
                : max($ropValue - $currentStock, 1);
            $pendingRecommendation = PurchaseRecommendation::query()
                ->where('product_id', $rop->product_id)
                ->where('status', 'pending')
                ->when(
                    $rop->hub_id,
                    fn ($query) => $query->where('hub_id', $rop->hub_id),
                    fn ($query) => $query->whereNull('hub_id'),
                )
                ->first();

            $payload = [
                'product_id' => $rop->product_id,
                'hub_id' => $rop->hub_id,
                'eoq_calculation_id' => $eoq?->id,
                'rop_calculation_id' => $rop->id,
                'current_stock' => $currentStock,
                'rop_value' => $ropValue,
                'recommended_quantity' => $recommendedQuantity,
                'notes' => 'Dibuat otomatis berdasarkan ROP terbaru.',
                'created_by' => $request->user()?->id,
            ];

            if ($pendingRecommendation) {
                $pendingRecommendation->update($payload);
                $summary['updated']++;
                continue;
            }

            PurchaseRecommendation::create($payload + ['status' => 'pending']);
            $summary['created']++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi pemesanan berhasil dibuat',
            'data' => $summary,
        ]);
    }

    public function show(PurchaseRecommendation $purchaseRecommendation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail rekomendasi pemesanan berhasil dimuat',
            'data' => $purchaseRecommendation->load($this->relations()),
        ]);
    }

    public function approve(Request $request, PurchaseRecommendation $purchaseRecommendation): JsonResponse
    {
        return $this->verify($request, $purchaseRecommendation, 'approved', 'Rekomendasi pemesanan disetujui');
    }

    public function reject(Request $request, PurchaseRecommendation $purchaseRecommendation): JsonResponse
    {
        return $this->verify($request, $purchaseRecommendation, 'rejected', 'Rekomendasi pemesanan ditolak');
    }

    private function verify(
        Request $request,
        PurchaseRecommendation $purchaseRecommendation,
        string $status,
        string $message,
    ): JsonResponse {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $purchaseRecommendation->update([
            'status' => $status,
            'notes' => array_key_exists('notes', $data)
                ? $data['notes']
                : $purchaseRecommendation->notes,
            'verified_by' => $request->user()?->id,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $purchaseRecommendation->refresh()->load($this->relations()),
        ]);
    }

    private function currentStock(RopCalculation $rop): int
    {
        if (! $rop->hub_id) {
            return $rop->current_stock;
        }

        return Inventory::query()
            ->where('product_id', $rop->product_id)
            ->where('hub_id', $rop->hub_id)
            ->value('current_stock') ?? 0;
    }

    private function queryWithRelations()
    {
        return PurchaseRecommendation::query()->with($this->relations());
    }

    private function relations(): array
    {
        return [
            'product:id,code,name,unit',
            'hub:id,name,code',
            'eoqCalculation:id,eoq_result',
            'ropCalculation:id,rop_result,stock_status',
            'verifier:id,name,email',
            'creator:id,name,email',
        ];
    }
}
