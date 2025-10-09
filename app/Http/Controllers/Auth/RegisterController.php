<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'terms' => ['accepted'],
        ]);

        // Create the user with a 10-digit unique code
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'unique_code' => $this->generateUniqueCode(),
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to a desired location
        return redirect()->route('user.profile.edit');
    }

    /**
     * Generate a unique 10-digit code for the user
     *
     * @return string
     */
    private function generateUniqueCode(): string
    {
        // Keep generating a random code until we find one that isn't already in use
        do {
            // Generate a random 10-digit number
            $code = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (User::where('unique_code', $code)->exists());

        return $code;
    }
}
