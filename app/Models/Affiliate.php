<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    /** @use HasFactory<\Database\Factories\AffiliateFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'commission_bps', 'status'];

    protected function casts(): array
    {
        return ['commission_bps' => 'integer'];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
