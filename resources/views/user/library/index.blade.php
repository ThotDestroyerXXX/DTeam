@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - My Library
@endsection

@section('content')
    <div class='flex flex-col gap-8'>
        <div class='flex flex-row gap-2'>
            <img src="{{ $userAvatar }}" alt="{{ $username }}" class="avatar rounded size-14 bg-primary" />
            <span class="font-semibold">{{ $username }}</span>
        </div>
        <!-- name of each tab group should be unique -->
        <div class="tabs tabs-border">
            <input type="radio" name="my_tabs_2" class="tab" aria-label="All Games ({{ $libraryGamesCount }})"
                @checked(request('tab', 'games') === 'games') />
            <div class="tab-content border-base-300 bg-base-100">
                <ul class="list">
                    @foreach ($libraryGames as $libraryGame)
                        <li class="list-row">
                            <div><img class="h-32 aspect-video rounded"
                                    src="{{ $libraryGame->game->gameImages->first()->image_url }}"
                                    alt="{{ $libraryGame->game->name }}" />
                            </div>
                            <div class="flex flex-col justify-between gap-2">
                                <h1 class="text-lg font-semibold">{{ $libraryGame->game->title }}</h1>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Purchased at
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ date('d M Y', strtotime($libraryGame->purchase_date)) }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('games.detail', $libraryGame->game) }}"
                                        class="btn btn-primary btn-sm">Store
                                        Page</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <input type="radio" name="my_tabs_2" class="tab" aria-label="Reviews ({{ $gameReviewsCount }})"
                @checked(request('tab') === 'reviews') />
            <div class="tab-content border-base-300 bg-base-100 p-4">
                <h1 class='font-semibold text-lg'>Recent Reviews by {{ $username }}</h1>
                <div class="divider m-0 p-0"></div>
                <div class="flex flex-col gap-4 pt-4">
                    @foreach ($gameReviews as $gameReview)
                        <div class="bg-base-100 flex flex-row gap-4">
                            <img src="{{ $gameReview->game->gameImages->first()->image_url }}"
                                alt="{{ $gameReview->game->name }}"
                                class="h-32 w-auto object-cover aspect-video rounded bg-black shrink-0" />
                            <div class="flex flex-col gap-2 flex-1">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $gameReview->ratingType->image_url }}"
                                        alt="{{ $gameReview->ratingType->title }} Logo"
                                        class="h-10 w-auto inline-block mr-2" />
                                    <div class="flex flex-col">
                                        <span class="font-medium text-sm">{{ $gameReview->ratingType->title }}</span>
                                        <span class="text-xs text-gray-500">Posted:
                                            {{ $gameReview->created_at->format('F j, Y') }}</span>
                                    </div>
                                </div>
                                <p class="text-sm">{{ $gameReview->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
