<?php

namespace App\Models;

use Database\Factories\SouvenirPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirPayment extends Model
{
    /** @use HasFactory<SouvenirPaymentFactory> */
    use HasFactory;

    protected $fillable = ['method', 'instructions', 'souvenir_order_id', 'reference', 'amount', 'status', 'checkout_url', 'provider_reference', 'paid_at', 'reconciled_at'];

    protected function casts(): array
    {
        return ['instructions' => 'array', 'amount' => 'integer', 'paid_at' => 'datetime', 'reconciled_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SouvenirOrder::class, 'souvenir_order_id');
    }
}
