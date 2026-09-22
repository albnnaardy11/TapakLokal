<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    /** @use HasFactory<\Database\Factories\MediaAssetFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'disk', 'path', 'mime_type', 'size', 'alt_text', 'visibility'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
