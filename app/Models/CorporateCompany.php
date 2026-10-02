<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateCompany extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'pic_name', 'position', 'work_email', 'phone', 'budget_range', 'source', 'status', 'verification_note', 'monthly_limit', 'consented_at', 'marketing_consent'];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime', 'marketing_consent' => 'boolean', 'monthly_limit' => 'integer'];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CorporateMembership::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CorporateRequest::class);
    }
}
