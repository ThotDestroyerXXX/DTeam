<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameLibrary;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $allGames = Game::all();

        foreach ($users as $user) {
            // Each user has between 0 to 5 games in their library
            $gameCount = min(rand(0, 5), $allGames->count());

            // Get random games for this user, ensuring no duplicates
            $randomGames = $allGames->random($gameCount);

            foreach ($randomGames as $game) {
                GameLibrary::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'discount_percentage' => fake()->numberBetween(0, 70)
                ]);
            }
        }
    }
}
