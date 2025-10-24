@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - Wishlist
@endsection

@section('content')
    <div class="flex flex-col gap-4 w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <h1 class="font-bold text-xl uppercase">My Wishlist</h1>
        @if ($wishlistGames->isEmpty())
            <p class="text-center text-gray-500">Your wishlist is empty.</p>
        @else
            <div class="flex flex-col gap-2 text-sm">
                @foreach ($wishlistGames as $game)
                    <div class="card bg-base-100 shadow-md p-4 gap-4 rounded flex-row">
                        @if ($game->isInUserLibrary(Auth::id()))
                            <div
                                class="absolute -left-4 top-10 bg-success badge badge-sm rounded border-none gap-1 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>In Library
                            </div>
                        @endif
                        <a href="{{ route('games.detail', $game) }}">
                            <img src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }}"
                                class="w-full h-36 object-cover rounded" />
                        </a>
                        <div class="card-body p-0">
                            <a href="{{ route('games.detail', $game) }}" class="card-title">{{ $game->title }}</a>
                            <div class='flex flex-row justify-between gap-4'>
                                <div class='flex flex-col text-sm text-gray-600'>
                                    <p>Publisher: <a href="{{ route('publisher.detail', $game->publisher) }}"
                                            class="text-blue-500">{{ $game->publisher->name }}</a>
                                    </p>
                                    <p>Release Date: {{ $game->release_date }}</p>
                                </div>
                                <div class='flex gap-2 bg-primary text-primary-content p-1 items-center rounded'>
                                    @if ($game->discount_percentage > 0)
                                        <span
                                            class="badge badge-success rounded-sm badge-sm">{{ $game->discount_percentage }}%</span>
                                        <div class="flex flex-col">
                                            <span class="text-xs line-through">${{ number_format($game->price, 2) }}</span>
                                            <span
                                                class="text-sm font-bold">${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm font-bold">${{ number_format($game->price, 2) }}</span>
                                    @endif
                                    <x-add-to-cart-button :game="$game" />
                                </div>
                            </div>
                            <div class='flex flex-row justify-between text-gray-600'>
                                <div class='flex flex-col gap-2'>
                                    <h2>Genres:</h2>
                                    <div class='flex flex-wrap gap-2'>
                                        @foreach ($game->genres as $genre)
                                            <span class="badge badge-sm badge-primary">{{ $genre->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex gap-4 items-center text-xs">
                                    {{-- when the user created the wishlist --}}
                                    <p>Added on {{ $game->pivot->created_at->format('M d, Y') }}</p>
                                    <form action="{{ route('user.wishlist.remove', $game) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm p-0 btn-link">( Remove )</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
