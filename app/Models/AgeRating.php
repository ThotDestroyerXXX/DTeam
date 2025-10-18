<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeRating extends Model
{
    /** @use HasFactory<\Database\Factories\AgeRatingFactory> */
    use HasFactory, Cacheable;

    protected $fillable = [
        'title',
        'image_url',
        'description',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
