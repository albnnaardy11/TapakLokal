<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    /** @use HasFactory<\Database\Factories\ContentPageFactory> */
    use HasFactory;

    protected $fillable = ['type', 'title', 'slug', 'excerpt', 'body', 'image_url', 'status', 'published_at', 'position'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'position' => 'integer'];
    }

}
