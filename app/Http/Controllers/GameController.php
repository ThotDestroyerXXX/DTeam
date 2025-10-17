<?php

namespace App\Http\Controllers;

use App\Traits\ImageKitUtility;
use App\Http\Requests\GameStoreRequest;
use App\Http\Requests\GameUpdateRequest;
use App\Models\AgeRating;
use App\Models\Game;
use App\Models\GameGenre;
use App\Models\Genre;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class GameController extends Controller
{

    use ImageKitUtility;

    public function index()
    {
        return view('publisher.games.index', [
            'publisher' => Auth::user()->publisher,
        ]);
    }

    public function add()
    {
        return view('publisher.games.add', [
            'ratingTypes' => AgeRating::all(),
            'genres' => Genre::all()
        ]);
    }

    public function store(GameStoreRequest $request)
    {
        $validated = $request->validated();
        $publisher = Auth::user()->publisher;

        $game = $publisher->games()->create([
            'title' => $validated['title'],
            'brief_description' => $validated['brief_description'],
            'full_description' => $validated['full_description'],
            'price' => $validated['price'],
            'release_date' => $validated['release_date'],
            'discount_percentage' => $validated['discount'] ?? 0,
            'age_rating_id' => $validated['age_rating_id'],
        ]);

        foreach ($validated['images'] as $image) {
            $response = $this->uploadToImageKit($image, $image->getClientOriginalName() . '-' . time(), 'DTeam/games', null, null, false);

            // Check if upload was successful
            if ($response && $response->error === null && isset($response->result)) {
                // Access the URL from the result object
                $game->gameImages()->create([
                    'image_url' => $response->result->url,
                    'image_file_id' => $response->result->fileId,
                ]);
            } else {
                // Log error or handle failed upload
                \Illuminate\Support\Facades\Log::error('Image upload failed: ' . json_encode($response));
                return redirect()->back()->with('error', 'Failed to upload one or more images. Please try again.');
            }
        }

        // Attach selected genres to the game
        if (isset($validated['genres'])) {
            foreach ($validated['genres'] as $genreId) {
                GameGenre::create([
                    'game_id' => $game->id,
                    'genre_id' => $genreId,
                ]);
            }
        }

        return redirect()->route('publisher.games.index')->with('success', 'Game added successfully.');
    }

    public function edit($gameId)
    {
        $publisher = Auth::user()->publisher;
        $game = $publisher->games()->with(['genres', 'gameImages'])->findOrFail($gameId);

        return view('publisher.games.edit', [
            'game' => $game,
            'ratingTypes' => AgeRating::all(),
            'genres' => Genre::all(),
        ]);
    }

    public function update(GameUpdateRequest $request, $gameId)
    {
        // Get validated data
        $validated = $request->validated();

        $publisher = Auth::user()->publisher;
        $game = $publisher->games()->findOrFail($gameId);

        // Update game details
        $game->update([
            'title' => $validated['title'],
            'brief_description' => $validated['brief_description'],
            'full_description' => $validated['full_description'],
            'price' => $validated['price'],
            'release_date' => $validated['release_date'],
            'discount_percentage' => $validated['discount'],
            'age_rating_id' => $validated['age_rating_id'],
        ]);

        // Handle image deletions
        if (isset($validated['delete_images'])) {
            foreach ($validated['delete_images'] as $imageId) {
                $image = $game->gameImages()->find($imageId);

                if ($image) {
                    // Delete from ImageKit if file_id exists
                    if ($image->image_file_id) {
                        $this->deleteImage($image->image_file_id);
                    }

                    // Delete from database
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if (isset($validated['images'])) {
            foreach ($validated['images'] as $image) {
                $response = $this->uploadToImageKit($image, $image->getClientOriginalName() . '-' . time(), 'DTeam/games', null, null, false);

                // Check if upload was successful
                if ($response && $response->error === null && isset($response->result)) {
                    // Access the URL from the result object
                    $game->gameImages()->create([
                        'image_url' => $response->result->url,
                        'image_file_id' => $response->result->fileId,
                    ]);
                } else {
                    // Log error or handle failed upload
                    \Illuminate\Support\Facades\Log::error('Image upload failed: ' . json_encode($response));
                    return redirect()->back()->with('error', 'Failed to upload one or more images. Please try again.');
                }
            }
        }

        // Update genres (remove all and re-add)
        $game->genres()->detach();

        if (isset($validated['genres'])) {
            foreach ($validated['genres'] as $genreId) {
                GameGenre::create([
                    'game_id' => $game->id,
                    'genre_id' => $genreId,
                ]);
            }
        }

        return redirect()->route('publisher.games.index')->with('success', 'Game updated successfully.');
    }

    public function detail($gameId)
    {
        $game = Game::with(['publisher', 'ageRating', 'genres', 'gameImages'])->findOrFail($gameId);

        // Get recent reviews (from the current month)
        $startOfMonth = now()->startOfMonth();
        $recentReviews = $game->gameReviews()->with('ratingType')->where('created_at', '>=', $startOfMonth)->get();
        $recentReviewsCount = $recentReviews->count();

        // Get all reviews
        $allReviews = $game->gameReviews()->with('ratingType')->get();
        $allReviewsCount = $allReviews->count();

        // Calculate review statuses
        $recentReviewStatus = $this->calculateReviewStatus($recentReviews);
        $allReviewStatus = $this->calculateReviewStatus($allReviews);

        // Get the review by the authenticated user, if any
        $userReview = null;
        if (Auth::check()) {
            $userId = Auth::id();
            $userReview = $game->gameReviews()->where('user_id', $userId)->first();
        }

        return view('games.detail', [
            'game' => $game,
            'userReview' => $userReview,
            'recentReviewsCount' => $recentReviewsCount,
            'reviewStatus' => $recentReviewStatus,
            'allReviewsCount' => $allReviewsCount,
            'allReviewStatus' => $allReviewStatus,
        ]);
    }

    /**
     * Calculate review status based on the percentage of positive reviews
     *
     * @param \Illuminate\Database\Eloquent\Collection $reviews
     * @return string
     */
    private function calculateReviewStatus($reviews)
    {
        $reviewCount = $reviews->count();

        // Default to "no reviews" if there are no reviews
        if ($reviewCount == 0) {
            return 'No Reviews';
        }

        // Count positive reviews (where rating_type title is "Recommended")
        $positiveReviews = $reviews->filter(function ($review) {
            return $review->ratingType->title === 'Recommended';
        })->count();

        // Calculate percentage of positive reviews
        $positivePercentage = ($positiveReviews / $reviewCount) * 100;

        // Determine review status based on percentage
        if ($positivePercentage > 70) {
            return 'Positive';
        } elseif ($positivePercentage >= 40) {
            return 'Mixed';
        } else {
            return 'Negative';
        }
    }
}
