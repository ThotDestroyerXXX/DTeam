<div class="max-w-7xl w-full h-full flex justify-between mx-auto ">
    <div class="flex flex-row gap-4 items-center">
        <h1>DTeam</h1>
        @can('is-admin')
            <a href="{{ route('admin.publishers.index') }}">MANAGE PUBLISHER</a>
            <a href="{{ route('admin.wallet-codes.index') }}">MANAGE WALLET CODE</a>
            <a href="{{ route('admin.genres.index') }}">MANAGE GENRE</a>
        @elsecan('is-publisher')
            <a href="{{ route('publisher.games.index') }}">MANAGE GAMES</a>
            <a href="{{ route('publisher.profile.edit') }}">EDIT PROFILE</a>
            {{-- <a href="{{ route('password.request') }}">CHANGE PASSWORD</a> --}}
        @elsecan('is-user')
            <a href="{{ route('store.index') }}">STORE</a>
            <a href="{{ route('user.library.index') }}">LIBRARY</a>
        @endcan
        @guest
            <a href="{{ route('store.index') }}">STORE</a>
        @endguest
    </div>
    <div class="flex flex-row gap-4 items-center">
        @auth
            @can('is-user')
                <div class="flex flex-end flex-row gap-4 items-center">
                    <div class="flex flex-col text-end text-sm">
                        <div class="flex flex-row items-center gap-2">
                            <div class="bg-primary text-primary-content rounded p-2 mr-2 relative cursor-pointer dropdown dropdown-end"
                                tabindex='0'>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 " fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <ul tabindex="-1"
                                    class="menu dropdown-content text-xs bg-primary text-primary-content rounded z-1 w-52 p-2 shadow-sm mt-4">
                                    <li><a href="{{ route('user.friends.pending') }}">{{ $friendRequestCount }} NEW FRIEND
                                            REQUEST</a></li>
                                    <li><a href="{{ route('user.game-gift.index') }}">{{ $gameGiftCount }} NEW GAME GIFT</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown dropdown-end">
                                {{-- bell svg icon --}}


                                <span tabindex="0" class="cursor-pointer">{{ Auth::user()->nickname }} ▼</span>
                                <ul tabindex="-1" class="menu dropdown-content bg-base-200 rounded-box z-1 w-52 p-2 shadow-sm">
                                    <li><a href="{{ route('user.profile.edit') }}">VIEW MY PROFILE</a></li>
                                    <li><a href="{{ route('user.transaction.index') }}">MY TRANSACTIONS</a></li>
                                    <li><a href="{{ route('user.wallet-code.index') }}">REDEEM WALLET CODE</a></li>
                                    <li><a href="{{ route('logout') }}">LOGOUT</a></li>
                                </ul>
                            </div>
                        </div>
                        <span>${{ number_format(Auth::user()->wallet, 2) }}</span>
                    </div>
                    {{-- display profile url if not null, display default profile if null --}}
                    <img src="{{ Auth::user()->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                        alt="{{ Auth::user()->nickname }}" class="avatar size-10 rounded bg-cover object-center bg-black" />
                </div>
            @else
                <a href="{{ route('logout') }}">LOGOUT</a>
            @endcan

        @endauth
        @guest
            <a href="{{ route('login') }}"><button class="btn btn-primary">Login</button></a>
            <a href="{{ route('register') }}"><button variant="ghost" class="btn btn-outline">Register</button></a>
        @endguest
    </div>
</div>
