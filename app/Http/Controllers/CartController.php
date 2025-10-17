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
        $game = Game::findOrFail($gameId);
        GameCart::create([
            'user_id' => $user->id,
            'game_id' => $gameId,
        ]);

        return redirect()->back()->with('success_add_to_cart', $game);
    }

    public function remove(Request $request, $gameId)
    {
        $user = $request->user();
        $gameCart = GameCart::where('user_id', $user->id)
            ->where('game_id', $gameId)
            ->firstOrFail();

        $gameCart->delete();

        return redirect()->back()->with('success_remove_from_cart', 'Game removed from cart.');
    }
}
