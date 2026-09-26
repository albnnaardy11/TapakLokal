<?php

namespace App\Models;

use Database\Factories\ContentPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentPage extends Model
{
    /** @use HasFactory<ContentPageFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['type', 'title', 'slug', 'excerpt', 'body', 'image_url', 'status', 'published_at', 'position', 'metadata', 'category'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'published_at' => 'datetime', 'position' => 'integer'];
    }
}
