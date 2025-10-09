<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RatingType extends Model
{
    /** @use HasFactory<\Database\Factories\RatingTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'image_url',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_reviews');
    }

    public function gameReviews(): BelongsToMany
    {
        return $this->belongsToMany(GameReview::class, 'game_reviews');
    }
}
