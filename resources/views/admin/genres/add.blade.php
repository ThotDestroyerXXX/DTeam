@extends('layouts.app')

@section('title')
    Genres
@endsection

@section('content')
    <div>
        <h1>Add New Genre</h1>
        <form action="{{ route('admin.genres.store') }}" method="POST" class="mt-4 max-w-md">
            @csrf
            <div class="form-control mb-4">
                <label class="input">
                    <span class="label">Genre Name</span>
                    <input type="text" name="name" required />
                </label>
            </div>
            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" class="btn btn-primary">Create Genre</button>
        </form>
    </div>
@endsection
