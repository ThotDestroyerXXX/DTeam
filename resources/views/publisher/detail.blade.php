@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - Publishers
@endsection

@section('content')
    <div class="flex flex-col gap-4 w-full text-sm">
        <div class="flex flex-row gap-4">
            <img src="{{ $publisher->image_url }}" alt="{{ $publisher->name }}" class="avatar size-20 rounded object-cover" />
            <div class="flex flex-col justify-around">
                <h1 class="font-bold text-3xl">{{ $publisher->name }}</h1>
                <a href="{{ $publisher->website }}" class="link link-primary">{{ $publisher->website }}</a>
            </div>
        </div>

        {{-- list of games --}}
        <div class="flex flex-col gap-4">
            @if ($games->isEmpty())
                <p class="text-center text-gray-500">No games available from this publisher.</p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach ($games as $game)
                        <a href="{{ route('games.detail', $game) }}"
                            class="flex flex-row items-center bg-base-100 rounded overflow-hidden shadow-md">
                            <img src="{{ $game->gameImages()->first()->image_url }}" alt="{{ $game->title }}"
                                class="h-20 w-auto aspect-video object-cover" />
                            <div class="flex flex-row justify-between w-full p-4">
                                <h3 class="font-semibold">{{ $game->title }}</h3>
                                <p class="text-gray-600">{{ $game->release_date }}</p>
                                <div class="flex flex-row items-enter gap-2">
                                    @if ($game->discount_percentage > 0)
                                        <p class="btn btn-success btn-xs">-{{ $game->discount_percentage }}%</p>
                                    @endif
                                    <p class="text-gray-600">
                                        ${{ $game->price - ($game->discount_percentage * $game->price) / 100 }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
