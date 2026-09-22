<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelerProfile extends Model
{
    /** @use HasFactory<\Database\Factories\TravelerProfileFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'birth_date', 'phone', 'emergency_contact'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
