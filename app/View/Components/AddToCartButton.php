<?php

namespace App\View\Components;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class AddToCartButton extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Game $game
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.add-to-cart-button', [
            'isInCart' => $this->game->isInUserCart(Auth::id()),
        ]);
    }
}
