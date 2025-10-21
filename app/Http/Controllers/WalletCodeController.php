<?php

namespace App\Http\Controllers;

use App\Enums\WalletCodeAmount;
use App\Http\Requests\WalletCodeRequest;
use App\Models\WalletCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletCode::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', '%' . $search . '%');
        }

        $codes = $query->paginate(10)->withQueryString();
        return view('admin.wallet-codes.index', [
            'walletCodes' => $codes
        ]);
    }

    public function add()
    {
        return view('admin.wallet-codes.add', [
            'walletCodeAmounts' => WalletCodeAmount::cases()
        ]);
    }

    public function store(WalletCodeRequest $request)
    {
        $inputs = $request->validated();

        for ($i = 0; $i < $inputs['quantity']; $i++) {
            // generate unique code in format: XXXX-XXXX-XXXX-XXXX where X is an uppercase letter or digit
            $inputs = array_merge($inputs, [
                'code' => strtoupper(substr(bin2hex(random_bytes(8)), 0, 4)) . '-' .
                    strtoupper(substr(bin2hex(random_bytes(8)), 0, 4)) . '-' .
                    strtoupper(substr(bin2hex(random_bytes(8)), 0, 4)) . '-' .
                    strtoupper(substr(bin2hex(random_bytes(8)), 0, 4))
            ]);
            WalletCode::create([
                'code' => $inputs['code'],
                'amount' => $inputs['amount'],
            ]);
        }

        return redirect()->route('admin.wallet-codes.index')->with('success', 'Wallet code created successfully.');
    }

    public function indexUser()
    {
        $user = Auth::user();
        return view('user.wallet-code.index', [
            'walletBalance' => $user->wallet,
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');
        $walletCode = WalletCode::where('code', $code)
            ->where('is_used', false)
            ->first();

        if (!$walletCode) {
            return redirect()->back()->with('error', 'Invalid or already redeemed wallet code.');
        }

        $user = Auth::user();

        // Update wallet code
        $walletCode->update([
            'is_used' => true,
            'used_at' => now(),
            'user_id' => $user->id,
        ]);

        // Add amount to user's wallet
        $user->wallet += $walletCode->amount;
        $user->save();

        return redirect()->back()->with('success', 'Wallet code redeemed successfully. Your wallet has been updated.');
    }
}
