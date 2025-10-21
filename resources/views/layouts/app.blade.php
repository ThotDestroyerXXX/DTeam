<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/cally"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title')</title>
</head>

<body class="flex min-h-screen flex-col bg-base-300">
    <div class="p-4 flex items-center bg-base-100 shadow">
        @include('components.header')
    </div>
    <div class="flex p-4 flex-col max-w-5xl w-full mx-auto">

        <x-inner-header />
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-4">
                Profile updated successfully.
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning mb-4">
                {{ session('warning') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success_add_to_cart'))
            @php
                $gameCart = session('success_add_to_cart');
                $isGameOwned =
                    Auth::check() && Auth::user()->gameLibraries()->where('game_id', $gameCart->game_id)->exists();
            @endphp
            <dialog id="my_modal_1" class="modal" open>
                <div class="modal-box flex flex-col gap-2 ">
                    <h3 class="text-lg font-bold">Added to your cart!</h3>
                    <div class="bg-base-100 gap-2 w-full flex flex-row items-center">
                        <img src="{{ $gameCart->game->gameImages->first()->image_url }}"
                            alt="{{ $gameCart->game->title }}" class="h-24 w-auto rounded" />
                        <div class="flex flex-col justify-between w-full">
                            <p class="font-medium">{{ $gameCart->game->title }}</p>
                            <div class="flex flex-row justify-between w-full">
                                <form id="giftStatusForm" action="{{ route('user.cart.toggle-gift', $gameCart->id) }}"
                                    method="POST" class="m-0 self-end">
                                    @csrf
                                    @method('PATCH')
                                    <select name="is_gift" class="select select-sm w-auto min-w-[150px] self-end"
                                        onchange="document.getElementById('giftStatusForm').submit()">
                                        @if (!$isGameOwned)
                                            <option value="0" {{ !$gameCart->is_gift ? 'selected' : '' }}>For my
                                                account
                                            </option>
                                        @endif
                                        <option value="1" {{ $gameCart->is_gift ? 'selected' : '' }}>This is a gift
                                        </option>
                                    </select>
                                </form>
                                <div class="flex flex-col self-end">
                                    @if ($gameCart->game->discount_percentage > 0)
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="badge badge-success rounded-sm font-semibold h-full px-1">{{ $gameCart->game->discount_percentage }}%</span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs line-through">${{ number_format($gameCart->game->price, 2) }}</span>
                                                <span
                                                    class="text-sm font-bold">${{ number_format($gameCart->game->price * (1 - $gameCart->game->discount_percentage / 100), 2) }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span
                                            class="text-sm font-bold">${{ number_format($gameCart->game->price, 2) }}</span>
                                    @endif
                                    <form action="{{ route('user.cart.remove', $gameCart->game) }}" method="POST"
                                        class="self-end">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link btn-sm p-0">remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-action w-full ">
                        <form method="dialog" class="gap-2 w-full grid grid-cols-2 grid-rows-1">
                            @csrf
                            <!-- if there is a button in form, it will close the modal -->
                            <button class="btn btn-secondary">Continue Shopping</button>
                            <a href="{{ route('user.cart.index') }}" class="btn btn-primary">View My
                                Cart</a>
                        </form>
                    </div>
                </div>
            </dialog>
        @endif
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
