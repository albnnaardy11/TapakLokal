<?php

namespace App\Models;

use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['category', 'question', 'answer', 'status', 'position'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }
}
