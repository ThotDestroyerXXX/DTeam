@extends('layouts.app')

@section('title')
    Edit Game: {{ $game->title }}
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Edit Game: {{ $game->title }}</h1>

        <x-publisher.form actionRoute="{{ route('publisher.games.update', $game->id) }}" method="PUT"
            buttonText="Update Game" cancelRoute="{{ route('publisher.games.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-input label="Game Title" name="title" placeholder="Game title" required
                    value="{{ old('title', $game->title) }}" />

                <x-publisher.form-input label="Price ($)" name="price" type="number" step="0.01" min="0"
                    placeholder="0.00" required value="{{ old('price', $game->price) }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-date-picker label="Release Date" name="release_date" required
                    value="{{ old('release_date', $game->release_date) }}" />

                <x-publisher.form-input label="Discount Percentage" name="discount" type="number" min="0"
                    max="100" required value="{{ old('discount', $game->discount_percentage) }}" />
            </div>

            <x-publisher.form-textarea label="Brief Description" name="brief_description" rows="4" required
                value="{{ old('brief_description', $game->brief_description) }}" />

            <x-publisher.form-textarea label="Full Description" name="full_description" rows="6" required
                value="{{ old('full_description', $game->full_description) }}" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-select label="Rating Type" name="age_rating_id" :options="$ratingTypes" :selected="old('age_rating_id', $game->age_rating_id)"
                    required />

                @php
                    $selectedGenres = old('genres', $game->genres->pluck('id')->toArray());
                @endphp

                <x-publisher.form-checkbox-group label="Genres" name="genres" :options="$genres" :selected="$selectedGenres"
                    maxSelections="3" />
            </div>

            <x-publisher.existing-images :game="$game" />

            <div class="mt-4">
                <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="file-input file-input-primary w-full" />
            </div>

            <x-publisher.image-preview />
            <x-validation-errors :errors="$errors" />
        </x-publisher.form>
    </div>
@endsection
