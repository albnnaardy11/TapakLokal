<?php

namespace App\Models;

use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<PromotionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'value', 'minimum_amount', 'maximum_discount', 'usage_limit', 'used_count', 'starts_at', 'ends_at', 'status'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date', 'value' => 'integer', 'minimum_amount' => 'integer', 'maximum_discount' => 'integer'];
    }
}
