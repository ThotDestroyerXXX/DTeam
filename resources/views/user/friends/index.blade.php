@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - {{ $isOwnProfile ? 'My Friends' : $user->nickname . '\'s Friends' }}
@endsection

@section('content')
    <div class='flex flex-col gap-8'>
        <div class='flex flex-row gap-2 items-center'>
            <img src="{{ $user->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                alt="{{ $user->nickname }}" class="avatar rounded size-14 bg-primary" />
            <span class="font-semibold text-xl">{{ $user->nickname }}</span>
        </div>

        <div class="flex gap-4">
            <!-- Sidebar -->
            <div>
                <h2 class="font-bold text-gray-500 text-xs uppercase mb-2">
                    @if ($isOwnProfile)
                        Friends
                    @else
                        {{ $user->nickname }}'s Friends
                    @endif
                </h2>

                <ul class="menu w-52 p-0 m-0">
                    @if ($isOwnProfile)
                        <li><a href="{{ route('user.friends.index') }}"
                                class="{{ $activeTab == 'your-friends' ? 'menu-active' : '' }}">Your Friends</a></li>
                        <li><a href="{{ route('user.friends.add') }}"
                                class="{{ $activeTab == 'add-friend' ? 'menu-active' : '' }}">Add a Friend</a></li>
                        <li><a href="{{ route('user.friends.pending') }}"
                                class="{{ $activeTab == 'pending-invites' ? 'menu-active' : '' }}">Pending Invites</a></li>
                    @else
                        <li><a href="{{ route('user.friends.show', ['user' => $user->id]) }}"
                                class="{{ $activeTab == 'all-friends' ? 'menu-active' : '' }}">All Friends</a></li>
                        <li><a href="{{ route('user.friends.mutual', ['user' => $user->id]) }}"
                                class="{{ $activeTab == 'mutual-friends' ? 'menu-active' : '' }}">Mutual Friends</a></li>
                    @endif
                </ul>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @if ($activeTab == 'your-friends' || $activeTab == 'all-friends')
                    @include('user.friends.partials.friends-list')
                @elseif($activeTab == 'mutual-friends')
                    @include('user.friends.partials.mutual-friends')
                @elseif($activeTab == 'add-friend')
                    @include('user.friends.partials.add-friend')
                @elseif($activeTab == 'pending-invites')
                    @include('user.friends.partials.pending-invites')
                @endif
            </div>
        </div>
    </div>
@endsection
