<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index()
    {
        return view('admin.publishers.index', [
            'publishers' => Publisher::paginate(10)
        ]);
    }

    public function editProfile()
    {
        return view('publisher.profile.edit');
    }
}
