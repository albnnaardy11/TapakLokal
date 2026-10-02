<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirCartItem extends Model
{
    protected $fillable = ['user_id', 'souvenir_product_id', 'variant', 'quantity', 'note'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SouvenirProduct::class, 'souvenir_product_id');
    }
}
