<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load original game data from JSON
        $originalGames = json_decode(File::get('database/data/games_data.json'), true);

        // Get publisher IDs from the database
        $publisherIds = DB::table('publishers')->pluck('id')->toArray();
        if (empty($publisherIds)) {
            echo "No publishers found in database. Please run the PublisherSeeder first.\n";
            return;
        }

        // Get age rating IDs from the database
        $ageRatingIds = DB::table('age_ratings')->pluck('id')->toArray();
        if (empty($ageRatingIds)) {
            echo "No age ratings found in database. Please run the AgeRatingSeeder first.\n";
            return;
        }

        $games = [];

        // Process each game from the JSON data
        //also seed the game images from the "Screenshots" field in the json
        // also seed gameGenre randomly from the genres in the database
        $gameImages = [];
        $gameGenres = [];

        foreach ($originalGames as $originalGame) {
            // Map JSON fields to database fields and add random IDs
            $game = [
                'id' => Str::ulid()->toString(),
                'title' => $originalGame['Name'],
                'trailer_url' => $originalGame["Movies"] ?? null,
                'brief_description' => substr($originalGame['About the game'] ?? 'No description available', 0, 200) . '...',
                'full_description' => $originalGame['About the game'] ?? 'No description available',
                'release_date' => date('Y-m-d', strtotime($originalGame['Release date'] ?? '2023-01-01')),
                'price' => (int) (($originalGame['Price'] ?? 0) * 100), // Convert to cents/smallest currency unit
                'discount_percentage' => 0, // Default value
                'age_rating_id' => $ageRatingIds[array_rand($ageRatingIds)], // Random age rating
                'publisher_id' => $publisherIds[array_rand($publisherIds)], // Random publisher
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $games[] = $game;

            // Process game images from the "Screenshots" field
            if (isset($originalGame['Screenshots'])) {
                // Split the comma-separated string into an array of URLs
                $screenshotUrls = explode(',', $originalGame['Screenshots']);
                foreach ($screenshotUrls as $screenshotUrl) {
                    // Trim to remove any whitespace
                    $trimmedUrl = trim($screenshotUrl);
                    if (!empty($trimmedUrl)) {
                        $gameImages[] = [
                            'id' => Str::ulid()->toString(),
                            'game_id' => $game['id'],
                            'image_url' => $trimmedUrl,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Pick randomly 1 to 3 genres from ALL genres in the database (not based on JSON)
            // Get all genre IDs from the database
            $allGenreIds = DB::table('genres')->pluck('id')->toArray();

            if (!empty($allGenreIds)) {
                // Randomly select 1-3 genres
                $numGenres = rand(1, min(3, count($allGenreIds)));
                // Shuffle and slice is a simpler way to get random elements
                shuffle($allGenreIds);
                $selectedGenreIds = array_slice($allGenreIds, 0, $numGenres);

                // Insert game-genre relationships
                foreach ($selectedGenreIds as $genreId) {
                    $gameGenres[] = [
                        'id' => Str::ulid()->toString(),
                        'game_id' => $game['id'],
                        'genre_id' => $genreId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert the processed games into the database
        DB::table('games')->insert($games);
        DB::table('game_images')->insert($gameImages);
        DB::table('game_genres')->insert($gameGenres);
    }
}
