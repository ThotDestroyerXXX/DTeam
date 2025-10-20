@props(['actionRoute', 'method' => 'POST', 'buttonText' => 'Save', 'cancelRoute' => null])

<form method="POST" action="{{ $actionRoute }}" enctype="multipart/form-data" novalidate {{ $attributes }}>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    {{ $slot }}

    <div class="mt-6 flex gap-4">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
        @if ($cancelRoute)
            <a href="{{ $cancelRoute }}" class="btn">Cancel</a>
        @endif
    </div>
</form>
