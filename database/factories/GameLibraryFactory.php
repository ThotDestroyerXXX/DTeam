<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameLibrary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameLibrary>
 */
class GameLibraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Find a unique user_id and game_id combination
        do {
            $userId = User::inRandomOrder()->first()->id;
            $gameId = Game::inRandomOrder()->first()->id;
        } while (GameLibrary::where('user_id', $userId)->where('game_id', $gameId)->exists());

        return [
            'user_id' => $userId,
            'game_id' => $gameId,
            'discount_percentage' => $this->faker->numberBetween(0, 70)
        ];
    }
}
