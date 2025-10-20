@props(['errors' => null])

@if ($errors && $errors->any())
    <div class="alert alert-error my-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
