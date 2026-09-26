<?php

namespace App\Models;

use Database\Factories\AffiliateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affiliate extends Model
{
    /** @use HasFactory<AffiliateFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'commission_bps', 'status'];

    protected function casts(): array
    {
        return ['commission_bps' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
