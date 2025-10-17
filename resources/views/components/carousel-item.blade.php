<div id={{ 'slide' . $index }}
    class="carousel-item relative w-full flex flex-row gap-4 text-primary-content overflow-hidden">
    <a href="{{ route('games.detail', $gameId) }}"
        class="carousel-item relative w-full flex flex-row gap-4 text-primary-content">
        <div class="flex-shrink-0 h-full">
            <img src="{{ $thumbnail }}" alt="{{ $title }}"
                class="h-full aspect-[16/10] bg-cover bg-center rounded-box" />
        </div>
        <div class="flex flex-col gap-4 h-full">
            <h2 class="font-bold text-2xl mt-2">{{ $title }}</h2>
            <div class="grid grid-cols-2 grid-rows-2 gap-2">
                @foreach (array_slice($images, 1, 4) as $image)
                    <img src="{{ $image }}" alt="{{ $title }}"
                        class="size-full object-cover rounded-lg" />
                @endforeach
            </div>
            <h3 class="font-medium text-xl">Now Available</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($genres as $genre)
                    <span class="badge badge-soft badge-primary text-sm">{{ $genre }}</span>
                @endforeach
            </div>
            <div class="mt-2 gap-2 flex items-center">
                @if ($discount > 0)
                    <span class="badge badge-success rounded-sm">{{ $discount }}%</span>
                    <div>
                        <span class="text-sm line-through">${{ number_format($price, 2) }}</span>
                        <span class="text-lg font-bold">${{ number_format($price * (1 - $discount / 100), 2) }}</span>
                    </div>
                @else
                    <span class="text-lg font-bold">${{ number_format($price, 2) }}</span>
                @endif
            </div>
        </div>
    </a>
    <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
        <a href="#slide{{ ($index + 9) % 10 }}" class="btn btn-circle">❮</a>
        <a href="#slide{{ ($index + 11) % 10 }}" class="btn btn-circle">❯</a>
    </div>
</div>
