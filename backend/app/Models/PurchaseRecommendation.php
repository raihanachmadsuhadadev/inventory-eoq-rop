<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'hub_id',
    'eoq_calculation_id',
    'rop_calculation_id',
    'current_stock',
    'rop_value',
    'recommended_quantity',
    'status',
    'notes',
    'verified_by',
    'verified_at',
    'created_by',
])]
class PurchaseRecommendation extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function eoqCalculation(): BelongsTo
    {
        return $this->belongsTo(EoqCalculation::class);
    }

    public function ropCalculation(): BelongsTo
    {
        return $this->belongsTo(RopCalculation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'hub_id' => 'integer',
            'eoq_calculation_id' => 'integer',
            'rop_calculation_id' => 'integer',
            'current_stock' => 'integer',
            'rop_value' => 'integer',
            'recommended_quantity' => 'integer',
            'verified_by' => 'integer',
            'created_by' => 'integer',
            'verified_at' => 'datetime',
        ];
    }
}
