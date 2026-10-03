<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentShare extends Model
{
    protected $fillable = ['booking_id', 'reference', 'token', 'label', 'position', 'amount', 'status', 'method', 'instructions', 'provider_reference', 'paid_at', 'reconciled_at'];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return ['instructions' => 'array', 'amount' => 'integer', 'paid_at' => 'datetime', 'reconciled_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
