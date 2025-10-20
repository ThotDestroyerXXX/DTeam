<?php

namespace App\Services;

use App\Exceptions\Game\ImageUploadException;
use App\Models\Game;
use App\Models\GameImage;
use App\Repositories\GameRepositoryInterface;
use App\Traits\ImageKitUtility;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GameService
{
    use ImageKitUtility;

    /**
     * @var GameRepositoryInterface
     */
    protected $gameRepository;

    /**
     * GameService constructor.
     *
     * @param GameRepositoryInterface $gameRepository
     */
    public function __construct(GameRepositoryInterface $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * Store a new game
     *
     * @param array $data
     * @param array $images
     * @return Game
     * @throws Exception
     */
    public function storeGame(array $data, array $images): Game
    {
        // Create the game
        $game = $this->gameRepository->createGame($data['publisher'], [
            'title' => $data['title'],
            'brief_description' => $data['brief_description'],
            'full_description' => $data['full_description'],
            'price' => $data['price'],
            'release_date' => $data['release_date'],
            'discount' => $data['discount'] ?? 0,
            'age_rating_id' => $data['age_rating_id'],
        ]);

        // Upload images
        $this->uploadGameImages($game, $images);

        // Attach genres
        if (isset($data['genres'])) {
            $this->gameRepository->attachGenres($game, $data['genres']);
        }

        return $game;
    }

    /**
     * Update an existing game
     *
     * @param Game $game
     * @param array $data
     * @param array|null $images
     * @param array|null $deleteImages
     * @return bool
     * @throws Exception
     */
    public function updateGame(Game $game, array $data, ?array $images = null, ?array $deleteImages = null): bool
    {
        // Delete images if any
        if ($deleteImages) {
            $this->deleteGameImages($game, $deleteImages);
        }

        // Upload new images if any
        if ($images) {
            $this->uploadGameImages($game, $images);
        }

        // Update genres
        if (isset($data['genres'])) {
            $this->gameRepository->detachGenres($game);
            $this->gameRepository->attachGenres($game, $data['genres']);
        }

        // Update game
        return $this->gameRepository->updateGame($game, [
            'title' => $data['title'],
            'brief_description' => $data['brief_description'],
            'full_description' => $data['full_description'],
            'price' => $data['price'],
            'release_date' => $data['release_date'],
            'discount' => $data['discount'],
            'age_rating_id' => $data['age_rating_id'],
        ]);
    }

    /**
     * Upload game images
     *
     * @param Game $game
     * @param array $images
     * @return void
     * @throws Exception
     */
    protected function uploadGameImages(Game $game, array $images): void
    {
        foreach ($images as $image) {
            $response = $this->uploadToImageKit(
                $image,
                $image->getClientOriginalName() . '-' . time(),
                'DTeam/games',
                null,
                null,
                false
            );

            // Check if upload was successful
            if ($response && $response->error === null && isset($response->result)) {
                // Access the URL from the result object
                $game->gameImages()->create([
                    'image_url' => $response->result->url,
                    'image_file_id' => $response->result->fileId,
                ]);
            } else {
                Log::error('Image upload failed: ' . json_encode($response));
                throw new ImageUploadException('Failed to upload one or more images. Please try again.');
            }
        }
    }

    /**
     * Delete game images
     *
     * @param Game $game
     * @param array $imageIds
     * @return void
     */
    protected function deleteGameImages(Game $game, array $imageIds): void
    {
        foreach ($imageIds as $imageId) {
            $image = $game->gameImages()->find($imageId);

            if ($image) {
                // Delete from ImageKit if file_id exists
                if ($image->image_file_id) {
                    try {
                        $this->deleteImage($image->image_file_id);
                    } catch (Exception $e) {
                        Log::error('Failed to delete image from ImageKit: ' . $e->getMessage());
                    }
                }

                // Delete from database
                $image->delete();
            }
        }
    }

    /**
     * Delete an image from ImageKit by its file ID
     *
     * @param string $fileId
     * @return bool
     * @throws Exception
     */
    public function deleteImageFromStorage(string $fileId): bool
    {
        try {
            $this->deleteImage($fileId);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete image from storage: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate review status based on the percentage of positive reviews
     *
     * @param \Illuminate\Database\Eloquent\Collection $reviews
     * @return string
     */
    public function calculateReviewStatus($reviews)
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
