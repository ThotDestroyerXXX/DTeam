<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class GameReview extends Model
{
    /** @use HasFactory<\Database\Factories\GameReviewFactory> */
    use HasFactory, HasUlids, Cacheable;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear related caches when a review is created, updated or deleted
        static::saved(function ($review) {
            // Clear game-specific caches
            $gameId = $review->game_id;
            $startOfMonth = now()->startOfMonth()->format('Y-m');

            Cache::forget('games_recent_reviews_' . $gameId . '_' . $startOfMonth);
            Cache::forget('games_all_reviews_' . $gameId);
            Cache::forget('games_detail_' . $gameId);

            // Clear the game's cache as well
            if ($review->game) {
                $review->game->flushCache();
            }
        });

        static::deleted(function ($review) {
            // Same cache clearing logic as saved event
            $gameId = $review->game_id;
            $startOfMonth = now()->startOfMonth()->format('Y-m');

            Cache::forget('games_recent_reviews_' . $gameId . '_' . $startOfMonth);
            Cache::forget('games_all_reviews_' . $gameId);
            Cache::forget('games_detail_' . $gameId);

            // Try to clear the game's cache if the relationship is still accessible
            try {
                if ($game = Game::find($gameId)) {
                    $game->flushCache();
                }
            } catch (\Exception $e) {
                // Log the error but don't halt execution
                \Illuminate\Support\Facades\Log::error('Failed to flush game cache on review deletion: ' . $e->getMessage());
            }
        });
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function ratingType(): BelongsTo
    {
        return $this->belongsTo(RatingType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
