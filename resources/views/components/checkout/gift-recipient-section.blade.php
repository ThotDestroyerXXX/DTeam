@props(['cart'])

<div class="gift-recipient-section" id="gift-recipient-section-{{ $cart->id }}"
    @if (isset($cart->recipient_id)) data-recipient-id="{{ $cart->recipient_id }}"
    data-recipient-name="{{ $cart->recipient->nickname }}" @endif>
    @if (isset($cart->recipient_id))
        <div class="bg-neutral px-4 py-2 text-neutral-content rounded selected-recipient">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <span class="font-medium">Gift Recipient:</span>
                    <span class="badge badge-sm"><img src="{{ $cart->recipient->profile_picture_url }}"
                            alt="{{ $cart->recipient->nickname }}'s avatar"
                            class="w-4 h-4 rounded-full inline-block" />{{ $cart->recipient->nickname }}</span>
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
<x-checkout.gift-recipient-modal :cart="$cart" />
