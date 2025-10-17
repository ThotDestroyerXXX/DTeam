<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CarouselItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $index,
        public string $thumbnail,
        public array $images,
        public string $title,
        public string $description,
        public int $price,
        public array $genres,
        public string $gameId,
        public int $discount = 0,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.carousel-item');
    }
}
