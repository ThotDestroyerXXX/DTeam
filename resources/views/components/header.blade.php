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
                        <div class="dropdown dropdown-end">
                            <span tabindex="0" class="cursor-pointer">{{ Auth::user()->nickname }} ▼</span>
                            <ul tabindex="-1" class="menu dropdown-content bg-base-200 rounded-box z-1 w-52 p-2 shadow-sm">
                                <li><a href="{{ route('logout') }}">LOGOUT</a></li>
                            </ul>
                        </div>

                        <span>${{ number_format(Auth::user()->balance, 2) }}</span>
                    </div>
                    {{-- display profile url if not null, display default profile if null --}}
                    <img src="{{ Auth::user()->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                        alt="{{ Auth::user()->nickname }}" class="avatar size-10 rounded bg-cover object-center" />
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
