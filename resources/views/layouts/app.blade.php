<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/cally"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title')</title>
</head>

<body class="flex min-h-screen flex-col bg-base-300">
    <div class="p-4 flex items-center bg-base-100 shadow">
        @include('components.header')
    </div>
    <div class="flex p-4 flex-col max-w-5xl w-full mx-auto">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-4">
                Profile updated successfully.
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning mb-4">
                {{ session('warning') }}
            </div>
        @endif
        <x-inner-header />
        @yield('content')
    </div>
</body>

</html>
