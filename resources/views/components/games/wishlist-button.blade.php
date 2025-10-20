@props(['game', 'isGameInWishlist'])

<div>
    @if ($isGameInWishlist)
        <form action="{{ route('user.wishlist.remove', $game->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-primary" type="submit">Remove from Your Wishlist</button>
        </form>
    @else
        <form action="{{ route('user.wishlist.add', $game->id) }}" method="POST">
            @csrf
            <button class="btn btn-primary" type="submit">Add to Your Wishlist</button>
        </form>
    @endif
</div>
