<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Load cart items with their games in a single query using the GameCart model
        $cartItems = GameCart::where('user_id', Auth::id())->with('game')->get();

        // Calculate total using the already loaded cart items to avoid another query
        $cartTotal = $cartItems->sum(function ($item) {
            // Apply any discount if applicable and return directly
            return $item->game->discount_percentage > 0
                ? $item->game->price * (1 - $item->game->discount_percentage / 100)
                : $item->game->price;
        });

        return view('user.cart.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
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

    public function removeAll(Request $request)
    {
        $user = $request->user();
        GameCart::where('user_id', $user->id)->delete();

        return redirect()->back()->with('success', 'All games removed from cart.');
    }
}
