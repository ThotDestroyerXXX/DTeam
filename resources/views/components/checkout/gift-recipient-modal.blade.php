@props(['cart'])

<dialog id="select-recipient-modal-{{ $cart->id }}" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Select Gift Recipient</h3>

        <div class="divider"></div>

        <div class="max-h-[300px] overflow-y-auto">
            @php
                $friendLists = Auth::user()->friendLists()->with('friend')->get();
            @endphp

            @if ($friendLists->isEmpty())
                <p class="text-center py-4">You don't have any friends yet. Add friends
                    to send gifts!</p>
            @else
                @foreach ($friendLists as $friendList)
                    @php
                        $friend = $friendList->friend;
                        $isOnWishlist = $friend->gameWishlists()->where('game_id', $cart->game->id)->exists();
                        $alreadyOwns = $friend->gameLibraries()->where('game_id', $cart->game->id)->exists();
                    @endphp

                    <div
                        class="flex justify-between items-center p-2 hover:bg-base-200 rounded {{ $alreadyOwns ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="{{ $friend->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->nickname) }}"
                                        alt="{{ $friend->nickname }}'s avatar" />
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">{{ $friend->nickname }}</p>
                                @if ($isOnWishlist)
                                    <span class="badge badge-accent">On Wishlist</span>
                                @endif

                                @if ($alreadyOwns)
                                    <span class="badge">Already owns this item</span>
                                @endif
                            </div>
                        </div>

                        @if (!$alreadyOwns)
                            <button class="btn btn-sm btn-primary"
                                onclick="selectRecipient('{{ $cart->id }}', '{{ $friend->id }}', '{{ $friend->nickname }}', '{{ $friend->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->nickname) }}')">
                                Select
                            </button>
                        @else
                            <button class="btn btn-sm btn-disabled">Select</button>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>
</dialog>
