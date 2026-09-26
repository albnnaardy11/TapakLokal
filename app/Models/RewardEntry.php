<?php

namespace App\Models;

use Database\Factories\RewardEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardEntry extends Model
{
    /** @use HasFactory<RewardEntryFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'booking_id', 'reference', 'points', 'description'];

    protected function casts(): array
    {
        return ['points' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
