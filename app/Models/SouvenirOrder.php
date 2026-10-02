<?php

namespace App\Models;

use Database\Factories\SouvenirOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SouvenirOrder extends Model
{
    /** @use HasFactory<SouvenirOrderFactory> */
    use HasFactory;

    protected $fillable = ['contact_email', 'reference', 'user_id', 'vendor_id', 'idempotency_key', 'request_hash', 'contact_name', 'contact_phone', 'method', 'address', 'pickup_date', 'delivery_service', 'shipping_fee', 'subtotal', 'total', 'vendor_amount', 'platform_fee', 'status', 'expires_at', 'tracking_number', 'completed_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'completed_at' => 'datetime', 'pickup_date' => 'date', 'shipping_fee' => 'integer', 'subtotal' => 'integer', 'total' => 'integer', 'vendor_amount' => 'integer', 'platform_fee' => 'integer'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(SouvenirOrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(SouvenirPayment::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
