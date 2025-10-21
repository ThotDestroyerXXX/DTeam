@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - Transaction History
@endsection

@section('content')
    <div class="flex flex-col gap-4 w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <h1 class="font-bold text-xl uppercase">My Transaction History</h1>

        @if ($transactions->isEmpty())
            <p class="text-center text-gray-500">You have no transactions yet.</p>
        @else
            <div class="overflow-x-auto bg-base-300 rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral text-neutral-content uppercase">
                        <tr>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Items</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Wallet Change</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach ($transactions as $transaction)
                            <tr class="bg-base-300 hover:bg-base-200">
                                <td class="px-6 py-4">
                                    {{ date('d M Y', strtotime($transaction['transaction_date'])) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $transaction['item_name'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $transaction['transaction_type'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <span>
                                        {{ $transaction['transaction_status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    {{ $transaction['wallet_change'] > 0 ? '+' : '-' }}$
                                    {{ number_format(abs($transaction['wallet_change']), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6 mb-4">
                <div class="btn-group">
                    @if ($transactions->onFirstPage())
                        <button class="btn btn-disabled btn-sm">«</button>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-sm">«</a>
                    @endif

                    @for ($i = 1; $i <= $transactions->lastPage(); $i++)
                        <a href="{{ $transactions->url($i) }}"
                            class="btn btn-sm {{ $transactions->currentPage() == $i ? 'btn-active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="btn btn-sm">»</a>
                    @else
                        <button class="btn btn-disabled btn-sm">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
