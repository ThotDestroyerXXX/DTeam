@extends('layouts.app')

@section('title')
    Add New Game
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add New Game</h1>
        <form method="POST" action="{{ route('publisher.games.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="title">Game Title</legend>
                    <input type="text" class="input w-full" name="title" placeholder="Game title" />
                </fieldset>
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="price">Price ($)</legend>
                    <input type="number" name="price" class="input w-full" id="price" step="0.01" min="0"
                        placeholder="0.00" required />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="release_date">Release Date</legend>
                    <input type="hidden" name="release_date" id="release_date" required />
                    <button popovertarget="cally-popover1" class="input input-border w-full" id="cally1" type="button"
                        style="anchor-name:--cally1">
                        Pick a date
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
                    <input type="number" name="discount" class="input w-full" id="discount" required />
                </fieldset>
            </div>
            <fieldset class="fieldset ">
                <legend class="fieldset-legend" for="brief_description">Brief Description</legend>
                <textarea name="brief_description" id="brief_description" rows="4" class="textarea w-full" required></textarea>
            </fieldset>
            <fieldset class="fieldset ">
                <legend class="fieldset-legend" for="full_description">Full Description</legend>
                <textarea name="full_description" id="full_description" rows="4" class="textarea w-full" required></textarea>
            </fieldset>
            {{-- divide 2 columns for rating type and genre. rating type is select, and genre is checkbox where user can choose max 3 genres --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend" for="age_rating_id">Rating Type</legend>
                    <select name="age_rating_id" id="age_rating_id" class="select w-full" required>
                        @foreach ($ratingTypes as $index => $type)
                            <option value="{{ $type->id }}" selected={{ $index === 1 }}><img
                                    src="{{ $type->image_url }}" alt="{{ $type->title }}"
                                    class="inline-block w-4 h-4 ml-2" />{{ $type->title }}</option>
                        @endforeach
                    </select>
                </fieldset>
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="genres">Genres (select up to 3)</legend>
                    <div class="grid grid-cols-2 gap-2 ">
                        @foreach ($genres as $genre)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="form-checkbox" />
                                <span class="ml-2 text-sm">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Game Images</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="file-input file-input-primary w-full" />
            </div>
            {{-- show images preview --}}
            <div id="image-preview" class="grid grid-cols-3 gap-4 my-4"></div>
            <script>
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
            <div>
                <button type="submit" class="btn btn-primary">Add
                    Game</button>
            </div>
        </form>
    </div>
@endsection
