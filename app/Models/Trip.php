<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;

    protected $fillable = ['vendor_id', 'title', 'slug', 'type', 'destination', 'description', 'itinerary', 'meeting_point', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price', 'status'];

    protected function casts(): array
    {
        return ['departure_date' => 'date', 'end_date' => 'date', 'price' => 'integer', 'capacity' => 'integer', 'reserved_seats' => 'integer'];
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class);
    }

}
