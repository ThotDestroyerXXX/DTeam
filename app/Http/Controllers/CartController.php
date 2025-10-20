<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('user.cart.index');
    }

    public function add(Request $request, $gameId)
    {
        $user = $request->user();
        Game::findOrFail($gameId);
        $gameCart = GameCart::create([
            'user_id' => $user->id,
            'game_id' => $gameId,
            'is_gift' => $user->gameLibraries()->where('game_id', $gameId)->exists() ? true : false,
        ]);

        // Load the relationships needed in the view
        $gameCart->load('game.gameImages');

        return redirect()->back()->with('success_add_to_cart', $gameCart);
    }

    public function remove(Request $request, $gameId)
    {
        $user = $request->user();
        $gameCart = GameCart::where('user_id', $user->id)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $gameCart->delete();

        return redirect()->back()->with('success', 'Game removed from cart.');
    }

    // change is_gift status
    public function toggleGift(Request $request, $gameCartId)
    {
        $user = $request->user();

        $gameCart = GameCart::where('user_id', $user->id)
            ->where('id', $gameCartId)
            ->firstOrFail();

        // if user already owns the game, set is_gift to true
        if ($user->gameLibraries()->where('game_id', $gameCart->game_id)->exists()) {
            return redirect()->back()->with('error', 'You already own this game. It is set as a gift.');
        }

        $gameCart->is_gift = (bool)$request->input('is_gift');
        $gameCart->save();

        return redirect()->back()->with('success_add_to_cart', $gameCart);
    }
}
