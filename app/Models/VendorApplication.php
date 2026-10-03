<?php

namespace App\Models;

use Database\Factories\VendorApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplication extends Model
{
    /** @use HasFactory<VendorApplicationFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'track', 'business', 'name', 'city', 'contact', 'email', 'phone', 'notes', 'status', 'review_note'];
}
