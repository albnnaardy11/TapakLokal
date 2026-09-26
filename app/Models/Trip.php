<?php

namespace App\Models;

use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    /** @use HasFactory<TripFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['vendor_id', 'title', 'slug', 'type', 'destination', 'description', 'itinerary', 'meeting_point', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price', 'status', 'experience'];

    protected $appends = ['selling_price'];

    public function getSellingPriceAttribute(): int
    {
        $bps = cache()->remember('platform.markup_bps', 60, function () {
            if (! app()->environment('testing')) {
                $percent = PlatformSetting::where('key', 'markup_percent')->value('value');
                if ($percent !== null && is_numeric($percent)) {
                    return (int) round(((float) $percent) * 100);
                }

                $setting = PlatformSetting::where('key', 'markup_bps')->value('value');
                if ($setting !== null && is_numeric($setting)) {
                    return (int) $setting;
                }
            }

            return (int) config('platform.markup_bps', 1000);
        });

        return $this->price + intdiv($this->price * max(0, min(10000, (int) $bps)), 10000);
    }

    protected function casts(): array
    {
        return ['experience' => 'array', 'departure_date' => 'date', 'end_date' => 'date', 'price' => 'integer', 'capacity' => 'integer', 'reserved_seats' => 'integer'];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
