<?php

namespace App\Http\Controllers;

use App\Enums\WalletCodeAmount;
use App\Http\Requests\WalletCodeRequest;
use App\Models\WalletCode;
use Illuminate\Http\Request;

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
}
