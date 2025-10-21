@props(['cart'])

<div class="flex flex-col gap-4 bg-base-100 p-3 rounded-box">
    <div class="flex flex-row gap-4">
        <img src="{{ $cart->game->gameImages->first()->image_url }}" alt="{{ $cart->game->title }}"
            class="h-24 aspect-video rounded" />
        <div class="flex flex-col justify-between w-full">
            <p class="font-medium ">{{ $cart->game->title }}</p>
            <div class="flex flex-row justify-between w-full">
                <select name="is_gift" class="select select-sm w-auto min-w-[150px] self-end" disabled>
                    @if (!(Auth::check() && Auth::user()->gameLibraries()->where('game_id', $cart->game->id)->exists()) && !$cart->is_gift)
                        <option value="0" {{ !$cart->is_gift ? 'selected' : '' }}>For my account</option>
                    @else
                        <option value="1" {{ $cart->is_gift ? 'selected' : '' }}>This is a gift</option>
                    @endif
                </select>
                <div class="flex flex-col self-end">
                    @if ($cart->game->discount_percentage > 0)
                        <span
                            class="text-sm font-bold text-end">${{ number_format($cart->game->price * (1 - $cart->game->discount_percentage / 100), 2) }}</span>
                    @else
                        <span class="text-sm font-bold">${{ number_format($cart->game->price, 2) }}</span>
                    @endif
                    {{-- remove --}}
                    <form action="{{ route('user.cart.remove', $cart->game) }}" method="POST" class="self-end">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm p-0">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if ($cart->is_gift)
        <x-checkout.gift-recipient-section :cart="$cart" />
    @endif
</div>
