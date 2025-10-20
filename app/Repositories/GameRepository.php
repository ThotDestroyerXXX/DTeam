<?php

namespace App\Repositories;

use App\Models\Game;
use App\Models\GameGenre;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class GameRepository implements GameRepositoryInterface
{
    /**
     * Get all games by publisher
     *
     * @param Publisher $publisher
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublisherGames(Publisher $publisher, int $perPage = 10): LengthAwarePaginator
    {
        return $publisher->games()
            ->with(['gameImages', 'genres', 'ageRating'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a game by ID for a publisher
     *
     * @param Publisher $publisher
     * @param int $gameId
     * @return Game|null
     */
    public function getPublisherGame(Publisher $publisher, string $gameId): ?Game
    {
        return $publisher->games()
            ->with(['genres', 'gameImages', 'ageRating'])
            ->findOrFail($gameId);
    }

    /**
     * Create a new game
     *
     * @param Publisher $publisher
     * @param array $data
     * @return Game
     */
    public function createGame(Publisher $publisher, array $data): Game
    {
        return $publisher->games()->create([
            'title' => $data['title'],
            'brief_description' => $data['brief_description'],
            'full_description' => $data['full_description'],
            'price' => $data['price'],
            'release_date' => $data['release_date'],
            'discount_percentage' => $data['discount'] ?? 0,
            'age_rating_id' => $data['age_rating_id'],
        ]);
    }

    /**
     * Update an existing game
     *
     * @param Game $game
     * @param array $data
     * @return bool
     */
    public function updateGame(Game $game, array $data): bool
    {
        $result = $game->update([
            'title' => $data['title'],
            'brief_description' => $data['brief_description'],
            'full_description' => $data['full_description'],
            'price' => $data['price'],
            'release_date' => $data['release_date'],
            'discount_percentage' => $data['discount'],
            'age_rating_id' => $data['age_rating_id'],
        ]);

        // Clear any cache related to this game
        $this->clearGameCache($game->id);

        return $result;
    }

    /**
     * Get game detail by ID with caching
     *
     * @param int $gameId
     * @return Game|null
     */
    public function getGameDetail(string $gameId): ?Game
    {
        $cacheKey = 'game_detail_' . $gameId;

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($gameId) {
            return Game::with([
                'publisher',
                'ageRating',
                'genres',
                'gameImages',
            ])->findOrFail($gameId);
        });
    }

    /**
     * Search games by query and filter
     *
     * @param string|null $query
     * @param int|null $genreId
     * @param array $relations
     * @return Collection
     */
    public function searchGames(?string $query, ?int $genreId = null, array $relations = []): Collection
    {
        $relations = $relations ?: ['genres', 'gameImages', 'publisher', 'ageRating'];

        $gamesQuery = Game::with($relations);

        if ($query) {
            $gamesQuery->where('title', 'like', "%{$query}%");
        }

        if ($genreId && $genreId !== 'all') {
            $gamesQuery->whereHas('genres', function ($q) use ($genreId) {
                $q->where('id', $genreId);
            });
        }

        return $gamesQuery->get();
    }

    /**
     * Get games by genre
     *
     * @param int $genreId
     * @param array $relations
     * @return Collection
     */
    public function getGamesByGenre(int $genreId, array $relations = []): Collection
    {
        $relations = $relations ?: ['gameImages', 'publisher', 'ageRating'];

        return Game::whereHas('genres', function ($query) use ($genreId) {
            $query->where('genres.id', $genreId);
        })->with($relations)->get();
    }

    /**
     * Attach genres to a game
     *
     * @param Game $game
     * @param array $genreIds
     * @return void
     */
    public function attachGenres(Game $game, array $genreIds): void
    {
        foreach ($genreIds as $genreId) {
            GameGenre::create([
                'game_id' => $game->id,
                'genre_id' => $genreId,
            ]);
        }
    }

    /**
     * Detach all genres from a game
     *
     * @param Game $game
     * @return void
     */
    public function detachGenres(Game $game): void
    {
        $game->genres()->detach();
    }

    /**
     * Clear cache for a game
     *
     * @param int $gameId
     * @return void
     */
    private function clearGameCache(int $gameId): void
    {
        Cache::forget('game_detail_' . $gameId);
        Cache::forget('recent_reviews_' . $gameId . '_' . now()->startOfMonth()->format('Y-m'));
        Cache::forget('all_reviews_' . $gameId);
    }
}
