<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardEntry extends Model
{
    /** @use HasFactory<\Database\Factories\RewardEntryFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'booking_id', 'reference', 'points', 'description'];

    protected function casts(): array
    {
        return ['points' => 'integer'];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
