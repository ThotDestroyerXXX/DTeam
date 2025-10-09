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
            <a href="{{ route('password.request') }}">CHANGE PASSWORD</a>
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
                <span>Balance: ${{ number_format(Auth::user()->balance, 2) }}</span>
            @endcan
            <a href="{{ route('logout') }}">LOGOUT</a>
        @endauth
        @guest
            <a href="{{ route('login') }}"><button class="btn btn-primary">Login</button></a>
            <a href="{{ route('register') }}"><button variant="ghost" class="btn btn-outline">Register</button></a>
        @endguest
    </div>
</div>
