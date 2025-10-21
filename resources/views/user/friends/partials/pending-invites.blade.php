<!-- Pending invites partial -->
<div class="flex flex-col gap-4">
    <div class="flex flex-col overflow-hidden rounded">
        <h1 class="text-lg font-bold bg-primary text-primary-content p-3">Received Invites</h1>
        <ul class="list bg-base-100 shadow-md">
            @forelse ($receivedRequests as $invite)
                <li class="list-row items-center">
                    <div><img class="size-14 rounded avatar bg-black" src="{{ $invite->sender->profile_image_url }}"
                            alt="{{ $invite->sender->nickname }}" /></div>
                    <div class="font-semibold">{{ $invite->sender->nickname }}</div>
                    <form action="{{ route('user.friends.request.accept', $invite->sender->id) }}" method="POST"
                        class="inline">
                        @csrf
                        <input type="hidden" name="sender_id" value="{{ $invite->sender->id }}" />
                        <button type="submit" class="btn btn-success btn-sm mr-2">
                            Accept
                        </button>
                    </form>
                    <form action="{{ route('user.friends.request.decline', $invite->sender->id) }}" method="POST"
                        class="inline">
                        @csrf
                        <input type="hidden" name="sender_id" value="{{ $invite->sender->id }}" />
                        <button type="submit" class="btn btn-error btn-sm">
                            Ignore
                        </button>
                    </form>
                </li>
            @empty
                <li class="list-row">
                    <div class="w-full text-center text-gray-500">No pending invites</div>
                </li>
            @endforelse
        </ul>
    </div>

    <div class="flex flex-col overflow-hidden rounded">
        <h1 class="text-lg font-bold bg-primary text-primary-content p-3">Sent Invites</h1>
        <ul class="list bg-base-100 shadow-md">
            @forelse ($sentRequests as $invite)
                <li class="list-row items-center">
                    <div><img class="size-14 rounded avatar bg-black" src="{{ $invite->receiver->profile_image_url }}"
                            alt="{{ $invite->receiver->nickname }}" /></div>
                    <div class="font-semibold">{{ $invite->receiver->nickname }}</div>
                    <form action="{{ route('user.friends.request.cancel', $invite->receiver->id) }}" method="POST"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-sm">
                            Cancel
                        </button>
                    </form>
                </li>
            @empty
                <li class="list-row">
                    <div class="w-full text-center text-gray-500">No sent invites</div>
                </li>
            @endforelse
        </ul>
    </div>

</div>
