<div class="container mx-auto flex flex-col gap-6">
    <div class="flex flex-col gap-1">
        <h1 class="text-xl font-bold">Profile Background</h1>
        <span class='text-sm text-gray-500'>Choose a background to show on your profile page</span>
    </div>
    <img id="background-preview"
        src="{{ $sectionData['user']->background_url ?? asset('storage/default_background_image.png') }}" alt="background"
        class="rounded object-cover object-center" />

    <div class='flex flex-col gap-2'>
        <h2 class="text-xl font-bold">Your Profile Backgrounds</h2>
        <div class='grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-4 items-center'>
            @forelse ($sectionData['backgroundItems'] as $background)
                <button type="button" aria-label="Select background {{ $background->id }}"
                    data-bg-id="{{ $background->id }}" data-bg-src="{{ $background->image_url }}"
                    class="bg-thumb border-0 bg-transparent p-0 rounded overflow-hidden cursor-pointer">
                    <img src="{{ $background->image_url }}" alt="background"
                        class="h-auto w-auto object-cover rounded" />
                </button>
            @empty
                <p class='text-sm'>No backgrounds found.</p>
            @endforelse
        </div>
    </div>
    <form method="POST" action="{{ route('user.profile.update.background') }}">
        @csrf
        @method('PUT')
        {{-- Hidden input to store selected background id for form submission --}}
        <input type="hidden" id="selected_background_id" name="selected_background_id" value="" />

        <button type="submit" class='self-end btn btn-primary min-w-40'>Save</button>
    </form>
</div>

@push('scripts')
    <script>
        (function() {
            const preview = document.getElementById('background-preview');
            const thumbs = document.querySelectorAll('.bg-thumb');
            const selectedInput = document.getElementById('selected_background_id');

            function setPreview(src, id = '') {
                if (!preview) return;
                preview.src = src;
                if (selectedInput) selectedInput.value = id || '';
            }

            thumbs.forEach(function(btn) {
                const src = btn.getAttribute('data-bg-src');
                const id = btn.getAttribute('data-bg-id');

                function activate() {
                    setPreview(src, id);
                }

                btn.addEventListener('click', activate);
                btn.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        activate();
                    }
                });
            });
        })();
    </script>
@endpush
