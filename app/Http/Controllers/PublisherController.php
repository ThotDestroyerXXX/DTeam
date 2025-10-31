<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\PublisherCreatedMail;
use Illuminate\Support\Facades\Redirect;
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

    private function generateUniqueCode(): string
    {
        // Keep generating a random code until we find one that isn't already in use
        do {
            // Generate a random 10-digit number
            $code = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (User::where('unique_code', $code)->exists());

        return $code;
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');

        // if user already exists, return with error
        if (User::where('email', $email)->exists()) {
            return Redirect::back()->with('error', 'A user with this email already exists.');
        }

        // generate random password
        $password = Str::random(10);

        // create user with publisher role
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'role' => Role::PUBLISHER,
            'unique_code' => $this->generateUniqueCode(),
        ]);

        // create publisher record associated to user (minimal)
        Publisher::create([
            'user_id' => $user->id,
        ]);

        // send email with credentials
        try {
            Mail::to($email)->send(new PublisherCreatedMail($user, $password));
        } catch (\Exception $e) {
            logger()->error('Failed to send publisher created email: ' . $e->getMessage());
            // continue — user was created
        }

        return Redirect::route('admin.publishers.index')->with('success', 'Publisher created and an email has been sent.');
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
