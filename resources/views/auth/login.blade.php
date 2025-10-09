@extends('layouts.app')

@section('title')
    Login
@endsection

@section('content')
    <div class="card bg-base-100 w-96 shadow-sm mx-auto my-10">
        <div class="card-body">
            <h2 class="card-title">Login</h2>
            <p>Please enter your credentials to login.</p>

            <form novalidate class="w-full flex flex-col gap-4 " method="POST" action="{{ route('login.authenticate') }}">
                @csrf
                <div class="input w-full">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                            stroke="currentColor">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </g>
                    </svg>
                    <input type="email" id="email" name='email' required placeholder="Email" />
                </div>
                <div class="input w-full">
                    <svg class="h-[1em] opacity-50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17 10V8C17 5.23858 14.7614 3 12 3C9.23858 3 7 5.23858 7 8V10M12 14.5V16.5M8.8 21H15.2C16.8802 21 17.7202 21 18.362 20.673C18.9265 20.3854 19.3854 19.9265 19.673 19.362C20 18.7202 20 17.8802 20 16.2V14.8C20 13.1198 20 12.2798 19.673 11.638C19.3854 11.0735 18.9265 10.6146 18.362 10.327C17.7202 10 16.8802 10 15.2 10H8.8C7.11984 10 6.27976 10 5.63803 10.327C5.07354 10.6146 4.6146 11.0735 4.32698 11.638C4 12.2798 4 13.1198 4 14.8V16.2C4 17.8802 4 18.7202 4.32698 19.362C4.6146 19.9265 5.07354 20.3854 5.63803 20.673C6.27976 21 7.11984 21 8.8 21Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <input type="password" id="password" name='password' required placeholder="Password" />
                </div>
                <label class="label cursor-pointer">
                    <input type="checkbox" name="remember" class="checkbox checkbox-sm" />
                    <span class="label-text">Remember me</span>

                </label>
                {{-- If there is any errors --}}
                @if ($errors->any())
                    <div role="alert" class="alert alert-error alert-outline">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class='inline-flex items-center gap-2'><svg class='h-[1em]' viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 8V12M12 16H12.01M2 8.52274V15.4773C2 15.7218 2 15.8441 2.02763 15.9592C2.05213 16.0613 2.09253 16.1588 2.14736 16.2483C2.2092 16.3492 2.29568 16.4357 2.46863 16.6086L7.39137 21.5314C7.56432 21.7043 7.6508 21.7908 7.75172 21.8526C7.84119 21.9075 7.93873 21.9479 8.04077 21.9724C8.15586 22 8.27815 22 8.52274 22H15.4773C15.7218 22 15.8441 22 15.9592 21.9724C16.0613 21.9479 16.1588 21.9075 16.2483 21.8526C16.3492 21.7908 16.4357 21.7043 16.6086 21.5314L21.5314 16.6086C21.7043 16.4357 21.7908 16.3492 21.8526 16.2483C21.9075 16.1588 21.9479 16.0613 21.9724 15.9592C22 15.8441 22 15.7218 22 15.4773V8.52274C22 8.27815 22 8.15586 21.9724 8.04077C21.9479 7.93873 21.9075 7.84119 21.8526 7.75172C21.7908 7.6508 21.7043 7.56432 21.5314 7.39137L16.6086 2.46863C16.4357 2.29568 16.3492 2.2092 16.2483 2.14736C16.1588 2.09253 16.0613 2.05213 15.9592 2.02763C15.8441 2 15.7218 2 15.4773 2H8.52274C8.27815 2 8.15586 2 8.04077 2.02763C7.93873 2.05213 7.84119 2.09253 7.75172 2.14736C7.6508 2.2092 7.56432 2.29568 7.39137 2.46863L2.46863 7.39137C2.29568 7.56432 2.2092 7.6508 2.14736 7.75172C2.09253 7.84119 2.05213 7.93873 2.02763 8.04077C2 8.15586 2 8.27815 2 8.52274Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-actions w-full">
                    <button class="btn btn-primary btn-block" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection
