<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MediaAsset> */
class MediaAssetFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'name' => 'document.pdf', 'disk' => 'local', 'path' => 'media/example.pdf', 'mime_type' => 'application/pdf', 'size' => 1000, 'visibility' => 'private'];
    }
}
