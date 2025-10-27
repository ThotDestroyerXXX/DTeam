<div class="container mx-auto flex flex-col gap-4">
    <h1 class="text-xl font-bold">Avatar</h1>

    <form method="POST" action="{{ route('user.profile.update.avatar') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Main preview image --}}
        <img id="avatar-preview"
            src="{{ $sectionData['user']->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
            alt="{{ $sectionData['user']->name }}'s Avatar" class="size-32 rounded object-cover" />

        <fieldset class="fieldset">
            <legend class="fieldset-legend">Upload your avatar</legend>
            {{-- name the file input so it can be submitted to server later --}}
            <input id="avatar-file" name="profile_picture" type="file" accept="image/*" class="file-input" />
            <label for="avatar-file" class="label">Max size 2MB</label>
        </fieldset>

        {{-- Hidden input to store an item id selected from the user's library (optional) --}}
        <input type="hidden" id="selected_item_id" name="selected_item_id" value="" />

        <h2 class="text-lg font-bold">Your Avatars</h2>
        <div class="grid grid-cols-4 gap-4">
            @forelse ($sectionData['itemLibraries'] as $itemLibrary)
                <button type="button" aria-label="Select {{ $itemLibrary->item->name }}"
                    data-item-id="{{ $itemLibrary->id }}" data-item-src="{{ $itemLibrary->item->image_url }}"
                    class="avatar-thumb rounded overflow-hidden border-0 bg-transparent p-0 cursor-pointer">
                    <img src="{{ $itemLibrary->item->image_url }}" alt="{{ $itemLibrary->item->name }}"
                        class="size-32 rounded" />
                </button>
            @empty
                <p class="col-span-4">No avatars found.</p>
            @endforelse
        </div>

        <button type="submit" class="btn btn-primary self-end  mt-4 min-w-32">Save</button>
    </form>
</div>

@push('scripts')
    <script>
        (function() {
            // Elements
            const preview = document.getElementById('avatar-preview');
            const fileInput = document.getElementById('avatar-file');
            const selectedItemInput = document.getElementById('selected_item_id');
            const thumbs = document.querySelectorAll('.avatar-thumb');

            // Helper to set preview src and optionally selected item id
            function setPreview(src, itemId = '') {
                if (!preview) return;
                preview.src = src;
                if (selectedItemInput) selectedItemInput.value = itemId || '';
            }

            // File upload preview
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;
                    // Only accept images
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        setPreview(ev.target.result, '');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Thumbnail click/keyboard activation
            thumbs.forEach(function(img) {
                const src = img.getAttribute('data-item-src');
                const itemId = img.getAttribute('data-item-id');

                function activate() {
                    // clear file input selection when choosing a library item
                    if (fileInput) fileInput.value = '';
                    setPreview(src, itemId);
                }

                img.addEventListener('click', activate);
                img.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        activate();
                    }
                });
            });
        })();
    </script>
@endpush
