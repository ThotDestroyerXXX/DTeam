<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'trailer_url',
        'brief_description',
        'full_description',
        'release_date',
        'discount_percentage',
        'publisher',
        'price',
        'age_rating_id',
    ];

    public function ageRating(): BelongsTo
    {
        return $this->belongsTo(AgeRating::class);
    }

    public function gameReviews(): HasMany
    {
        return $this->hasMany(GameReview::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'game_genres');
    }

    public function gameImages(): HasMany
    {
        return $this->hasMany(GameImage::class);
    }

    public function gameCarts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_carts');
    }

    public function gameWishlists(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_wishlists');
    }

    public function gameLibraries(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_libraries');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function gameGifts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_gifts');
    }
}
