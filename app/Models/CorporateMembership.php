<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateMembership extends Model
{
    use HasFactory;

    protected $fillable = ['corporate_company_id', 'user_id', 'role', 'status'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CorporateCompany::class, 'corporate_company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
