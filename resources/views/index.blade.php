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
                    :price="$game->price" :discount="$game->discount_percentage" :genres="$game->genres->pluck('name')->toArray()" :game-id="$game->id" />
            @endforeach
        </div>
    </div>

    <div class="mt-8 mb-4">
        <div class="bg-radial from-orange-100 to-orange-200 rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="font-bold text-2xl text-red-500">Featured Deep Discounts</h2>
                    <p class="text-sm">Especially great deals on some of the all-time greats
                    </p>
                </div>
                {{-- <a href="{{ route('store.games') }}?discount=true" class="btn btn-sm bg-white hover:bg-gray-100 text-gray-800 border-none">SEE ALL</a> --}}
            </div>

            <div class="relative">
                <!-- Carousel controls -->
                <div class="absolute -left-4 top-1/2 -translate-y-1/2 z-10">
                    <button class="btn btn-circle btn-sm bg-white/80 hover:bg-white border-none text-gray-800 shadow-lg"
                        onclick="scrollCarousel('discount-carousel', -1)">❮</button>
                </div>
                <div class="absolute -right-4 top-1/2 -translate-y-1/2 z-10">
                    <button class="btn btn-circle btn-sm bg-white/80 hover:bg-white border-none text-gray-800 shadow-lg"
                        onclick="scrollCarousel('discount-carousel', 1)">❯</button>
                </div>

                <!-- Carousel container with 3 items per slide -->
                <div id="discount-carousel" class="flex overflow-hidden gap-4 scroll-smooth">
                    @foreach ($featuredDiscounts->chunk(3) as $chunk)
                        <div class="flex-none w-full grid grid-cols-3 gap-4">
                            @foreach ($chunk as $game)
                                <div class="relative">
                                    <a href="{{ route('games.detail', $game) }}"><img
                                            src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }}"
                                            class="w-full aspect-video object-cover rounded" /></a>
                                    <div
                                        class="absolute bottom-0 right-0 bg-gradient-to-t from-black/80 to-transparent rounded-b-lg">
                                        <div class="flex items-center flex-wrap join rounded-sm bg-primary ">
                                            {{-- @if ($game->discount_percentage > 0) --}}
                                            <div class="join-item p-0">
                                                <span
                                                    class="badge badge-success rounded-none">{{ $game->discount_percentage }}%</span>
                                            </div>
                                            <div class="text-primary-content join-item p-2">
                                                <span
                                                    class="text-xs line-through">${{ number_format($game->price, 2) }}</span>
                                                <span
                                                    class="text-base font-bold">${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</span>
                                            </div>
                                            {{-- @else
                                                <span class="text-white font-bold">Rp
                                                    {{ number_format($game->price, 0, ',', '.') }}</span>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                function scrollCarousel(id, direction) {
                    const container = document.getElementById(id);
                    const slideWidth = container.clientWidth;
                    container.scrollBy({
                        left: direction * slideWidth + (direction > 0 ? 16 : -16),
                        behavior: 'smooth'
                    });
                }
            </script>
        </div>
    </div>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
@endsection
