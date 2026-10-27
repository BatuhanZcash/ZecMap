<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'bio',
        'avatar_url',
        'last_sign_in_at',
        'payout_address',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ROLE_ADMIN = 'admin';

    public const ROLE_USER = 'user';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public static function normalizeUsername(string $value): string
    {
        return Str::lower(trim($value));
    }

    public function recordSignIn(): void
    {
        $this->forceFill(['last_sign_in_at' => now()])->save();
    }

    public function initials(): string
    {
        $initial = mb_substr(trim($this->name), 0, 1);

        return mb_strtoupper($initial !== '' ? $initial : '?');
    }

    public function businessReviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function businessVerifications(): HasMany
    {
        return $this->hasMany(BusinessVerification::class);
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function savedBusinesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_favorites')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function avatarDisplayUrl(): ?string
    {
        if (! $this->avatar_url) {
            return null;
        }

        if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
            return $this->avatar_url;
        }

        return asset(ltrim($this->avatar_url, '/'));
    }

    public function displayName(): string
    {
        if (trim($this->name) !== '') {
            return $this->name;
        }

        return $this->username ?? '';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_sign_in_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
