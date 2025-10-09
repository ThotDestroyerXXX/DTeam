<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameImage extends Model
{
    /** @use HasFactory<\Database\Factories\GameImageFactory> */
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'game_id',
        'image_url',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
