<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\PasswordOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;

class PasswordController extends Controller
{
    // Show the forgot-password email input form
    public function index()
    {
        return view('forgot-password.index');
    }

    // Accept email, generate OTP + token, send email, and redirect to OTP entry page
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            return Redirect::back()->withErrors(['email' => 'No user found with that email address.'])->withInput();
        }

        // generate token and otp
        $token = Str::random(64);
        $otp = random_int(100000, 999999); // 6-digit

        $payload = [
            'email' => $email,
            'otp' => (string) $otp,
            'created_at' => Carbon::now()->toDateTimeString(),
            'verified' => false,
        ];

        // store in cache for 15 minutes
        Cache::put('password_reset_' . $token, $payload, now()->addMinutes(15));

        // send email
        try {
            Mail::to($email)->send(new PasswordOtpMail($otp, $user));
        } catch (\Exception $e) {
            // log but continue
            logger()->error('Failed to send password OTP email: ' . $e->getMessage());
            return Redirect::back()->withErrors(['email' => 'Failed to send email. Please try again later.']);
        }

        return Redirect::route('password.reset', ['token' => $token])->with('info', 'An OTP has been sent to your email.');
    }

    // Show the OTP input or the password reset form depending on cache state
    public function showResetForm($token)
    {
        $key = 'password_reset_' . $token;
        $data = Cache::get($key);

        if (! $data) {
            return redirect()->route('password.index')->withErrors(['token' => 'This password reset link has expired or is invalid.']);
        }

        $verified = isset($data['verified']) && $data['verified'];

        return view('forgot-password.reset')->with([
            'token' => $token,
            'email' => $data['email'],
            'verified' => $verified,
        ]);
    }

    // Handle OTP verification or final password reset depending on inputs
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');
        $key = 'password_reset_' . $token;
        $data = Cache::get($key);

        if (! $data) {
            return Redirect::route('password.index')->withErrors(['token' => 'This password reset link is invalid or expired.']);
        }

        // If OTP provided and no password fields, verify OTP
        if ($request->has('otp') && ! $request->has('password')) {
            $request->validate(['otp' => 'required|string']);
            if (hash_equals((string) $data['otp'], (string) $request->input('otp'))) {
                // mark verified
                $data['verified'] = true;
                Cache::put($key, $data, now()->addMinutes(15));

                return Redirect::route('password.reset', ['token' => $token])->with('success', 'OTP verified. You may now change your password.');
            }

            return Redirect::back()->withErrors(['otp' => 'The OTP you entered is invalid.']);
        }

        // Otherwise expect password fields to perform reset
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        if (! isset($data['verified']) || ! $data['verified']) {
            return Redirect::back()->withErrors(['otp' => 'OTP not verified.']);
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return Redirect::route('password.index')->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // remove cache
        Cache::forget($key);

        return Redirect::route('login')->with('success', 'password updated');
    }
}
