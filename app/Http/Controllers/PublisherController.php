<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $query = Publisher::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $publishers = $query->paginate(10)->withQueryString();

        return view('admin.publishers.index', [
            'publishers' => $publishers
        ]);
    }

    public function add()
    {
        return view('admin.publishers.add');
    }

    public function editProfile()
    {
        return view('publisher.profile.edit');
    }

    public function detail(Publisher $publisher)
    {

        // return the publisher detail, combined with the games that the publisher has published

        $games = $publisher->games()->paginate(10);

        return view('publisher.detail', [
            'publisher' => $publisher,
            'games' => $games
        ]);
    }
}
