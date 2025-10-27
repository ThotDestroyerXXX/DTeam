@extends('layouts.app')
@section('title')
    Edit Profile
@endsection
@section('content')
    <div class="container">
        <h1 class="text-3xl font-bold mb-4">Edit Profile</h1>
        <ul class="menu menu-vertical lg:menu-horizontal bg-base-100 rounded mb-4">
            <li><a href="{{ route('user.profile.edit.section', ['section' => 'general']) }}"
                    class="tab {{ isset($activeSection) && $activeSection === 'general' ? 'tab-active' : '' }}">General</a>
            </li>
            <li><a href="{{ route('user.profile.edit.section', ['section' => 'avatar']) }}"
                    class="tab {{ isset($activeSection) && $activeSection === 'avatar' ? 'tab-active' : '' }}">Avatar</a>
            </li>
            <li><a href="{{ route('user.profile.edit.section', ['section' => 'background']) }}"
                    class="tab {{ isset($activeSection) && $activeSection === 'background' ? 'tab-active' : '' }}">Background</a>
            </li>
            <li><a href="{{ route('user.profile.edit.section', ['section' => 'change-password']) }}"
                    class="tab {{ isset($activeSection) && $activeSection === 'change-password' ? 'tab-active' : '' }}">Change
                    Password</a></li>
        </ul>

    </div>
    {{-- show partial based on section using ifelse --}}
    @if (isset($activeSection) && $activeSection === 'general')
        @include('user.profile.partials.general')
    @elseif(isset($activeSection) && $activeSection === 'avatar')
        @include('user.profile.partials.avatar')
    @elseif(isset($activeSection) && $activeSection === 'background')
        @include('user.profile.partials.background')
    @elseif(isset($activeSection) && $activeSection === 'change-password')
        @include('user.profile.partials.change-password')
    @else
        @include('user.profile.partials.general')
    @endif
@endsection
