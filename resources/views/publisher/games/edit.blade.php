@extends('layouts.app')

@section('title')
    Edit Game: {{ $game->title }}
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Edit Game: {{ $game->title }}</h1>
        <form method="POST" action="{{ route('publisher.games.update', $game->id) }}" enctype="multipart/form-data"
            novalidate>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="title">Game Title</legend>
                    <input type="text" class="input w-full" name="title" placeholder="Game title"
                        value="{{ old('title', $game->title) }}" />
                </fieldset>
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="price">Price ($)</legend>
                    <input type="number" name="price" class="input w-full" id="price" step="0.01" min="0"
                        placeholder="0.00" required value="{{ old('price', $game->price) }}" />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="release_date">Release Date</legend>
                    <input type="hidden" name="release_date" id="release_date" required
                        value="{{ old('release_date', $game->release_date) }}" />
                    <button popovertarget="cally-popover1" class="input input-border w-full" id="cally1" type="button"
                        style="anchor-name:--cally1">
                        {{ old('release_date', $game->release_date) }}
                    </button>
                </fieldset>
                <div popover id="cally-popover1" class="dropdown bg-base-100 rounded-box shadow-lg"
                    style="position-anchor:--cally1">
                    <calendar-date class="cally"
                        onchange="document.getElementById('cally1').innerText=this.value; document.getElementById('release_date').value=this.value;">
                        <svg aria-label="Previous" class="fill-current size-4" slot="previous"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                        </svg>
                        <svg aria-label="Next" class="fill-current size-4" slot="next" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                        </svg>
                        <calendar-month></calendar-month>
                    </calendar-date>
                </div>
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="discount">Discount Percentage</legend>
                    <input type="number" name="discount" class="input w-full" id="discount" required
                        value="{{ old('discount', $game->discount_percentage) }}" />
                </fieldset>
            </div>
            <fieldset class="fieldset ">
                <legend class="fieldset-legend" for="brief_description">Brief Description</legend>
                <textarea name="brief_description" id="brief_description" rows="4" class="textarea w-full" required>{{ old('brief_description', $game->brief_description) }}</textarea>
            </fieldset>
            <fieldset class="fieldset ">
                <legend class="fieldset-legend" for="full_description">Full Description</legend>
                <textarea name="full_description" id="full_description" rows="4" class="textarea w-full" required>{{ old('full_description', $game->full_description) }}</textarea>
            </fieldset>
            {{-- divide 2 columns for rating type and genre. rating type is select, and genre is checkbox where user can choose max 3 genres --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend" for="age_rating_id">Rating Type</legend>
                    <select name="age_rating_id" id="age_rating_id" class="select w-full" required>
                        @foreach ($ratingTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('age_rating_id', $game->age_rating_id) == $type->id ? 'selected' : '' }}>
                                <img src="{{ $type->image_url }}" alt="{{ $type->title }}"
                                    class="inline-block w-4 h-4 ml-2" />{{ $type->title }}
                            </option>
                        @endforeach
                    </select>
                </fieldset>
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="genres">Genres (select up to 3)</legend>
                    <div class="grid grid-cols-2 gap-2 ">
                        @php
                            $selectedGenres = old('genres', $game->genres->pluck('id')->toArray());
                        @endphp
                        @foreach ($genres as $genre)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="form-checkbox"
                                    {{ in_array($genre->id, $selectedGenres) ? 'checked' : '' }} />
                                <span class="ml-2 text-sm">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
            {{-- Current Game Images --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Current Images</h3>
                <p class="text-sm text-gray-600 mb-2">Check the "Remove" checkbox to mark images for deletion. Images will
                    be deleted when you submit the form.</p>
                <div class="grid grid-cols-3 gap-4" id="current-images-container">
                    @foreach ($game->gameImages as $image)
                        <div class="relative image-container" data-image-id="{{ $image->id }}">
                            <img src="{{ $image->image_url }}" alt="Game screenshot" class="w-full h-auto rounded-md">
                            <div class="absolute top-2 right-2">
                                <input type="checkbox" id="delete_image_{{ $image->id }}" name="delete_images[]"
                                    value="{{ $image->id }}" class="checkbox checkbox-error delete-image-checkbox"
                                    onchange="toggleImageDeletion(this)" />
                                <label for="delete_image_{{ $image->id }}"
                                    class="text-xs text-white bg-red-500 px-1 rounded">Remove</label>
                            </div>
                            <div
                                class="absolute inset-0 bg-red-500 bg-opacity-30 deletion-overlay hidden items-center justify-center">
                                <span class="text-white font-bold">Marked for deletion</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="file-input file-input-primary w-full" />
            </div>
            {{-- show images preview --}}
            <div id="image-preview" class="grid grid-cols-3 gap-4 my-4"></div>
            <script>
                // Handle new image uploads preview
                document.getElementById('images').addEventListener('change', function(event) {
                    const preview = document.getElementById('image-preview');
                    preview.innerHTML = '';
                    const files = event.target.files;
                    Array.from(files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('w-full', 'h-auto', 'object-cover', 'rounded-md');
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });

                // Handle toggle of image deletion preview
                function toggleImageDeletion(checkbox) {
                    const container = checkbox.closest('.image-container');
                    const overlay = container.querySelector('.deletion-overlay');

                    if (checkbox.checked) {
                        // If checked, show the deletion overlay
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    } else {
                        // If unchecked, hide the deletion overlay
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                    }
                }
            </script>
            @if ($errors->any())
                <div class="alert alert-error my-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex gap-4 mt-4">
                <button type="submit" class="btn btn-primary">Update Game</button>
                <a href="{{ route('publisher.games.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </div>
@endsection
