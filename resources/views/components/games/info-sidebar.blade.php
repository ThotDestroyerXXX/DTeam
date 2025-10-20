@props(['game'])

<div class="w-1/3 flex flex-col gap-2">
    <div class="bg-base-100 p-4 rounded-lg flex flex-row gap-4">
        <img src="{{ $game->ageRating->image_url }}" alt="{{ $game->ageRating->title }} Logo"
            class="h-20 w-auto rounded" />
        <p class="text-sm text-justify">{{ $game->ageRating->description }}</p>
    </div>

    <div class="bg-base-100 p-4 rounded-lg flex flex-col gap-2 text-sm">
        <p>Title: {{ $game->title }}</p>
        <p>Genres: {{ $game->genres->pluck('name')->join(' ') }}</p>
        <p>Publisher: {{ $game->publisher->name }}</p>
        <p>Release Date: {{ $game->release_date }}</p>

        <button class="btn btn-primary">
            <a href="{{ $game->publisher->website }}" target="_blank">
                View Publisher Website
                {{-- add redirect link logo svg --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 inline-block ml-1" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </button>
    </div>
</div>
