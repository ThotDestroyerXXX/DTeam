@extends('layouts.app')

@section('title')
    {{ $game->title }} - Game Details
@endsection

@section('content')
    @if (session('success_add_to_cart'))
        <dialog id="my_modal_1" class="modal" open>
            <div class="modal-box flex flex-col gap-2 ">
                <h3 class="text-lg font-bold">Added to your cart!</h3>
                <div class="bg-base-100 gap-2 w-full flex flex-row items-center">
                    <img src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }}"
                        class="h-24 w-auto rounded" />
                    <div class="flex flex-col justify-between w-full">
                        <p class="font-medium">{{ $game->title }}</p>
                        <div class="flex flex-row justify-between w-full">
                            <p class="text-sm self-end">for my account</p>
                            <div class="flex flex-col self-end">
                                @if ($game->discount_percentage > 0)
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="badge badge-success rounded-sm font-semibold h-full px-1">{{ $game->discount_percentage }}%</span>
                                        <div class="flex flex-col">
                                            <span class="text-xs line-through">${{ number_format($game->price, 2) }}</span>
                                            <span
                                                class="text-sm font-bold">${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-bold">${{ number_format($game->price, 2) }}</span>
                                @endif
                                <form action="{{ route('user.cart.remove', $game) }}" method="POST" class="self-end">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link btn-sm p-0">remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-action w-full ">
                    <form method="dialog" class="gap-2 w-full grid grid-cols-2 grid-rows-1">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn btn-secondary">Continue Shopping</button>
                        <a href="{{ route('user.cart.index') }}" class="btn btn-primary">View My
                            Cart</a>
                    </form>
                </div>
            </div>
        </dialog>
    @endif
    <div class="flex flex-col gap-4">
        <h1 class="text-3xl font-bold ">{{ $game->title }}</h1>
        <div class="bg-base-100 flex flex-row gap-3 p-3 rounded-lg">
            <div class="w-full min-w-2/3 flex flex-col">
                <div id="main-media-container" class="w-full h-auto rounded flex-shrink-0">
                    @if ($game->trailer_url)
                        <video id="main-video" class="size-full object-cover rounded" controls
                            onloadstart="this.volume=0.4">
                            <source src="{{ $game->trailer_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img id="main-image" src="{{ $game->gameImages->first()->image_url }}"
                            alt="{{ $game->title }} Cover" class="w-full h-auto rounded">
                    @endif
                </div>

                {{--  display all images and trailer --}}
                <div class="flex flex-row gap-2 overflow-x-auto mt-2">
                    @if ($game->trailer_url)
                        <div class="h-20 w-auto rounded flex-shrink-0 relative cursor-pointer media-thumbnail"
                            data-type="video" data-src="{{ $game->trailer_url }}">
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
            <div class="w-full max-w-1/3 gap-2 flex flex-col">
                <img src="{{ $game->gameImages->first()->image_url }}" alt="{{ $game->title }} Cover"
                    class="w-full h-auto rounded">
                <p class="text-sm text-justify">{{ $game->brief_description }}</p>

                <div class='text-sm'>
                    <div class="flex items-center gap-2">
                        <h3 class="font-medium">Recent Reviews:</h3>
                        <span class="text-blue-500">{{ $reviewStatus }}</span>
                        <span>({{ $recentReviewsCount }} reviews)</span>
                    </div>

                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="font-medium">All Reviews:</h3>
                        <span class="text-blue-500">{{ $allReviewStatus }}</span>
                        <span>({{ $allReviewsCount }} reviews)</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="font-medium">Release Date: </h3>
                        <span>{{ $game->release_date }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="font-medium">Publisher: </h3>
                        <a href="">{{ $game->publisher->name }}</a>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="font-medium">Genres:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($game->genres as $genre)
                                <span class="badge badge-primary badge-sm">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($game->isInUserLibrary(Auth::id()))
            <div class="flex flex-col">
                <div class="bg-primary p-4 items-center relative flex min-h-16">
                    <div
                        class="absolute -left-4 top-1/2  -translate-y-1/2 flex items-center gap-1 bg-success shadow-xl px-2 py-1 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p>in library</p>
                    </div>
                    <p class="text-sm ml-16 font-semibold text-primary-content">{{ $game->title }} is already in your
                        library
                    </p>
                </div>
                {{-- display user review if user already reviewed the game --}}
                @if ($userReview)
                    <div class="bg-base-100 p-4 flex flex-col gap-2">
                        <h2 class="font-semibold">You reviewed this game on
                            {{ $userReview->created_at->format('F j, Y') }}</h2>
                        <div class="bg-base-200">
                            <img src="{{ $userReview->ratingType->image_url }}"
                                alt="{{ $userReview->ratingType->title }} Logo" class="h-10 w-auto inline-block mr-2" />
                            <span class="font-medium text-sm">{{ $userReview->ratingType->title }}</span>
                        </div>
                        <p class="text-sm">{{ $userReview->content }}</p>
                    </div>
                @endif
            </div>
        @else
            <div>
                <button class="btn btn-primary">Add to Your Wishlist</button>
            </div>

            <div class="flex flex-row bg-base-100 p-6 rounded-lg items-center relative">
                <h2 class="font-semibold">Buy {{ $game->title }}</h2>
                <div
                    class="absolute right-4 -bottom-4 flex flex-row gap-2 bg-primary text-primary-content p-1 shadow-lg items-center rounded-none">
                    <div class="flex items-center gap-2">
                        @if ($game->discount_percentage > 0)
                            <span
                                class="badge badge-success rounded-sm font-semibold h-full px-1">{{ $game->discount_percentage }}%</span>
                            <div>
                                <span class="text-xs line-through">${{ number_format($game->price, 2) }}</span>
                                <span
                                    class="text-sm font-bold">${{ number_format($game->price * (1 - $game->discount_percentage / 100), 2) }}</span>
                            </div>
                        @else
                            <span class="text-sm font-bold">${{ number_format($game->price, 2) }}</span>
                        @endif
                    </div>

                    @can('is-user')
                        <div>|</div>
                        @if (!$game->isInUserCart(Auth::id()))
                            <form method="POST" action="{{ route('user.cart.add', $game) }}">
                                @csrf
                                <button class="btn btn-success btn-sm">Add to
                                    Cart</button>
                            </form>
                        @else
                            <button class="btn btn-success btn-sm"><a href="{{ route('user.cart.index') }}">In
                                    Cart</a></button>
                        @endif
                    @endcan
                    @guest
                        <div>|</div>
                        <a href="{{ route('login') }}"> <button class="btn btn-success btn-sm">Add to
                                Cart</button></a>
                    @endguest
                </div>

            </div>
        @endif

        <div class="flex flex-row w-full gap-8 mt-8">
            <div class="w-2/3">
                <h2 class="font-semibold text-2xl">About This Game</h2>
                <div class="divider m-0"></div>
                <div class="text-justify">
                    {!! nl2br(e($game->full_description)) !!}
                </div>
            </div>
            <div class="w-1/3 flex flex-col gap-2">
                <div class="bg-base-100 p-4 rounded-lg flex flex-row gap-4">
                    <img src="{{ $game->ageRating->image_url }}" alt="{{ $game->ageRating->title }} Logo"
                        class="h-20 w-auto rounded" />
                    <p class="text-sm text-justify">{{ $game->ageRating->description }}</p>

                </div>
                <div class="bg-base-100 p-4 rounded-lg flex flex-col gap-2 text-sm">
                    <p>Title: {{ $game->title }}</p>
                    <p>Genres: {{ $game->genres->pluck('name')->join(' ') }}</p>
                    <p>Publisher: {{ $game->publisher->name }}</p>
                    <p>Release Date: {{ $game->release_date }}</p>

                    <button class="btn btn-primary"><a href="{{ $game->publisher->website }}" target="_blank">View
                            Publisher
                            Website
                            {{-- add redirect link logo svg --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 inline-block ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </button>
                </div>
            </div>
        </div>
        <h1 class="text-2xl font-semibold">Customer Review</h1>
        {{-- no customer review --}}
        @if ($allReviewsCount > 0)
            {{-- display all reviews --}}
            @foreach ($game->gameReviews as $review)
                <div class="bg-base-100 flex flex-row gap-2 mb-2 p-4 rounded-lg ">
                    <div class="flex flex-row w-1/4 gap-2">
                        <img src="{{ asset('storage/default_profile_image.png') }}"
                            alt="{{ $review->user->name }} Profile" class="avatar size-16 rounded shrink-0" />
                        <div class="flex flex-col">
                            <span class="font-medium text-sm">{{ $review->user->nickname }}</span>
                            <span class="text-xs">{{ $review->user->gameReviews->count() }} reviews</span>
                        </div>
                    </div>
                    <div class=" flex flex-col gap-2 w-3/4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $review->ratingType->image_url }}" alt="{{ $review->ratingType->title }} Logo"
                                class="h-10 w-auto inline-block mr-2" />
                            <div class="flex flex-col">
                                <span class="font-medium text-sm">{{ $review->ratingType->title }}</span>
                                <span class="text-xs text-gray-500">Posted:
                                    {{ $review->created_at->format('F j, Y') }}</span>
                            </div>
                        </div>
                        <p class="text-sm">{{ $review->content }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-sm">No customer reviews yet. Be the first to review this game!</p>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endsection
