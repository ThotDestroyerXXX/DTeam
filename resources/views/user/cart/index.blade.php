@extends('layouts.app')

@section('title')
    Shopping Cart
@endsection

@section('content')
    <div class="container gap-6 flex flex-col">
        <h1 class="font-semibold text-3xl">Your Shopping Cart</h1>
        @if ($cartItems->isEmpty())
            <p>Your cart is empty.</p>
        @else
            <div class="flex flex-col gap-4">

                <div class="flex flex-row gap-4">
                    <div class="flex flex-col gap-4 flex-1 max-w-2/3">
                        @foreach ($cartItems as $cart)
                            <div class="bg-base-100 p-3 rounded-box flex flex-row gap-4">
                                <img src="{{ $cart->game->gameImages->first()->image_url }}" alt="{{ $cart->game->title }}"
                                    class="h-24 aspect-video rounded" />
                                <div class="flex flex-col justify-between w-full">
                                    <p class="font-medium ">{{ $cart->game->title }}</p>
                                    <div class="flex flex-row justify-between w-full">
                                        <form id="giftStatusForm_{{ $cart->id }}"
                                            action="{{ route('user.cart.toggle-gift', $cart->id) }}" method="POST"
                                            class="m-0 self-end">
                                            @csrf
                                            @method('PATCH')
                                            <select name="is_gift" class="select select-sm w-auto min-w-[150px] self-end"
                                                onchange="document.getElementById('giftStatusForm_{{ $cart->id }}').submit()">
                                                @if (!(Auth::check() && Auth::user()->gameLibraries()->where('game_id', $cart->game->id)->exists()))
                                                    <option value="0" {{ !$cart->is_gift ? 'selected' : '' }}>For
                                                        my
                                                        account
                                                    </option>
                                                @endif
                                                <option value="1" {{ $cart->is_gift ? 'selected' : '' }}>This is
                                                    a
                                                    gift
                                                </option>
                                            </select>
                                        </form>
                                        <div class="flex flex-col self-end">
                                            @if ($cart->game->discount_percentage > 0)
                                                <span
                                                    class="text-sm font-bold text-end">${{ number_format($cart->game->price * (1 - $cart->game->discount_percentage / 100), 2) }}</span>
                                            @else
                                                <span
                                                    class="text-sm font-bold">${{ number_format($cart->game->price, 2) }}</span>
                                            @endif
                                            {{-- remove --}}
                                            <form action="{{ route('user.cart.remove', $cart->game) }}" method="POST"
                                                class="self-end">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm p-0">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex flex-row justify-between">
                            <a href="{{ route('store.index') }}" class="btn btn-primary">Continue Shopping</a>
                            <form action="{{ route('user.cart.remove-all') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm">Remove All Items</button>
                            </form>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 w-1/3">
                        <div class="bg-base-100 rounded p-4 flex flex-col gap-4 self-start">
                            <div class="flex flex-row justify-between">
                                <h2 class="font-semibold text-xl">Estimated Total</h2>
                                <span class="font-bold">${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <p class="text-sm">Sales tax will be calculated during checkout when applicable</p>
                            <a class="btn btn-primary" href="{{ route('user.checkout.index') }}">Continue</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
