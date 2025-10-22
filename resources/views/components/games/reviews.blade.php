@props(['game', 'allReviewsCount'])

<h1 class="text-2xl font-semibold">Customer Review</h1>
{{-- no customer review --}}
@if ($allReviewsCount > 0)
    {{-- display all reviews --}}
    @foreach ($game->gameReviews as $review)
        <div class="bg-base-100 flex flex-row gap-2 mb-2 p-4 rounded-lg">
            <div class="flex flex-row w-1/4 gap-2">
                <img src="{{ $review->user->profile_picture_url }}" alt="{{ $review->user->name }} Profile"
                    class="avatar size-16 rounded shrink-0 bg-black" />
                <div class="flex flex-col">
                    <span class="font-medium text-sm">{{ $review->user->nickname }}</span>
                    <span class="text-xs">{{ $review->user->gameReviews->count() }} reviews</span>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-3/4">
                <div class="flex items-center gap-2">
                    <img src="{{ $review->ratingType->image_url }}" alt="{{ $review->ratingType->title }} Logo"
                        class="h-10 w-auto inline-block mr-2" />
                    <div class="flex flex-col">
                        <span class="font-medium text-sm">{{ $review->ratingType->title }}</span>
                        <span class="text-xs text-gray-500">Posted:
                            {{ $review->created_at->format('F j, Y') }}</span>
                    </div>
                </div>
                <p class="text-sm">{{ $review->content }}</p>
            </div>
        </div>
    @endforeach
@else
    <p class="text-sm">No customer reviews yet. Be the first to review this game!</p>
@endif
