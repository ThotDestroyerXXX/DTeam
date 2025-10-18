<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, Cacheable;

    protected $fillable = [
        'name',
        'image_url',
        'type',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'item_libraries');
    }
}
