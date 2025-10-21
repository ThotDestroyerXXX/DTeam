<!-- Add a friend partial -->
<div class="flex flex-col rounded overflow-hidden">
    <h1 class="text-lg font-bold bg-primary text-primary-content p-3">Add a Friend</h1>

    <div class="bg-base-100 p-4 flex flex-col gap-4">
        <div class='flex flex-col gap-2'>
            <p>Your Friend Code</p>
            <div class="bg-base-200 p-2 rounded flex flex-row justify-between items-center">
                <h2 class="font-semibold text-lg">{{ $user->unique_code }}</h2>
                <button class="btn btn-primary" onclick="navigator.clipboard.writeText('{{ $user->unique_code }}')">
                    Copy
                </button>
            </div>

            <!-- Code search form -->
            <div class="text-sm flex flex-col gap-2">
                <span>Enter your friend's Friend Code to invite them to connect.</span>
                <form method="GET" action="{{ route('user.friends.search') }}" class="flex flex-col gap-2">
                    <div class="input input-bordered w-full">
                        <input type="text" name="search_value" placeholder="Enter Friend Code" />
                    </div>
                    <input type="hidden" name="search_type" value="code">
                </form>
            </div>

            <div class="divider"></div>

            <!-- Nickname search form -->
            <div class="text-sm flex flex-col gap-2">
                <span>Or try searching for your friend</span>
                <form method="GET" action="{{ route('user.friends.search') }}" class="flex flex-col gap-2">
                    <div class="input input-bordered w-full">
                        <input type="text" name="search_value" placeholder="Enter Friend nickname" />
                    </div>
                    <input type="hidden" name="search_type" value="nickname">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Container for search results -->
<div id="searchResults">
    @isset($results)
        @include('user.friends.partials.search-results', [
            'results' => $results,
            'searchValue' => $searchValue,
            'searchType' => $searchType,
        ])
    @endisset
</div>
