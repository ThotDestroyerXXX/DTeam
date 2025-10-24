@extends('layouts.app')

@section('title')
    {{ config('app.name', 'Laravel') }} - Point Shop
@endsection

@section('content')
    <div class="flex flex-col gap-6 text-sm">
        <div class="flex flex-row justify-between items-center">
            <h1 class="font-semibold text-3xl">Point Shop</h1>
            <div class="flex flex-col items-end">
                <span class="font-semibold text-base">Your Points</span>
                <span>{{ Auth::user()->point }}</span>

            </div>
        </div>
        <h2 class="font-semibold text-2xl">Avatars</h2>
        @if ($avatars->isEmpty())
            <p class="text-gray-500">No items available in the point shop.</p>
        @else
            <div class='flex flex-row flex-wrap gap-4 justify-around'>
                @foreach ($avatars as $index => $avatar)
                    <x-point-shop.item-card :item="$avatar" :type="'avatar'" :index="$index" />
                    <dialog id="my_modal_{{ $index }}_avatar" class="modal">
                        <div class="modal-box">

                            <div class="flex gap-4">
                                <div class="w-32 h-32 bg-base-200 rounded overflow-hidden flex items-center justify-center">
                                    <img id="modal-item-image" src="{{ $avatar->image_url }}" alt=""
                                        class="object-contain max-h-full max-w-full" />
                                </div>
                                <div class="flex-1">
                                    <h3 id="modal-item-name" class="text-lg font-bold">{{ $avatar->name }}</h3>
                                    <div id="modal-item-price" class="text-sm font-medium text-gray-700 mt-1">
                                        {{ $avatar->price }} Points
                                    </div>
                                </div>
                            </div>

                            <div class="modal-action justify-between items-center">
                                @if (Auth::user()->items()->where('item_id', $avatar->id)->exists())
                                    <div class="text-red-500 italic">You Owned This Item</div>
                                @elseif(Auth::user()->point < $avatar->price)
                                    <div class="text-red-500 italic">Insufficient Points
                                    </div>
                                @endif
                                <div class="flex flex-row gap-2">
                                    @if (Auth::user()->point >= $avatar->price && !Auth::user()->items()->where('items.id', $avatar->id)->exists())
                                        <form id="point-purchase-form" method="POST"
                                            action="{{ route('user.point-shop.purchase', $avatar->id) }}">
                                            @csrf
                                            <button type="submit" id="modal-purchase-btn"
                                                class="btn btn-primary">Purchase</button>
                                        </form>
                                    @elseif(Auth::user()->items()->where('items.id', $avatar->id)->exists())
                                        <a href="{{ route('user.profile.edit') }}" class="btn btn-primary">Edit My
                                            Avatar</a>
                                    @endif
                                    <form method="dialog">
                                        <!-- if there is a button in form, it will close the modal -->
                                        <button class="btn">Close</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </dialog>
                @endforeach

            </div>
        @endif
        <h2 class="font-semibold text-2xl">Backgrounds</h2>
        @if ($backgrounds->isEmpty())
            <p class="text-gray-500">No items available in the point shop.</p>
        @else
            <div class='flex flex-row flex-wrap gap-4 justify-around'>
                @foreach ($backgrounds as $index => $background)
                    <x-point-shop.item-card :item="$background" :type="'background'" :index="$index" />
                    <dialog id="my_modal_{{ $index }}_background" class="modal">
                        <div class="modal-box">

                            <div class="flex gap-4">
                                <div class="w-32 h-32 bg-base-200 rounded overflow-hidden flex items-center justify-center">
                                    <img id="modal-item-image" src="{{ $background->image_url }}" alt=""
                                        class="object-contain max-h-full max-w-full" />
                                </div>
                                <div class="flex-1">
                                    <h3 id="modal-item-name" class="text-lg font-bold">{{ $background->name }}</h3>
                                    <div id="modal-item-price" class="text-sm font-medium text-gray-700 mt-1">
                                        {{ $background->price }} Points
                                    </div>
                                </div>
                            </div>

                            <div class="modal-action justify-between items-center">
                                @if (Auth::user()->items()->where('items.id', $background->id)->exists())
                                    <div class="text-red-500 italic">You Owned This Item</div>
                                @elseif(Auth::user()->point < $background->price)
                                    <div class="text-red-500 italic">Insufficient Points
                                    </div>
                                @endif
                                <div>
                                    @if (Auth::user()->point >= $background->price && !Auth::user()->items()->where('items.id', $background->id)->exists())
                                        <form id="point-purchase-form" method="POST"
                                            action="{{ route('user.point-shop.purchase', $background->id) }}">@csrf
                                            <button type="submit" id="modal-purchase-btn"
                                                class="btn btn-primary">Purchase</button>
                                        </form>
                                    @elseif(Auth::user()->items()->where('item_id', $background->id)->exists())
                                        <a href="{{ route('user.profile.edit') }}" class="btn btn-primary">Edit My
                                            Avatar</a>
                                    @endif
                                    <form method="dialog">
                                        <!-- if there is a button in form, it will close the modal -->
                                        <button class="btn">Close</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </dialog>
                @endforeach
            </div>
        @endif
    </div>
@endsection
