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
        if (Auth::check() && Auth::user()->role === Role::ADMIN) {
            return redirect()->route('admin.publishers.index');
        }
        if (Auth::check() && Auth::user()->role === Role::PUBLISHER) {
            return redirect()->route('publisher.games.index');
        }
        return view('index', [
            'recommendedGames' => Game::inRandomOrder()->take(10)->get(),
        ]);
    }
}
