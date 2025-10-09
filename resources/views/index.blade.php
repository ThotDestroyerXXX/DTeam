@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }}
@endsection

@section('content')
    <div class="flex flex-col gap-2 w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <h1 class="font-bold text-xl uppercase">Featured and Recommended</h1>
        <div class="carousel carousel-center w-full aspect-[18/8] rounded-lg p-4 gap-4 bg-primary">
            @foreach ($recommendedGames as $index => $game)
                <x-carousel-item :index="$index" :thumbnail="$game->gameImages->first()->image_url" :images="$game->gameImages->pluck('image_url')->toArray()" :title="$game->title" :description="$game->brief_description"
                    :price="$game->price" :discount="$game->discount_percentage" :genres="$game->genres->pluck('name')->toArray()" />
            @endforeach
        </div>
    </div>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
@endsection
