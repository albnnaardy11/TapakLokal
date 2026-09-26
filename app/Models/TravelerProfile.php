<?php

namespace App\Models;

use Database\Factories\TravelerProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelerProfile extends Model
{
    /** @use HasFactory<TravelerProfileFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'birth_date', 'phone', 'emergency_contact'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
