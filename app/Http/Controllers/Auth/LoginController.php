<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $credentials['remember'] ?? false)) {
            // Regenerate session
            session()->regenerate();

            // Get the authenticated user and determine redirect path
            return $this->getRedirectBasedOnUserRole();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Determine redirect path based on user role and status
     */
    protected function getRedirectBasedOnUserRole(): RedirectResponse
    {
        $user = Auth::user();
        $redirectPath = '/'; // Default redirect

        if ($user->role === Role::ADMIN) {
            $redirectPath = route('admin.publishers.index');
        } elseif ($user->role === Role::PUBLISHER) {
            $redirectPath = route('publisher.games.index');
        } elseif ($user->role === Role::USER) {
            $redirectPath = '/';
        }

        return redirect($redirectPath);
    }
}
