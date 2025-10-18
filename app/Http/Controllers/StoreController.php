<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    public function index()
    {
        // Get the top 10 most popular games based on game_libraries count - cache for 1 hour
        $popularGames = Cache::remember('popular_games', 3600, function () {
            return Game::withCount('gameLibraries')
                ->orderByDesc('game_libraries_count')
                ->take(10)
                ->get();
        });

        // Get featured discounted games - cache for 1 hour
        $featuredDiscounts = Cache::remember('featured_discounts', 3600, function () {
            return Game::whereNotNull('discount_percentage')
                ->orderByDesc('discount_percentage')
                ->take(10)
                ->get();
        });

        return view('index', [
            'recommendedGames' => $popularGames,
            'featuredDiscounts' => $featuredDiscounts,
        ]);
    }
}
