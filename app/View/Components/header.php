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
        if (!Auth::check() || !Auth::user()) {
            return view('components.header', [
                'friendRequestCount' => 0,
                'gameGiftCount' => 0,
            ]);
        }
        $data['friendRequestCount'] = FriendRequest::where('receiver_id', Auth::user()->id)->count();
        $data['gameGiftCount'] = Auth::user()->gameGiftsReceived()->where('status', GameGiftStatus::PENDING)->count();


        return view('components.header', [
            'friendRequestCount' => $data['friendRequestCount'],
            'gameGiftCount' => $data['gameGiftCount'],
        ]);
    }
}
