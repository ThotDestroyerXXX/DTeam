@props(['game', 'mainMedia' => null])

<div class="w-full min-w-2/3 flex flex-col">
    <div id="main-media-container" class="w-full h-auto rounded flex-shrink-0">
        @if ($game->trailer_url)
            <video id="main-video" class="size-full object-cover rounded" controls onloadstart="this.volume=0.4">
                <source src="{{ $game->trailer_url }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            <img id="main-image" src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }} Cover"
                class="w-full h-auto rounded">
        @endif
    </div>

    {{--  display all images and trailer --}}
    <div class="flex flex-row gap-2 overflow-x-auto mt-2">
        @if ($game->trailer_url)
            <div class="h-20 w-auto rounded flex-shrink-0 relative cursor-pointer media-thumbnail" data-type="video"
                data-src="{{ $game->trailer_url }}">
                <video class="size-full object-cover rounded">
                    <source src="{{ $game->trailer_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                {{-- play button with white background color icon svg --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-white bg-black bg-opacity-50 rounded-full p-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.752 11.168l-6.518-3.751A1 1 0 007 8.308v7.384a1 1 0 001.234.97l6.518-3.752a1 1 0 000-1.732z" />
                    </svg>
                </div>
            </div>
        @endif
        @foreach ($game->gameImages as $image)
            <img src="{{ $image->image_url }}" alt="{{ $game->title }}"
                class="h-20 w-auto rounded flex-shrink-0 cursor-pointer media-thumbnail" data-type="image"
                data-src="{{ $image->image_url }}">
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeMediaGallery();
        });

        function initializeMediaGallery() {
            // Get all media thumbnails
            const mediaThumbnails = document.querySelectorAll('.media-thumbnail');
            const mainContainer = document.getElementById('main-media-container');

            // Add click event listener to each thumbnail
            mediaThumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const mediaType = this.getAttribute('data-type');
                    const mediaSrc = this.getAttribute('data-src');

                    // Clear the main container
                    mainContainer.innerHTML = '';

                    if (mediaType === 'video') {
                        // Create video element
                        const video = document.createElement('video');
                        video.id = 'main-video';
                        video.className = 'size-full object-cover rounded';
                        video.controls = true;
                        video.setAttribute('onloadstart', 'this.volume=0.4');

                        const source = document.createElement('source');
                        source.src = mediaSrc;
                        source.type = 'video/mp4';

                        video.appendChild(source);
                        video.appendChild(document.createTextNode(
                            'Your browser does not support the video tag.'));

                        mainContainer.appendChild(video);
                        video.play();
                    } else {
                        // Create image element
                        const img = document.createElement('img');
                        img.id = 'main-image';
                        img.src = mediaSrc;
                        img.alt = "{{ $game->title }} Cover";
                        img.className = 'w-full h-auto rounded';

                        mainContainer.appendChild(img);
                    }
                });
            });
        }
    </script>
@endpush
