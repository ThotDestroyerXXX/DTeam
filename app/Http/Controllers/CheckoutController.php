<?php

namespace App\Http\Controllers;

use App\Enums\GameGiftStatus;
use App\Models\GameCart;
use App\Models\GameGift;
use App\Models\GameLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = GameCart::where('user_id', Auth::id())->with('game')->get();

        // Calculate total using the already loaded cart items to avoid another query
        $cartTotal = $cartItems->sum(function ($item) {
            // Apply any discount if applicable and return directly
            return $item->game->discount_percentage > 0
                ? $item->game->price * (1 - $item->game->discount_percentage / 100)
                : $item->game->price;
        });

        return view('user.checkout.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    public function process(Request $request)
    {
        // Checkout logic to be implemented
        $user = Auth::user();

        // ensure wallet has sufficient balance
        $cartItems = GameCart::where('user_id', $user->id)->with('game')->get();
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->game->discount_percentage > 0
                ? $item->game->price * (1 - $item->game->discount_percentage / 100)
                : $item->game->price;
        });
        if ($user->wallet < $cartTotal) {
            return redirect()->back()->with('error', 'Insufficient wallet balance. Please top up your wallet.');
        }

        // Parse gift details from the form submission
        $giftDetails = [];
        if ($request->has('gift_details') && !empty($request->gift_details)) {
            $giftDetails = json_decode($request->gift_details, true);
        }

        // Map the gift details by cart ID for easy lookup
        $giftDetailsByCartId = [];
        foreach ($giftDetails as $detail) {
            $giftDetailsByCartId[$detail['cart_id']] = [
                'recipient_id' => $detail['recipient_id'],
                'message' => $detail['message']
            ];
        }

        // add each cart item to user library if is_gift is false, else send gift
        foreach ($cartItems as $item) {
            if ($item->is_gift) {
                // Get gift details for this cart item
                $giftDetail = $giftDetailsByCartId[$item->id] ?? null;

                if ($giftDetail) {
                    // Create a gift record
                    GameGift::create([
                        'game_id' => $item->game_id,
                        'sender_id' => $user->id,
                        'receiver_id' => $giftDetail['recipient_id'],
                        'message' => $giftDetail['message'],
                        'status' => GameGiftStatus::PENDING,
                        'discount_percentage' => $item->game->discount_percentage,
                    ]);
                }
            } else {
                // Logic to add game to user's library
                GameLibrary::create([
                    'user_id' => $user->id,
                    'game_id' => $item->game_id,
                    'discount_percentage' => $item->game->discount_percentage,
                ]);
            }
        }

        // Deduct the total from the user's wallet
        $user->wallet -= $cartTotal;
        $user->point += (int) $cartTotal / 100;
        $user->save();

        // Clear the user's cart
        GameCart::where('user_id', $user->id)->delete();

        return redirect()->route('user.transaction.index')->with('success', 'Purchase completed successfully!');
    }
}
