<?php

namespace App\View\Components;

use App\Enums\GameGiftStatus;
use App\Models\FriendRequest;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // Initialize data with default values
        $data = [
            'friendRequestCount' => 0,
            'gameGiftCount' => 0
        ];

        // Only try to count if user is authenticated
        if (Auth::check() && Auth::user()) {
            $data['friendRequestCount'] = FriendRequest::where('receiver_id', Auth::user()->id)->count();

            // Make sure gameGiftsReceived method exists
            if (method_exists(Auth::user(), 'gameGiftsReceived')) {
                $data['gameGiftCount'] = Auth::user()->gameGiftsReceived()->where('status', GameGiftStatus::PENDING->value)->count();
            } elseif (method_exists(Auth::user(), 'gameGifts')) {
                // Try the gameGifts method instead
                $data['gameGiftCount'] = Auth::user()->gameGifts()->where('status', GameGiftStatus::PENDING->value)->count();
            }
        }


        return view('components.header', [
            'friendRequestCount' => $data['friendRequestCount'],
            'gameGiftCount' => $data['gameGiftCount'],
        ]);
    }
}
