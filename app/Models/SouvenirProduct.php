<?php

namespace App\Models;

use Database\Factories\SouvenirProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirProduct extends Model
{
    /** @use HasFactory<SouvenirProductFactory> */
    use HasFactory;

    protected $fillable = ['vendor_id', 'slug', 'name', 'category', 'region', 'description', 'care', 'image_url', 'variants', 'price', 'stock', 'reserved_stock', 'weight', 'preparation_days', 'availability', 'pickup_only', 'pickup_address', 'delivery_rates', 'status'];

    protected function casts(): array
    {
        return ['variants' => 'array', 'delivery_rates' => 'array', 'pickup_only' => 'boolean', 'price' => 'integer', 'stock' => 'integer', 'reserved_stock' => 'integer', 'weight' => 'integer', 'preparation_days' => 'integer'];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function sellingPrice(): int
    {
        return $this->price + intdiv($this->price * max(800, min(1500, (int) config('platform.markup_bps'))), 10000);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')->whereHas('vendor', fn ($vendor) => $vendor->where('status', 'verified')->whereHas('user', fn ($user) => $user->where('status', 'active')));
    }
}
