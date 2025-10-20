@props(['publisher', 'games'])

<div class="flex items-end justify-between flex-row mb-4">
    <div class="flex flex-row gap-2 items-center">
        <img src="{{ $publisher->image_url }}" alt="{{ $publisher->name }}"
            class="avatar size-16 rounded bg-cover object-center" />
        <div>
            <h1 class="text-2xl font-bold">{{ $publisher->name }}</h1>
            <a href="{{ $publisher->website }}" class="text-blue-500" target="_blank">{{ $publisher->website }}</a>
        </div>
    </div>
    <a href="{{ route('publisher.games.add') }}" class="btn btn-primary">Add Game</a>
</div>

<div>
    <ul class="list bg-base-100 rounded-box shadow-md overflow-hidden">
        @forelse ($games as $game)
            <li class="list-row p-0 pr-6 items-center gap-8">
                <img class="h-24 aspect-[18/10]" src="{{ $game->gameImages->first()->image_url }}"
                    alt="{{ $game->name }}" />
                <div>
                    <h1 class='text-base font-bold line-clamp-1'>{{ $game->title }}</h1>
                    <div class="text-xs uppercase font-medium opacity-60">{{ $game->release_date }}</div>
                </div>
                <button class="btn btn-square btn-success">
                    {{ $game->discount_percentage }}%
                </button>
                <div class="flex flex-col">
                    <div class="text-sm line-through">${{ number_format($game->price, 2) }}</div>
                    <div class="font-bold">
                        ${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</div>
                </div>
                <a href="{{ route('publisher.games.edit', $game->id) }}" class="btn btn-primary">
                    Edit
                </a>
            </li>
        @empty
            <li class="list-row">
                <div>No games found.</div>
            </li>
        @endforelse
    </ul>
    {{ $games->links() }}
</div>
