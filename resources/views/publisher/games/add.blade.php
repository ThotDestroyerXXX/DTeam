@extends('layouts.app')

@section('title')
    Add New Game
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add New Game</h1>

        <x-publisher.form actionRoute="{{ route('publisher.games.store') }}" buttonText="Add Game"
            cancelRoute="{{ route('publisher.games.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-input label="Game Title" name="title" placeholder="Game title" required />

                <x-publisher.form-input label="Price ($)" name="price" type="number" step="0.01" min="0"
                    placeholder="0.00" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-date-picker label="Release Date" name="release_date" required />

                <x-publisher.form-input label="Discount Percentage" name="discount" type="number" min="0"
                    max="100" required />
            </div>

            <x-publisher.form-textarea label="Brief Description" name="brief_description" rows="4" required />

            <x-publisher.form-textarea label="Full Description" name="full_description" rows="6" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-publisher.form-select label="Rating Type" name="age_rating_id" :options="$ratingTypes" required />

                <x-publisher.form-checkbox-group label="Genres" name="genres" :options="$genres" maxSelections="3" />
            </div>

            <div class="mt-4">
                <label for="images" class="block text-sm font-medium text-gray-700">Game Images</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="file-input file-input-primary w-full" />
            </div>

            <x-publisher.image-preview />
            <x-validation-errors :errors="$errors" />
        </x-publisher.form>
    </div>
@endsection
