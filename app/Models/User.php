<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nickname',
        'real_name',
        'profile_picture_url',
        'role',
        'bio',
        'unique_code',
        'wallet',
        'point',
        'background_url',
        'email',
        'password',
        'country_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function friendLists(): HasMany
    {
        return $this->hasMany(FriendList::class);
    }

    public function friendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_libraries');
    }

    public function gameGifts(): HasMany
    {
        return $this->hasMany(GameGift::class);
    }

    public function walletCodes(): HasMany
    {
        return $this->hasMany(WalletCode::class);
    }

    public function gameWishlists(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_wishlists');
    }

    public function gameCarts(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_carts');
    }

    public function gameLibraries(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_libraries');
    }

    public function publisher(): HasOne
    {
        return $this->hasOne(Publisher::class);
    }

    public function gameReviews(): HasMany
    {
        return $this->hasMany(GameReview::class)->with(['ratingType']);
    }

    public function gameReviewsWithGame(): HasMany
    {
        return $this->hasMany(GameReview::class)->with(['game', 'ratingType']);
    }

    public function isProfileComplete(): bool
    {
        //check all the fields for profile completion
        return $this->nickname && $this->real_name && $this->bio && $this->country_id;
    }

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }
}
