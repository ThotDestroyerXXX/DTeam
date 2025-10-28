@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Forgot Password</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" id="email" class="form-control input" required
                    value="{{ old('email') }}">
            </div>
            <button class="btn btn-primary mt-2" type="submit">Send OTP</button>
        </form>
    </div>
@endsection
<div>
    <!-- We must ship. - Taylor Otwell -->
</div>
