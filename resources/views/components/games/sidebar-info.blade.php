@props(['game', 'reviewStatus', 'recentReviewsCount', 'allReviewStatus', 'allReviewsCount'])

<div class="w-full max-w-1/3 gap-2 flex flex-col">
    <img src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }} Cover" class="w-full h-auto rounded">
    <p class="text-sm text-justify">{{ $game->brief_description }}</p>

    <div class='text-sm'>
        <div class="flex items-center gap-2">
            <h3 class="font-medium">Recent Reviews:</h3>
            <span class="text-blue-500">{{ $reviewStatus }}</span>
            <span>({{ $recentReviewsCount }} reviews)</span>
        </div>

        <div class="flex items-center gap-2 mt-1">
            <h3 class="font-medium">All Reviews:</h3>
            <span class="text-blue-500">{{ $allReviewStatus }}</span>
            <span>({{ $allReviewsCount }} reviews)</span>
        </div>
        <div class="flex items-center gap-2 mt-1">
            <h3 class="font-medium">Release Date: </h3>
            <span>{{ $game->release_date }}</span>
        </div>
        <div class="flex items-center gap-2 mt-1">
            <h3 class="font-medium">Publisher: </h3>
            <a href="">{{ $game->publisher->name }}</a>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="font-medium">Genres:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($game->genres as $genre)
                    <span class="badge badge-primary badge-sm">{{ $genre->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
