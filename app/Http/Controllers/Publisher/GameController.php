<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Models\AgeRating;
use App\Models\Game;
use App\Models\Genre;
use App\Repositories\GameRepositoryInterface;
use App\Services\GameService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Display a listing of games for the publisher.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $publisher = Auth::user()->publisher;
        $games = $this->gameRepository->getPublisherGames($publisher);

        return view('publisher.games.index', [
            'publisher' => $publisher,
            'games' => $games,
        ]);
    }

    /**
     * Show the form for creating a new game.
     *
     * @return \Illuminate\View\View
     */
    public function add()
    {
        return view('publisher.games.add', [
            'ratingTypes' => AgeRating::all(),
            'genres' => Genre::all(),
        ]);
    }

    /**
     * Store a newly created game.
     *
     * @param GameRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GameRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['publisher'] = Auth::user()->publisher;

            $this->gameService->storeGame($validated, $validated['images'] ? $validated['images'] : null);

            return redirect()
                ->route('publisher.games.index')
                ->with('success', 'Game added successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create game: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the game. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified game.
     *
     * @param Game $game
     * @return \Illuminate\View\View
     */
    public function edit(Game $game)
    {
        // Verify the game belongs to the current publisher
        $publisher = Auth::user()->publisher;

        if ($game->publisher_id !== $publisher->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('publisher.games.edit', [
            'game' => $game,
            'ratingTypes' => AgeRating::all(),
            'genres' => Genre::all(),
        ]);
    }

    /**
     * Update the specified game.
     *
     * @param GameRequest $request
     * @param Game $game
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GameRequest $request, Game $game)
    {
        try {
            // Verify the game belongs to the current publisher
            $publisher = Auth::user()->publisher;

            if ($game->publisher_id !== $publisher->id) {
                abort(403, 'Unauthorized action.');
            }

            $validated = $request->validated();

            $this->gameService->updateGame(
                $game,
                $validated,
                isset($validated['images']) ? $validated['images'] : null,
                isset($validated['delete_images']) ? $validated['delete_images'] : null
            );

            return redirect()
                ->route('publisher.games.index')
                ->with('success', 'Game updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to update game: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the game. Please try again.');
        }
    }

    /**
     * Remove the specified game from storage.
     *
     * @param Game $game
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Game $game)
    {
        try {
            // Verify the game belongs to the current publisher
            $publisher = Auth::user()->publisher;

            if ($game->publisher_id !== $publisher->id) {
                abort(403, 'Unauthorized action.');
            }

            // Delete game images
            foreach ($game->gameImages as $image) {
                if ($image->image_file_id) {
                    try {
                        $this->gameService->deleteImageFromStorage($image->image_file_id);
                    } catch (Exception $e) {
                        Log::error('Failed to delete image: ' . $e->getMessage());
                    }
                }
                $image->delete();
            }

            // Delete genre relationships
            $this->gameRepository->detachGenres($game);

            // Delete the game
            $game->delete();

            return redirect()
                ->route('publisher.games.index')
                ->with('success', 'Game deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete game: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the game. Please try again.');
        }
    }
}
