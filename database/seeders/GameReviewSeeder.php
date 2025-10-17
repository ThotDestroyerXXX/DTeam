<?php

namespace Database\Seeders;

use App\Models\GameLibrary;
use App\Models\GameReview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get the user id and game id in the game library table
        $userGameId = GameLibrary::select('user_id', 'game_id')->get();

        foreach ($userGameId as $item) {
            GameReview::factory()->create([
                'user_id' => $item->user_id,
                'game_id' => $item->game_id,
                'rating_type_id' => fake()->numberBetween(1, 2),
                'content' => fake()->paragraph(rand(1, 3)),
            ]);
        }
    }
}
