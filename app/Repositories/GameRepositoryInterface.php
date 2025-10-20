<?php

namespace App\Repositories;

use App\Models\Game;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface GameRepositoryInterface
{
    /**
     * Get all games by publisher
     *
     * @param Publisher $publisher
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublisherGames(Publisher $publisher, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get a game by ID for a publisher
     *
     * @param Publisher $publisher
     * @param int $gameId
     * @return Game|null
     */
    public function getPublisherGame(Publisher $publisher, string $gameId): ?Game;

    /**
     * Create a new game
     *
     * @param Publisher $publisher
     * @param array $data
     * @return Game
     */
    public function createGame(Publisher $publisher, array $data): Game;

    /**
     * Update an existing game
     *
     * @param Game $game
     * @param array $data
     * @return bool
     */
    public function updateGame(Game $game, array $data): bool;

    /**
     * Get game detail by ID
     *
     * @param int $gameId
     * @return Game|null
     */
    public function getGameDetail(string $gameId): ?Game;

    /**
     * Search games by query and filter
     *
     * @param string|null $query
     * @param int|null $genreId
     * @param array $relations
     * @return Collection
     */
    public function searchGames(?string $query, ?int $genreId = null, array $relations = []): Collection;

    /**
     * Get games by genre
     *
     * @param int $genreId
     * @param array $relations
     * @return Collection
     */
    public function getGamesByGenre(int $genreId, array $relations = []): Collection;

    /**
     * Attach genres to a game
     *
     * @param Game $game
     * @param array $genreIds
     * @return void
     */
    public function attachGenres(Game $game, array $genreIds): void;

    /**
     * Detach all genres from a game
     *
     * @param Game $game
     * @return void
     */
    public function detachGenres(Game $game): void;
}
