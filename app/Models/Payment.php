<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = ['booking_id', 'reference', 'amount', 'status', 'provider', 'provider_reference', 'checkout_url', 'paid_at', 'reconciled_at'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'reconciled_at' => 'datetime', 'amount' => 'integer'];
    }

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

}
