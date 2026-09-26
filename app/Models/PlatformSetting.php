<?php

namespace App\Models;

use Database\Factories\PlatformSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    /** @use HasFactory<PlatformSettingFactory> */
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(function () {
            cache()->forget('platform.markup_bps');
        });

        static::deleted(function () {
            cache()->forget('platform.markup_bps');
        });
    }
}
