<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check user profile completion if there is user, next if there is no user (guest)
        if (Auth::check() && Auth::user()->role === Role::PUBLISHER) {
            $profile = Auth::user()->publisher;
            if ($profile && !$profile->isProfileComplete()) {
                return redirect()->route('publisher.profile.edit')->with('warning', 'Please complete your profile before proceeding.');
            }
        } elseif (Auth::check() && !Auth::user()->isProfileComplete()) {
            return redirect()->route('user.profile.setup')->with('warning', 'Please complete your profile before proceeding.');
        } elseif (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('warning', 'Please verify your email address before proceeding.');
        }
        return $next($request);
    }
}
