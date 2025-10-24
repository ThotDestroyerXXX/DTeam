@props(['cart'])

<dialog id="select-recipient-modal-{{ $cart->id }}" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Select Gift Recipient</h3>

        <div class="divider"></div>

        <div class="max-h-[300px] overflow-y-auto">
            @php
                // Get friends where current user is user_id
                $currentUserId = Auth::id();
                $friendsAsUser = Auth::user()->friendLists()->with('friend')->get();

                // Get friends where current user is friend_id
                $friendsAsFriend = App\Models\FriendList::where('friend_id', $currentUserId)->with('user')->get();

                // Create a collection of all friend users
                $allFriends = collect();

                // Add friends from first relationship type
                foreach ($friendsAsUser as $friendship) {
                    $allFriends->push(
                        (object) [
                            'id' => $friendship->friend->id,
                            'user' => $friendship->friend,
                        ],
                    );
                }

                // Add friends from second relationship type
                foreach ($friendsAsFriend as $friendship) {
                    $allFriends->push(
                        (object) [
                            'id' => $friendship->user->id,
                            'user' => $friendship->user,
                        ],
                    );
                }

                // Remove any duplicates by friend id
                $uniqueFriends = $allFriends->unique('id');
            @endphp

            @if ($uniqueFriends->isEmpty())
                <p class="text-center py-4">You don't have any friends yet. Add friends
                    to send gifts!</p>
            @else
                @foreach ($uniqueFriends as $friendItem)
                    @php
                        $friend = $friendItem->user;
                        $isOnWishlist = $friend->gameWishlists()->where('game_id', $cart->game->id)->exists();
                        $alreadyOwns = $friend->gameLibraries()->where('game_id', $cart->game->id)->exists();
                    @endphp

                    <div
                        class="flex justify-between items-center p-2 hover:bg-base-200 rounded {{ $alreadyOwns ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('user.profile.index', $friend->id) }}" class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="{{ $friend->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->nickname) }}"
                                        alt="{{ $friend->nickname }}'s avatar" />
                                </div>
                            </a>
                            <div>
                                <a href="{{ route('user.profile.index', $friend->id) }}"
                                    class="font-medium">{{ $friend->nickname }}</a>
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
