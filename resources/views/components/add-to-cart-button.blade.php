@if (!$isInCart)
    <form method="POST" action="{{ route('user.cart.add', $game->id) }}">
        @csrf
        <button class="btn btn-success btn-sm">Add to
            Cart</button>
    </form>
@else
    <button class="btn btn-success btn-sm"><a href="{{ route('user.cart.index') }}">In
            Cart</a></button>
@endif
