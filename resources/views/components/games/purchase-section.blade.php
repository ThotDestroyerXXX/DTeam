@props(['game'])

<div class="flex flex-row bg-base-100 p-6 rounded-lg items-center relative">
    <h2 class="font-semibold">Buy {{ $game->title }}</h2>
    <div
        class="absolute right-4 -bottom-4 flex flex-row gap-2 bg-primary text-primary-content p-1 shadow-lg items-center rounded-none">
        <div class="flex items-center gap-2">
            @if ($game->discount_percentage > 0)
                <span
                    class="badge badge-success rounded-sm font-semibold h-full px-1">{{ $game->discount_percentage }}%</span>
                <div>
                    <span class="text-xs line-through">${{ number_format($game->price, 2) }}</span>
                    <span
                        class="text-sm font-bold">${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</span>
                </div>
            @else
                <span class="text-sm font-bold">${{ number_format($game->price, 2) }}</span>
            @endif
        </div>

        @can('is-user')
            <div>|</div>
            <x-add-to-cart-button :game="$game" />
        @endcan
        @guest
            <div>|</div>
            <a href="{{ route('login') }}"> <button class="btn btn-success btn-sm">Add to
                    Cart</button></a>
        @endguest
    </div>
</div>
