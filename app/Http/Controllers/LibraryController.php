<?php

namespace App\Http\Controllers;

use App\Models\GameLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $libraryGames = GameLibrary::where('user_id', $user->id)->with('game')->get();
        $gameReviews = $user->gameReviewsWithGame()->get();

        return view('user.library.index', [
            'libraryGames' => $libraryGames,
            'gameReviews' => $gameReviews,
            'libraryGamesCount' => $libraryGames->count(),
            'gameReviewsCount' => $gameReviews->count(),
            'username' => $user->nickname,
            'userAvatar' => $user->profile_picture_url,
        ]);
    }
}
