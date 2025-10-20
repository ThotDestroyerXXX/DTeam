@extends('layouts.app')

@section('title')
    {{ $game->title }} - Game Details
@endsection

@section('content')
    <div class="flex flex-col gap-4">
        <h1 class="text-3xl font-bold">{{ $game->title }}</h1>

        <div class="bg-base-100 flex flex-row gap-3 p-3 rounded-lg">
            <x-games.media-gallery :game="$game" />
            <x-games.sidebar-info :game="$game" :reviewStatus="$reviewStatus" :recentReviewsCount="$recentReviewsCount" :allReviewStatus="$allReviewStatus"
                :allReviewsCount="$allReviewsCount" />
        </div>

        <x-games.wishlist-button :game="$game" :isGameInWishlist="$isGameInWishlist" />

        @if ($game->isInUserLibrary(Auth::id()))
            <x-games.library-status :game="$game" :userReview="$userReview" />
        @else
            <x-games.purchase-section :game="$game" />
        @endif

        <div class="flex flex-row w-full gap-8 mt-8">
            <x-games.about :game="$game" />
            <x-games.info-sidebar :game="$game" />
        </div>

        <x-games.reviews :game="$game" :allReviewsCount="$allReviewsCount" />
    </div>
@endsection

@push('scripts')
    <!-- Any additional page-specific scripts will be loaded here -->
@endpush
