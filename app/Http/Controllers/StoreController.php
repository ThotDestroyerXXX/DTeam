<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index()
    {
        // Get the top 10 most popular games based on game_libraries count
        $popularGames = Game::withCount('gameLibraries')
            ->orderByDesc('game_libraries_count')
            ->take(10)
            ->get();

        return view('index', [
            'recommendedGames' => $popularGames,
            'featuredDiscounts' => Game::whereNotNull('discount_percentage')->orderByDesc('discount_percentage')->take(10)->get(),
        ]);
    }
}
