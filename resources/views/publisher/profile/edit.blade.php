@extends('layouts.app')

@section('title')
    Edit Profile
@endsection


@section('content')
    <div>
        <h1>Edit Profile</h1>
        <form method="POST" class="mt-4 max-w-md" action="{{ route('publisher.profile.update') }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-control ">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="name">Publisher Name</legend>
                    <input type="text" class="input w-full" name="name" placeholder="Publisher name"
                        value="{{ old('name', $publisher->name) }}" required />
                </fieldset>
            </div>
            <div class="form-control">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="website">Website</legend>
                    <input type="url" class="input w-full" name="website" placeholder="Website"
                        value="{{ old('website', $publisher->website) }}" required />
                </fieldset>
            </div>
            <div class="form-control">
                <fieldset class="fieldset ">
                    <legend class="fieldset-legend" for="image">Profile Image</legend>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            @if ($publisher->image_url)
                                <img id="current-profile-image" src="{{ $publisher->image_url }}" alt="Current Profile"
                                    class="h-20 w-20 object-cover rounded-full border-2 border-primary">
                            @else
                                <div id="current-profile-image"
                                    class="h-20 w-20 bg-gray-200 rounded-full flex items-center justify-center border-2 border-primary">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                            @endif
                            <div id="image-preview"
                                class="h-20 w-20 object-cover rounded-full border-2 border-primary absolute inset-0 hidden">
                            </div>
                        </div>
                        <div class="flex-grow">
                            <input type="file" class="file-input file-input-primary w-full" name="image"
                                id="profile-image-input" accept="image/*" />
                            <p class="text-xs text-gray-500 mt-1">Upload a new profile image</p>

                            {{-- Hidden remove_image checkbox that gets automatically checked when a new image is selected --}}
                            <input type="checkbox" name="remove_image" id="remove-image-checkbox" class="hidden"
                                value="1" />
                        </div>
                    </div>
                </fieldset>
            </div>

            <script>
                // Add image preview functionality
                document.getElementById('profile-image-input').addEventListener('change', function(event) {
                    const preview = document.getElementById('image-preview');
                    const currentImage = document.getElementById('current-profile-image');
                    const removeCheckbox = document.getElementById('remove-image-checkbox');

                    if (this.files && this.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            // Update preview style
                            preview.style.backgroundImage = `url(${e.target.result})`;
                            preview.style.backgroundSize = 'cover';
                            preview.style.backgroundPosition = 'center';

                            // Show preview, hide current image
                            preview.classList.remove('hidden');
                            currentImage.classList.add('opacity-0');

                            // Automatically check the remove_image checkbox when a new image is selected
                            removeCheckbox.checked = true;
                        };

                        reader.readAsDataURL(this.files[0]);
                    } else {
                        // Hide preview, show current image
                        preview.classList.add('hidden');
                        currentImage.classList.remove('opacity-0');

                        // Uncheck the remove_image checkbox if no file is selected
                        removeCheckbox.checked = false;
                    }
                });
            </script>
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" class="btn btn-primary mt-4">Update Profile</button>
        </form>
    </div>
@endsection
