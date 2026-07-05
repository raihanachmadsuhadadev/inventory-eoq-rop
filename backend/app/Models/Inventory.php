<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'hub_id',
    'current_stock',
    'reserved_stock',
    'available_stock',
    'last_updated_at',
])]
class Inventory extends Model
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

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'hub_id' => 'integer',
            'current_stock' => 'integer',
            'reserved_stock' => 'integer',
            'available_stock' => 'integer',
            'last_updated_at' => 'datetime',
        ];
    }
}
