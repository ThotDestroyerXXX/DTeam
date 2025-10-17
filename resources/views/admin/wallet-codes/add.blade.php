@extends('layouts.app')

@section('title')
    Wallet Codes
@endsection

@section('content')
    <div>
        <h1>Add New Wallet Code</h1>
        <form action="{{ route('admin.wallet-codes.store') }}" method="POST" class="mt-4 max-w-md">
            @csrf
            <div class="form-control mb-4">
                <label class="select">
                    <span class="label">Amount</span>
                    <select name="amount" id="amount" class="input input-bordered">
                        @foreach ($walletCodeAmounts as $amount)
                            <option value="{{ $amount->value }}">{{ $amount->value }}$</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="form-control mb-4">
                <label class="input">
                    <span class="label">Quantity</span>
                    <input type="number" name="quantity" required />
                </label>
            </div>
            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" class="btn btn-primary">Create Wallet Code</button>
        </form>
    </div>
@endsection
