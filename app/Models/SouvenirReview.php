<?php

namespace App\Models;

use Database\Factories\SouvenirReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirReview extends Model
{
    /** @use HasFactory<SouvenirReviewFactory> */
    use HasFactory;

    protected $fillable = ['souvenir_order_item_id', 'souvenir_product_id', 'user_id', 'rating', 'body', 'vendor_response', 'status'];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SouvenirProduct::class, 'souvenir_product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
