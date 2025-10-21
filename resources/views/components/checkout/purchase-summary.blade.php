@props(['cartTotal'])

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

        <form method="POST" action="{{ route('user.checkout.process') }}" onsubmit="return prepareGiftDetails()">
            @csrf
            <input type="hidden" id="gift-details" name="gift_details" value="">
            <button id="checkout-button" type="submit" class="btn btn-primary w-full" disabled>Checkout</button>
        </form>
    </div>
</div>
