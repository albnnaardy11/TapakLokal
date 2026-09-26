<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = ['booking_id', 'reference', 'amount', 'status', 'provider', 'provider_reference', 'checkout_url', 'paid_at', 'reconciled_at'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'reconciled_at' => 'datetime', 'amount' => 'integer'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
