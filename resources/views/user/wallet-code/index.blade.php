@extends('layouts.app')

@section('title')
    Redeem Wallet Code
@endsection

@section('content')
    <div class="flex flex-col gap-4 w-full">
        <!-- Title Banner -->
        <h1 class="text-2xl font-bold">REDEEM A DTEAM GIFT CARD OR WALLET CODE</h1>
        <!-- Main Content Area -->
        <div class="flex flex-row gap-8">
            <!-- Left Side - Redemption Form -->
            <div class="flex flex-col gap-4 w-2/3">
                <div>

                    <form action="{{ route('user.wallet-code.redeem') }}" method="POST" class="flex gap-4">
                        @csrf
                        <input type="text" name="code" placeholder="DTeam Wallet Code"
                            class="input input-bordered w-full" required />
                        <button type="submit" class="btn btn-primary">Redeem</button>
                    </form>

                    <div class="mt-4 text-sm">
                        <p>Note: If the gift card you attempt to redeem is not in your currency, it will be automatically
                            converted if redeemable in your region. Not all gift card currencies can be redeemed in all
                            regions.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Wallet Information -->
            <div class="w-1/3">
                <div class='bg-primary text-primary-content rounded overflow-hidden'>
                    <h3 class="p-4">Your DTeam Account</h3>

                    <div class="flex justify-between items-center bg-base-100 text-primary p-4 text-sm">
                        <span class="font-medium">Wallet Balance</span>
                        <span class="font-bold">$ {{ number_format($walletBalance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
