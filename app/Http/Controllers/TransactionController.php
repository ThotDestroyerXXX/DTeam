<?php

namespace App\Http\Controllers;

use App\Enums\GameGiftStatus;
use App\Models\Game;
use App\Models\GameGift;
use App\Models\GameLibrary;
use App\Models\WalletCode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $perPage = 10;
        $page = request()->get('page', 1);

        // Get library transactions (game purchases)
        $libraryTransactions = $this->getLibraryTransactions($userId);

        // Get gift transactions (gifts sent to others)
        $giftTransactions = $this->getGiftTransactions($userId);

        // Get wallet top-up transactions
        $walletTransactions = $this->getWalletTransactions($userId);

        // Merge all transactions and sort by date
        $allTransactions = $libraryTransactions->concat($giftTransactions)->concat($walletTransactions);
        $sortedTransactions = $allTransactions->sortByDesc('transaction_date');

        // Create a custom paginator
        $items = $sortedTransactions->forPage($page, $perPage);
        $paginator = new LengthAwarePaginator(
            $items,
            $sortedTransactions->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('user.transaction.index', [
            'transactions' => $paginator
        ]);
    }

    /**
     * Get transactions from the game library
     */
    private function getLibraryTransactions(string $userId)
    {
        $libraryItems = GameLibrary::where('user_id', $userId)
            ->with(['game'])
            ->get();

        return $libraryItems->map(function ($item) {
            $originalPrice = $item->game->price;
            $discountPercentage = $item->discount_percentage ?? 0;
            $actualPrice = $originalPrice * (1 - ($discountPercentage / 100));

            return [
                'id' => $item->id,
                'transaction_date' => $item->created_at,
                'item_name' => $item->game->title,
                'transaction_type' => 'Purchase',
                'transaction_status' => 'Success',
                'wallet_change' => $actualPrice,
                'original_data' => $item
            ];
        });
    }

    /**
     * Get transactions from sent gifts
     */
    private function getGiftTransactions(string $userId)
    {
        $giftItems = GameGift::where('sender_id', $userId)
            ->with(['game', 'receiver'])
            ->get();

        return $giftItems->map(function ($item) {
            $originalPrice = $item->game->price;
            $discountPercentage = $item->discount_percentage ?? 0;
            $actualPrice = $originalPrice * (1 - ($discountPercentage / 100));

            $statusText = match ($item->status) {
                GameGiftStatus::PENDING => 'Pending',
                GameGiftStatus::ACCEPTED => 'Accepted',
                default => 'Pending'
            };

            return [
                'id' => $item->id,
                'transaction_date' => $item->created_at,
                'item_name' => $item->game->title . ' (Gift to ' . $item->receiver->nickname . ')',
                'transaction_type' => 'Gift to ' . $item->receiver->nickname,
                'transaction_status' => $statusText,
                'wallet_change' => $actualPrice,
                'original_data' => $item
            ];
        });
    }

    /**
     * Get wallet top-up transactions
     */
    private function getWalletTransactions(string $userId)
    {
        $walletCodes = WalletCode::where('user_id', $userId)
            ->where('is_used', true)
            ->get();

        return $walletCodes->map(function ($item) {
            return [
                'id' => $item->id,
                'transaction_date' => $item->used_at ?? $item->updated_at,
                'item_name' => 'Purchase $' . number_format($item->amount, 2) . ' Wallet Credit',
                'transaction_type' => 'Purchase',
                'transaction_status' => 'Success',
                'wallet_change' => $item->amount,
                'original_data' => $item
            ];
        });
    }
}
