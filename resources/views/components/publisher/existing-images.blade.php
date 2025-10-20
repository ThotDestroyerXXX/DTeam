@props(['game'])

<div class="mb-6">
    <h3 class="text-lg font-semibold mb-2">Current Images</h3>
    <p class="text-sm text-gray-600 mb-2">Check the "Remove" checkbox to mark images for deletion. Images will be deleted
        when you submit the form.</p>
    <div class="grid grid-cols-3 gap-4" id="current-images-container">
        @foreach ($game->gameImages as $image)
            <div class="relative image-container" data-image-id="{{ $image->id }}">
                <img src="{{ $image->image_url }}" alt="Game screenshot" class="w-full h-auto rounded-md">
                <div class="absolute top-2 right-2">
                    <input type="checkbox" id="delete_image_{{ $image->id }}" name="delete_images[]"
                        value="{{ $image->id }}" class="checkbox checkbox-error delete-image-checkbox"
                        onchange="toggleImageDeletion(this)" />
                    <label for="delete_image_{{ $image->id }}" class="text-xs text-white bg-red-500 px-1 rounded">
                        Remove
                    </label>
                </div>
                <div
                    class="absolute inset-0 bg-red-500 bg-opacity-30 deletion-overlay hidden items-center justify-center">
                    <span class="text-white font-bold">Marked for deletion</span>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
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
@endpush
