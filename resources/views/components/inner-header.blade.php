@can('is-user')
    <div class="flex flex-row gap-2 mb-2 justify-end">
        <a href="{{ route('user.wishlist.index') }}" class='btn btn-primary btn-sm rounded-box'>Wishlist</a>
        <a href="" class="btn btn-secondary btn-sm rounded-box btn-outline">Cart</a>
    </div>
@endcan
<div
    class="w-full mb-4 navbar !min-h-0 justify-between items-center bg-base-100 shadow-sm rounded-box border border-base-content/5">

    {{-- dropdown of game genres --}}
    <div class="flex-none items-center">
        <ul class="menu menu-horizontal items-center gap-2 p-0">
            <li><a href="{{ route('store.index') }}">Store</a></li>
            <li>
                <details>
                    <summary>Categories</summary>
                    <ul class="bg-base-100 rounded-t-none p-2 z-10">
                        @foreach ($categories as $category)
                            <li><a
                                    href="{{ route('store.index', ['genre' => $category->id]) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </li>
            @can('is-user')
                <li><a href="{{ route('store.index', ['points' => true]) }}">Points Shop</a></li>
            @endcan
        </ul>
    </div>
    <div>
        <input type="text" placeholder="Search" class="input input-bordered w-24 md:w-auto" />
    </div>
</div>
