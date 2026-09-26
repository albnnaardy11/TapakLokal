<?php

namespace App\Models;

use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    /** @use HasFactory<PartnerFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['name', 'image_url', 'website_url', 'status', 'position'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }
}
