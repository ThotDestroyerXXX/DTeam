@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Password reset</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>Please follow the instructions sent to <strong>{{ $email }}</strong>.</p>

        @if (!$verified)
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="otp">Enter OTP</label>
                    <input type="text" name="otp" id="otp" class="form-control input" required>
                </div>
                <button class="btn btn-primary mt-2" type="submit">Verify OTP</button>
            </form>
        @else
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" class="form-control input" required>
                </div>
                <div class="form-group mt-2">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control input" required>
                </div>
                <button class="btn btn-primary mt-2" type="submit">Change Password</button>
            </form>
        @endif
    </div>
@endsection
