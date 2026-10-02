<?php

namespace Database\Factories;

use App\Models\SouvenirProduct;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SouvenirProduct> */
class SouvenirProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory()->state(['status' => 'verified']),
            'slug' => fake()->unique()->slug(), 'name' => 'Kopi lokal 250 gram',
            'category' => 'Kopi & minuman', 'region' => 'Jawa Tengah',
            'description' => 'Kopi sangrai produksi mitra lokal.', 'variants' => ['Biji utuh', 'Giling halus'],
            'price' => 50000, 'stock' => 20, 'reserved_stock' => 0, 'weight' => 250,
            'preparation_days' => 1, 'availability' => 'Ready stock', 'pickup_only' => true,
            'pickup_address' => 'Jalan Mitra Lokal nomor 10, Semarang', 'status' => 'published',
        ];
    }
}
