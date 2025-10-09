<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletCodeController extends Controller
{
    public function index()
    {
        return view('admin.wallet-codes.index');
    }
}
