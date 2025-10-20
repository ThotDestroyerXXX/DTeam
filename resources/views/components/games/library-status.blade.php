@props(['game', 'userReview'])

<div class="flex flex-col">
    <div class="bg-primary p-4 items-center relative flex min-h-16">
        <div
            class="absolute -left-4 top-1/2 -translate-y-1/2 flex items-center gap-1 bg-success shadow-xl px-2 py-1 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p>in library</p>
        </div>
        <p class="text-sm ml-16 font-semibold text-primary-content">{{ $game->title }} is already in your
            library
        </p>
    </div>

    {{-- display user review if user already reviewed the game --}}
    @if ($userReview)
        <div class="bg-base-100 p-4 flex flex-col gap-2">
            <h2 class="font-semibold">You reviewed this game on
                {{ $userReview->created_at->format('F j, Y') }}</h2>
            <div class="bg-base-200">
                <img src="{{ $userReview->ratingType->image_url }}" alt="{{ $userReview->ratingType->title }} Logo"
                    class="h-10 w-auto inline-block mr-2" />
                <span class="font-medium text-sm">{{ $userReview->ratingType->title }}</span>
            </div>
            <p class="text-sm">{{ $userReview->content }}</p>
        </div>
    @endif
</div>
