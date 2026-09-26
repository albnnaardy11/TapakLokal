<?php

namespace App\Models;

use Database\Factories\VirtualTourFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualTour extends Model
{
    /** @use HasFactory<VirtualTourFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'status', 'placement', 'media_asset_id', 'trip_id', 'content_page_id', 'position', 'source_hash'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(function (Builder $query) {
            $query->where('placement', 'homepage')
                ->orWhere(fn (Builder $q) => $q->where('placement', 'trip')->whereHas('trip', fn (Builder $trip) => $trip->where('status', 'published')->whereHas('vendor', fn (Builder $vendor) => $vendor->where('status', 'verified'))))
                ->orWhere(fn (Builder $q) => $q->where('placement', 'destination')->whereHas('contentPage', fn (Builder $page) => $page->where('status', 'published')->whereIn('type', ['destination', 'hidden-gem'])->where(fn (Builder $date) => $date->whereNull('published_at')->orWhere('published_at', '<=', now()))));
        });
    }

    /** @return array<string, mixed> */
    public function presentation(bool $preview = false): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'description' => $this->description, 'image_url' => route($preview ? 'admin.tours.preview' : 'tours.image', ['tour' => $this->id])];
    }
}
