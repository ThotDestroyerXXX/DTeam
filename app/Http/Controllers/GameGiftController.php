<?php

namespace App\Http\Controllers;

use App\Enums\GameGiftStatus;
use App\Models\GameGift;
use App\Models\GameLibrary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameGiftController extends Controller
{
    public function index()
    {
        $gameGifts = GameGift::where('receiver_id', Auth::id())
            ->where('status', GameGiftStatus::PENDING)
            ->with(['game', 'sender'])
            ->groupBy('sender_id', 'game_id', 'status', 'id')
            ->get();

        return view('user.game-gift.index', [
            'gameGifts' => $gameGifts
        ]);
    }

    public function store(User $sender)
    {
        // accept all pending gifts for the authenticated user from the sender

        $userId = Auth::id();

        $pendingGifts = GameGift::where('receiver_id', $userId)
            ->where('sender_id', $sender->id)
            ->where('status', GameGiftStatus::PENDING)
            ->get();

        foreach ($pendingGifts as $gift) {
            // Add the game to the user's library
            GameLibrary::create([
                'user_id' => $userId,
                'game_id' => $gift->game_id,
                'discount_percentage' => $gift->discount_percentage,
            ]);

            // Update gift status to ACCEPTED
            $gift->status = GameGiftStatus::ACCEPTED;
            $gift->save();
        }

        return redirect()->route('user.library.index')->with('success', 'All pending game gifts accepted and added to your library.');
    }
}
