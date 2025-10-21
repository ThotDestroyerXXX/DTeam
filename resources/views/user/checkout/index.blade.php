@extends('layouts.app')

@section('title')
    Checkout
@endsection

@section('content')
    <div class="container gap-6 flex flex-col">
        <h1 class="font-semibold text-3xl">Checkout</h1>
        @if ($cartItems->isEmpty())
            <p>No item to checkout.</p>
        @else
            <div class="flex flex-col gap-4">
                {{-- Store current user data for JS --}}
                <input type="hidden" id="current-user-name" value="{{ Auth::user()->nickname }}">
                <input type="hidden" id="current-user-picture"
                    value="{{ Auth::user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->nickname) }}">

                <div class="flex flex-row gap-4">
                    {{-- Cart Items Section --}}
                    <div class="flex flex-col gap-4 flex-1 max-w-2/3">
                        @foreach ($cartItems as $cart)
                            <x-checkout.cart-item :cart="$cart" />
                        @endforeach
                    </div>

                    {{-- Purchase Summary Section --}}
                    <x-checkout.purchase-summary :cartTotal="$cartTotal" />
                </div>
            </div>
        @endif
    </div>

    {{-- Load checkout JS only when needed --}}
    @if (!$cartItems->isEmpty())
        <script src="{{ asset('js/checkout/gift-checkout.js') }}"></script>
    @endif
@endsection
