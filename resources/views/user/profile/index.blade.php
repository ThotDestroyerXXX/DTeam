@extends('layouts.app')

@section('title')
    User Profile
@endsection

@section('content')
    <div class='flex flex-col gap-6 text-sm'>
        <div class="flex flex-row justify-between gap-4">
            <div class="flex flex-row gap-4 w-[70%] h-full">
                <img src="{{ $user->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                    alt="{{ $user->nickname }}" class="avatar size-32 rounded bg-black object-cover object-center shrink-0" />
                <div class="flex flex-col justify-between">
                    <div class="flex flex-col">
                        <h1 class="text-lg font-bold">{{ $user->nickname }}</h1>
                        <span>{{ $user->real_name }}, {{ $user->country->name }}</span>
                    </div>
                    <p class="text-gray-500">{{ $user->bio }}</p>
                </div>
            </div>
            <div class='w-[30%] flex flex-col gap-2'>
                <div class='text-lg w-full flex flex-row gap-2 items-center'>
                    <p class='font-medium'>Level</p>
                    <span
                        class="badge rounded-full badge-md bg-primary text-primary-content">{{ $user->gameLibraries->count() }}</span>

                </div>
                @if (Auth::id() === $user->id)
                    <a href="{{ route('user.profile.edit.section', ['section' => 'general']) }}"
                        class="btn btn-secondary btn-sm">Edit
                        Profile</a>
                @endif
            </div>
        </div>
        <div class="flex flex-row justify-between gap-4">
            <div class="flex flex-col items-center gap-4 w-[70%]">
                <div class="bg-primary text-primary-content p-3 rounded w-full shadow-md">
                    <h2>Recently Added</h2>
                </div>
                <div class=" border-base-300 bg-base-100 w-full rounded shadow-md">
                    <ul class="list">
                        @foreach ($userGames as $userGame)
                            <li class="list-row">
                                <div><img class="h-32 aspect-video rounded "
                                        src="{{ $userGame->game->gameImages->first()->image_url }}"
                                        alt="{{ $userGame->game->name }}" />
                                </div>
                                <div class="flex flex-col justify-between gap-2">
                                    <h1 class="text-lg font-semibold">{{ $userGame->game->title }}</h1>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Purchased at
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ date('d M Y', strtotime($userGame->purchase_date)) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('games.detail', $userGame->game) }}"
                                            class="btn btn-primary btn-sm">Store
                                            Page</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class='w-[30%] flex flex-col rounded shadow-md overflow-hidden h-fit'>
                <div class='bg-primary text-primary-content p-3'>
                    <h2 class='text-lg font-semibold'>Currently Online</h2>
                    <p>Joined Since {{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div class="bg-base-100 w-full">
                    <ul class="list">
                        @if (Auth::check() && Auth::id() === $user->id)
                            <a href="{{ route('user.library.index', ['tab' => 'games']) }}" class="list-row">
                                Games
                            </a>
                            <a href="{{ route('user.library.index', ['tab' => 'reviews']) }}" class="list-row">
                                Reviews
                            </a>
                        @endif
                        <li class="list-row flex flex-col">
                            <a href="{{ route('user.friends.show', $user->id) }}">Friends</a>
                            <div class="flex flex-col gap-2">
                                @foreach ($userFriends as $friend)
                                    <a href="{{ route('user.profile.index', $friend->friend->id) }}"
                                        class='flex flex-row gap-2 items-center'>
                                        <div class="avatar size-10 rounded overflow-hidden">
                                            <img src="{{ $friend->friend->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                                                alt="{{ $friend->nickname }}">
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $friend->friend->nickname }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
