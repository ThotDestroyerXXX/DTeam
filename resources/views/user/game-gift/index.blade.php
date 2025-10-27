@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - Game Gifts
@endsection

@section('content')
    <div class="flex flex-col gap-4 w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <h1 class="font-bold text-xl uppercase">My Game Gifts</h1>

        @if ($gameGifts->isEmpty())
            <p class="text-center text-gray-500">You have no game gifts at the moment.</p>
        @else
            {{-- Group gifts by sender for UI layout --}}
            @php
                $giftsBySender = $gameGifts->groupBy('sender_id');
            @endphp

            <div class="flex flex-col gap-6 text-sm">
                @foreach ($giftsBySender as $senderId => $gifts)
                    @php
                        $sender = $gifts->first()->sender;
                    @endphp
                    <div class="rounded overflow-hidden flex flex-col">
                        {{-- Gift header - similar to the image --}}
                        <div class="bg-neutral text-neutral-content p-3 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">You received a gift from</span>
                                <div class="avatar">
                                    <a href="{{ route('user.profile.index', $sender) }}" class="w-8 h-8 rounded-full">
                                        <img src="{{ $sender->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                                            alt="{{ $sender->nickname }}'s avatar">
                                    </a>
                                </div>
                                <a href="{{ route('user.profile.index', $sender) }}"
                                    class="font-semibold">{{ $sender->nickname }}</a>
                            </div>

                            {{-- Accept button could go here if we want to accept all gifts from this sender --}}
                            @if ($gifts->where('status', \App\Enums\GameGiftStatus::PENDING->value)->count() > 0)
                                <form action="{{ route('user.game-gift.store', $sender) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">ACCEPT ALL</button>
                                </form>
                            @endif
                        </div>

                        {{-- Gifts from this sender --}}
                        <div class="flex flex-col gap-0">
                            @foreach ($gifts as $gift)
                                <div class="overflow-hidden bg-base-100 border-b border-base-300">
                                    <div class="flex p-4">
                                        <div class="flex-shrink-0 mr-4">
                                            <img src="{{ $gift->game->gameImages->first()->image_url ?? 'https://placehold.co/300x200?text=No+Image' }}"
                                                alt="{{ $gift->game->title }}"
                                                class="h-32 w-auto aspect-video object-cover rounded">
                                        </div>
                                        <div class="flex flex-col justify-between flex-1">
                                            <div class="flex flex-col gap-1">
                                                <h2 class="font-semibold mb-1 text-base">{{ $gift->game->title }}</h2>
                                                <div class='flex flex-col'>
                                                    <p class="text-sm font-medium">
                                                        RECEIVED AT</p>
                                                    <span
                                                        class="opacity-75">{{ $gift->created_at->format('d M Y') }}</span>
                                                </div>
                                                </p>
                                                @if ($gift->message)
                                                    <div class="flex flex-col">
                                                        <div class="font-medium">MESSAGE</div>
                                                        <p class="opacity-75">{{ $gift->message }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
