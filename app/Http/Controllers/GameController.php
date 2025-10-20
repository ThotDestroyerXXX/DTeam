<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Repositories\GameRepositoryInterface;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GameController extends Controller
{
    /**
     * @var GameRepositoryInterface
     */
    protected $gameRepository;

    /**
     * @var GameService
     */
    protected $gameService;

    /**
     * GameController constructor.
     *
     * @param GameRepositoryInterface $gameRepository
     * @param GameService $gameService
     */
    public function __construct(
        GameRepositoryInterface $gameRepository,
        GameService $gameService
    ) {
        $this->gameRepository = $gameRepository;
        $this->gameService = $gameService;
    }

    /**
     * Show the game details.
     *
     * @param int $gameId
     * @return \Illuminate\View\View
     */
    public function detail($gameId)
    {
        // Get the game with eager loaded relationships
        $game = $this->gameRepository->getGameDetail($gameId);

        // Get recent reviews
        $startOfMonth = now()->startOfMonth();
        $recentReviews = $this->getRecentReviews($game, $startOfMonth);
        $recentReviewsCount = $recentReviews->count();

        // Get all reviews
        $allReviews = $this->getAllReviews($game);
        $allReviewsCount = $allReviews->count();

        // Calculate review statuses
        $recentReviewStatus = $this->gameService->calculateReviewStatus($recentReviews);
        $allReviewStatus = $this->gameService->calculateReviewStatus($allReviews);

        // Get user-specific data
        $userReview = $this->getUserReview($game);
        $isGameInWishlist = $this->isGameInWishlist($game);
        $isGameOwned = $this->isGameOwned($game);
        $isGameInCart = $this->isGameInCart($game);
        $gameCart = $this->getGameCart($game);

        return view('games.detail', [
            'game' => $game,
            'userReview' => $userReview,
            'recentReviewsCount' => $recentReviewsCount,
            'reviewStatus' => $recentReviewStatus,
            'allReviewsCount' => $allReviewsCount,
            'allReviewStatus' => $allReviewStatus,
            'isGameInWishlist' => $isGameInWishlist,
            'isGameOwned' => $isGameOwned,
            'isGameInCart' => $isGameInCart,
            'gameCart' => $gameCart,
        ]);
    }

    /**
     * Search for games.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $genre = $request->input('genre');

        $games = $this->gameRepository->searchGames($query, $genre);

        return view('games.search', [
            'games' => $games,
            'query' => $query,
            'selectedGenre' => $genre,
            'genres' => Genre::all(),
        ]);
    }

    /**
     * List games by genre.
     *
     * @param int $genreId
     * @return \Illuminate\View\View
     */
    public function listByGenre($genreId)
    {
        $genre = Genre::findOrFail($genreId);
        $games = $this->gameRepository->getGamesByGenre($genreId);

        return view('games.by-genre', [
            'genre' => $genre,
            'games' => $games,
        ]);
    }


    /**
     * Get recent reviews with caching.
     *
     * @param Game $game
     * @param \Carbon\Carbon $startOfMonth
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentReviews(Game $game, $startOfMonth)
    {
        $cacheKey = 'recent_reviews_' . $game->id . '_' . $startOfMonth->format('Y-m');

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($game, $startOfMonth) {
            return $game->gameReviews()
                ->with('ratingType')
                ->where('created_at', '>=', $startOfMonth)
                ->get();
        });
    }

    /**
     * Get all reviews with caching.
     *
     * @param Game $game
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAllReviews(Game $game)
    {
        $cacheKey = 'all_reviews_' . $game->id;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($game) {
            return $game->gameReviews()
                ->with('ratingType')
                ->get();
        });
    }

    /**
     * Get the user's review for a game.
     *
     * @param Game $game
     * @return \App\Models\GameReview|null
     */
    private function getUserReview(Game $game)
    {
        if (!Auth::check()) {
            return null;
        }

        return $game->gameReviews()->where('user_id', Auth::id())->first();
    }

    /**
     * Check if the game is in the user's wishlist.
     *
     * @param Game $game
     * @return bool
     */
    private function isGameInWishlist(Game $game)
    {
        return Auth::check() && $game->gameWishlists()->where('user_id', Auth::id())->exists();
    }

    /**
     * Check if the user owns the game.
     *
     * @param Game $game
     * @return bool
     */
    private function isGameOwned(Game $game)
    {
        return Auth::check() && $game->gameLibraries()->where('user_id', Auth::id())->exists();
    }

    /**
     * Check if the game is in the user's cart.
     *
     * @param Game $game
     * @return bool
     */
    private function isGameInCart(Game $game)
    {
        return Auth::check() && $game->gameCarts()->where('user_id', Auth::id())->exists();
    }

    /**
     * Get the game cart.
     *
     * @param Game $game
     * @return \App\Models\GameCart|null
     */
    private function getGameCart(Game $game)
    {
        return Auth::check() ? $game->gameCarts()->where('user_id', Auth::id())->first() : null;
    }
}
