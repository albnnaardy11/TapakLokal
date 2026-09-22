<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory;

    protected $fillable = ['name', 'image_url', 'website_url', 'status', 'position'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

}
