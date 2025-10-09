@extends('layouts.app')

@section('title')
    Edit Profile
@endsection

@section('content')
    <div class="flex flex-col gap-6 mb-6">
        <h1 class="text-3xl font-bold ">Edit Profile</h1>
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="flex justify-between flex-col md:flex-row gap-6">
                <div class="flex flex-col gap-6 items-center">
                    <div class="avatar">
                        <div class="w-24 rounded">
                            <img src={{ asset('storage/default_profile_image.png') }} alt="Profile"
                                class='bg-cover bg-center' />
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm">Change Profile Picture</button>
                </div>
                <div class="flex flex-col gap-6 flex-1">
                    <label class="input w-full" for="nickname">
                        <span class="label">Nickname</span>
                        <input type="text" name='nickname' value="{{ $user->nickname }}" required
                            placeholder="Nickname" />
                    </label>
                    <label class="input w-full" for="real_name">
                        <span class="label">Real Name</span>
                        <input type="text" name='real_name' value="{{ $user->real_name }}" required
                            placeholder="Real Name" />
                    </label>
                    <label class="textarea w-full" for="bio">
                        <span class="label">Bio</span>
                        <textarea name="bio" placeholder="Bio" class="textarea w-full">{{ $user->bio }}</textarea>
                    </label>
                    <label class="select w-full" for="country_id">
                        <span class="label">Country</span>
                        <select name="country_id" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ $user->country_id == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    {{-- check for errors --}}
                    @if ($errors->any())
                        <div role="alert" class="alert alert-error">
                            <ul class="flex flex-col gap-2">
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

                    <button class="btn btn-primary self-end">Save Changes</button>
                </div>
        </form>
    </div>
@endsection
