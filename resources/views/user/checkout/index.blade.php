@extends('layouts.app')

@section('title')
    Checkout
@endsection

@section('content')
    <div class="container gap-6 flex flex-col">
        <h1 class="font-semibold text-3xl">Checkout</h1>
        @if ($cartItems->isEmpty())
            <p>No item to checkout.</p>
        @else
            <div class="flex flex-col gap-4">

                <div class="flex flex-row gap-4">
                    <div class="flex flex-col gap-4 flex-1 max-w-2/3">
                        @foreach ($cartItems as $cart)
                            <div class="flex flex-col gap-4 bg-base-100 p-3 rounded-box">
                                <div class="flex flex-row gap-4">
                                    <img src="{{ $cart->game->gameImages->first()->image_url }}"
                                        alt="{{ $cart->game->title }}" class="h-24 aspect-video rounded" />
                                    <div class="flex flex-col justify-between w-full">
                                        <p class="font-medium ">{{ $cart->game->title }}</p>
                                        <div class="flex flex-row justify-between w-full">
                                            <select name="is_gift" class="select select-sm w-auto min-w-[150px] self-end"
                                                disabled>
                                                @if (!(Auth::check() && Auth::user()->gameLibraries()->where('game_id', $cart->game->id)->exists()))
                                                    <option value="0" {{ !$cart->is_gift ? 'selected' : '' }}>For
                                                        my
                                                        account
                                                    </option>
                                                @else
                                                    <option value="1" {{ $cart->is_gift ? 'selected' : '' }}>This is
                                                        a
                                                        gift
                                                    </option>
                                                @endif

                                            </select>
                                            <div class="flex flex-col self-end">
                                                @if ($cart->game->discount_percentage > 0)
                                                    <span
                                                        class="text-sm font-bold text-end">${{ number_format($cart->game->price * (1 - $cart->game->discount_percentage / 100), 2) }}</span>
                                                @else
                                                    <span
                                                        class="text-sm font-bold">${{ number_format($cart->game->price, 2) }}</span>
                                                @endif
                                                {{-- remove --}}
                                                <form action="{{ route('user.cart.remove', $cart->game) }}" method="POST"
                                                    class="self-end">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-sm p-0">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($cart->is_gift)
                                    <div class="gift-recipient-section" id="gift-recipient-section-{{ $cart->id }}"
                                        @if (isset($cart->recipient_id)) data-recipient-id="{{ $cart->recipient_id }}"
                                        data-recipient-name="{{ $cart->recipient->nickname }}" @endif>
                                        @if (isset($cart->recipient_id))
                                            <div
                                                class="bg-neutral px-4 py-2 text-neutral-content rounded selected-recipient">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-1">
                                                        <span class="font-medium">Gift Recipient:</span>
                                                        <span
                                                            class="badge badge-sm">{{ $cart->recipient->nickname }}</span>
                                                    </div>
                                                    <button class="text-primary hover:underline text-sm"
                                                        onclick="document.getElementById('select-recipient-modal-{{ $cart->id }}').showModal()">
                                                        Edit
                                                    </button>
                                                </div>

                                                <div class="mb-2">
                                                    <span class="font-medium block mb-1">Gift Message:</span>
                                                    <textarea class="textarea textarea-bordered w-full bg-neutral-focus text-neutral-content border-neutral-focus"
                                                        id="gift-message-{{ $cart->id }}" placeholder="Add a personal message to your gift">{{ $cart->gift_message ?? '' }}</textarea>
                                                </div>

                                                <div class="flex items-center">
                                                    <span class="font-medium mr-1">From:</span>
                                                    <div class="avatar mr-1">
                                                        <div class="w-6 h-6 rounded-full">
                                                            <img src="{{ Auth::user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->nickname) }}"
                                                                alt="Your avatar" />
                                                        </div>
                                                    </div>
                                                    <span>{{ Auth::user()->nickname }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <button class="btn btn-primary btn-sm w-full"
                                                onclick="document.getElementById('select-recipient-modal-{{ $cart->id }}').showModal()">
                                                Select Gift Recipient...
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Gift Recipient Modal -->
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
                                                            $isOnWishlist = $friend
                                                                ->gameWishlists()
                                                                ->where('game_id', $cart->game->id)
                                                                ->exists();
                                                            $alreadyOwns = $friend
                                                                ->gameLibraries()
                                                                ->where('game_id', $cart->game->id)
                                                                ->exists();
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
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-col w-1/3 rounded overflow-hidden">
                        <div class="bg-primary text-primary-content p-2">
                            <h2 class="font-semibold">Purchase Details</h2>
                        </div>
                        <div class="bg-base-100 p-4 flex flex-col gap-4 self-start w-full">
                            <div class="flex flex-row justify-between">
                                <h2>Wallet Balance</h2>
                                <span class="font-bold">${{ number_format(Auth::user()->wallet, 2) }}</span>
                            </div>
                            <div class="flex flex-row justify-between">
                                <h2>Total</h2>
                                <span class="font-bold">${{ number_format($cartTotal, 2) }}</span>
                            </div>

                            <form method="POST" action="{{ route('user.checkout.process') }}"
                                onsubmit="return prepareGiftDetails()">
                                @csrf
                                <input type="hidden" id="gift-details" name="gift_details" value="">
                                <button id="checkout-button" type="submit" class="btn btn-primary w-full"
                                    disabled>Checkout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Store selected recipients in memory until checkout
        let selectedRecipients = {};
        let giftMessages = {};

        // Keep track of gift items that need recipients
        let giftItemsRequiringRecipients = [];

        // Current user info for the "From" section
        const currentUserName = "{{ Auth::user()->nickname }}";
        const currentUserPictureUrl =
            "{{ Auth::user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->nickname) }}";

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Find all gift items that need recipients
            document.querySelectorAll('.gift-recipient-section').forEach(section => {
                const cartId = section.id.replace('gift-recipient-section-', '');
                if (!section.querySelector('.selected-recipient')) {
                    giftItemsRequiringRecipients.push(cartId);
                } else {
                    // If there's already a recipient selected (from previous page load)
                    const recipientId = section.dataset.recipientId;
                    const recipientName = section.dataset.recipientName;
                    if (recipientId && recipientName) {
                        selectedRecipients[cartId] = {
                            id: recipientId,
                            name: recipientName
                        };
                    }
                }
            });

            // Update checkout button state
            updateCheckoutButtonState();
        });

        function selectRecipient(cartId, friendId, friendName, profilePictureUrl) {
            // Store the selected recipient for this cart item
            selectedRecipients[cartId] = {
                id: friendId,
                name: friendName,
                profilePicture: profilePictureUrl
            };

            // Remove this cart from the requiring recipients list
            const index = giftItemsRequiringRecipients.indexOf(cartId);
            if (index > -1) {
                giftItemsRequiringRecipients.splice(index, 1);
            }

            // Update checkout button state
            updateCheckoutButtonState();

            // Close the modal
            document.getElementById(`select-recipient-modal-${cartId}`).close();

            // Update the UI to show the selected recipient
            const recipientSection = document.getElementById(`gift-recipient-section-${cartId}`);

            recipientSection.innerHTML = `
                <div class="selected-recipient">
                    <div class="divider m-0 p-0"></div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-1">
                            <span class="font-medium">Gift Recipient:</span>
                            <span>${friendName}</span>
                        </div>
                        <button class="btn btn-link btn-sm"
                            onclick="document.getElementById('select-recipient-modal-${cartId}').showModal()">
                            Edit
                        </button>
                    </div>

                    <div class="mb-2 text-sm">
                        <span class="font-medium block mb-1">Gift Message:</span>
                        <textarea class="textarea textarea-bordered w-full"
                            id="gift-message-${cartId}"
                            oninput="updateGiftMessage('${cartId}')"
                            placeholder="Add a personal message to your gift">${giftMessages[cartId] || ''}</textarea>
                    </div>

                    <div class="flex items-center text-sm">
                        <span class="font-medium mr-1">From:</span>
                        <div class="avatar mr-1">
                            <div class="w-6 h-6 rounded-full">
                                <img src="${currentUserPictureUrl}" alt="Your avatar" />
                            </div>
                        </div>
                        <span>${currentUserName}</span>
                    </div>
                </div>
            `;
        }

        function updateGiftMessage(cartId) {
            const messageElement = document.getElementById(`gift-message-${cartId}`);
            giftMessages[cartId] = messageElement.value;
        }

        // Update checkout button enabled/disabled state
        function updateCheckoutButtonState() {
            const checkoutButton = document.getElementById('checkout-button');
            if (!checkoutButton) return;

            // Check if all gift items have recipients
            if (giftItemsRequiringRecipients.length > 0) {
                checkoutButton.disabled = true;
                checkoutButton.title = "Please select a recipient for all gifts before checkout";
            } else {
                checkoutButton.disabled = false;
                checkoutButton.title = "";
            }
        }

        // When checking out, collect all gift details
        function prepareGiftDetails() {
            // Double-check that all gifts have recipients
            if (giftItemsRequiringRecipients.length > 0) {
                alert("Please select a recipient for all gift items before checkout.");
                return false;
            }

            const giftDetails = [];

            Object.keys(selectedRecipients).forEach(cartId => {
                giftDetails.push({
                    cart_id: cartId,
                    recipient_id: selectedRecipients[cartId].id,
                    message: giftMessages[cartId] || ''
                });
            });

            // Store in hidden input
            document.getElementById('gift-details').value = JSON.stringify(giftDetails);

            // Submit the form
            return true;
        }
    </script>
@endsection
