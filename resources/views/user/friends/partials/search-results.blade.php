<!-- Friend Search Results -->
<div class="bg-base-100 p-4 mt-4 rounded">
    <h2 class="text-lg font-semibold mb-3">Search Results</h2>

    @if (empty($searchValue))
        <p>Enter a search term to find friends</p>
    @elseif($results->isEmpty())
        <p>No users found matching "{{ $searchValue }}" {{ $searchType === 'code' ? 'code' : 'nickname' }}.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($results as $result)
                <div class="flex items-center justify-between p-3 bg-base-200 rounded">
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <a href="{{ route('user.profile.index', $result->id) }}"
                                class="avatar rounded bg-black size-14">
                                @if ($result->profile_picture_url)
                                    <img src="{{ $result->profile_picture_url ?? asset('storage/default_profile_image.png') }}"
                                        alt="{{ $result->nickname }}'s avatar" />
                                @else
                                    <div
                                        class="bg-primary text-primary-content flex items-center justify-center h-full">
                                        {{ substr($result->nickname, 0, 1) }}
                                    </div>
                                @endif
                            </a>
                        </div>
                        <div class='text-sm'>
                            <a href="{{ route('user.profile.index', $result->id) }}"
                                class="font-medium">{{ $result->nickname }}</a>
                            @if (isset($result->friendship_status) && $result->friendship_status === 'friend')
                                <div>
                                    You're already friends with this user
                                </div>
                            @elseif(isset($result->friendship_status) && $result->friendship_status === 'sent_request')
                                <div>
                                    You sent a friend request to this user
                                </div>
                            @endif
                        </div>
                    </div>
                    @if (!isset($result->friendship_status) || $result->friendship_status === 'none')
                        <form action="{{ route('user.friends.request.send') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $result->id }}" />
                            <button type="submit" class="btn btn-sm btn-primary">Send Request</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
