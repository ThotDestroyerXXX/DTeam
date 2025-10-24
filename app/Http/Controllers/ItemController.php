<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('user.point-shop.index', [
            'avatars' => $items->where('type', 'avatar'),
            'backgrounds' => $items->where('type', 'background'),
        ]);
    }

    public function purchase(Item $item)
    {
        $user = Auth::user();

        if ($user->point < $item->price) {
            return redirect()->back()->with('error', 'Insufficient points to purchase this item.');
        }

        if ($user->items()->where('items.id', $item->id)->exists()) {
            return redirect()->back()->with('error', 'You already own this item.');
        }

        // Deduct points and attach item to user
        $user->point -= $item->price;
        $user->save();
        ItemLibrary::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        return redirect()->back()->with('success', 'Item purchased successfully!');
    }
}
