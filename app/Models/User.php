<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'phone', 'city', 'google_id', 'avatar', 'status', 'email_verified_at', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = ['status' => 'active'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(RewardEntry::class);
    }

    public function getPointsAttribute(): int
    {
        return (int) RewardEntry::where('user_id', $this->id)->sum('points');
    }

    public function getTierAttribute(): string
    {
        $pts = $this->points;
        if ($pts >= 5000) {
            return 'Gold';
        }
        if ($pts >= 1000) {
            return 'Silver';
        }

        return 'Bronze';
    }

    public function getUsernameAttribute(): string
    {
        return Str::before($this->email, '@') ?: Str::slug($this->name, '');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        $this->loadMissing('roles.permissions');

        return $this->roles->contains(fn (Role $role) => $role->permissions->contains('name', $permission));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
