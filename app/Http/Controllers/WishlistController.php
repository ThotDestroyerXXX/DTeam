<?php

namespace App\Http\Controllers;

use App\Models\GameWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        return view('user.wishlist.index', [
            'wishlistGames' => Auth::user()->gameWishlists()->withPivot('created_at')->get(),
        ]);
    }

    public function add(Request $request, $gameId)
    {
        // Logic to add game to wishlist goes here
        GameWishlist::create([
            'user_id' => $request->user()->id,
            'game_id' => $gameId,
        ]);

        return redirect()->back()->with('success', 'Game added to wishlist.');
    }

    public function remove(Request $request, $gameId)
    {
        $user = $request->user();
        $gameWishlist = GameWishlist::where('user_id', $user->id)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $gameWishlist->delete();

        return redirect()->back()->with('success', 'Game removed from wishlist.');
    }
}
