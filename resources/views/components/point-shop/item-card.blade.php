<button onclick="document.getElementById('my_modal_{{ $index }}_{{ $type }}').showModal()"
    class="card w-44 bg-base-100 shadow-md text-sm rounded overflow-hidden cursor-pointer">
    <figure class="w-full aspect-square bg-base-200">
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-auto object-contain">
    </figure>
    <div class="card-body m-0 p-3 justify-between">
        <h2 class="font-semibold">{{ $item->name }}</h2>
        <div class="card-actions text-xs justify-between items-center ">
            @if (Auth::user()->items()->where('item_id', $item->id)->exists())
                {{-- checkbox rounded icon svg --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 bg-green-500 rounded-full text-white"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            @endif
            <span>{{ $item->price }}</span>
        </div>
    </div>
</button>
