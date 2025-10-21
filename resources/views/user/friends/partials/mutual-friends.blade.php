<!-- Friends list partial -->
<div class="flex flex-col gap-4">
    <h1 class="text-lg font-bold bg-primary text-primary-content p-3 rounded">
        Mutual Friends
    </h1>
    <div>
        <form method="GET" action="">
            {{-- search icon svg --}}
            <label for="search" class="input input-bordered w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14"
                    height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <circle cx="10" cy="10" r="7"></circle>
                    <line x1="21" y1="21" x2="15" y2="15"></line>
                </svg>
                <input type="text" name="search" placeholder="Search friends..." value="{{ request('search') }}">
            </label>
        </form>
    </div>
    <!-- Content will be implemented later -->
    @if ($friends->isEmpty())
        <div class="flex flex-col items-center justify-center p-8">
            <p class="text-gray-500">No mutual friends found.</p>
        </div>
    @else
        {{-- search friend by name --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Friend items will go here -->
            @foreach ($friends as $friend)
                <div class="bg-base-100 p-4 rounded-lg text-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ $friend['profile_picture_url'] }}" alt="{{ $friend['nickname'] }}"
                            class="avatar rounded size-12 bg-primary" />
                        <div>
                            <span class="font-semibold">{{ $friend['nickname'] }}</span>
                            <p class=" text-gray-500">Currently Offline</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if (isset($paginationLinks))
            {{ $paginationLinks->links() }}
        @endif
    @endif
</div>
