<?php

namespace App\Models;

use Database\Factories\SouvenirOrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SouvenirOrderItem extends Model
{
    /** @use HasFactory<SouvenirOrderItemFactory> */
    use HasFactory;

    protected $fillable = ['souvenir_order_id', 'souvenir_product_id', 'name', 'variant', 'quantity', 'unit_price', 'vendor_price', 'note'];
}
