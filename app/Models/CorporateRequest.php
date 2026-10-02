<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateRequest extends Model
{
    use HasFactory;

    protected $fillable = ['corporate_company_id', 'user_id', 'reference', 'idempotency_key', 'title', 'destination', 'departure_date', 'end_date', 'participants', 'budget', 'cost_center', 'needs', 'status', 'trip_id', 'quote_total', 'quote', 'quote_history', 'quote_expires_at', 'approved_by', 'approved_at', 'decision_note', 'booking_id', 'travelers'];

    protected function casts(): array
    {
        return ['departure_date' => 'date', 'end_date' => 'date', 'participants' => 'integer', 'budget' => 'integer', 'quote_total' => 'integer', 'travelers' => 'array', 'quote' => 'array', 'quote_history' => 'array', 'quote_expires_at' => 'datetime', 'approved_at' => 'datetime'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CorporateCompany::class, 'corporate_company_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
