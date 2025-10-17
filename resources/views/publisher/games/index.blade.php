@extends('layouts.app')

@section('title')
    Games
@endsection

@section('content')
    <div class="container mx-auto p-4 gap-4 flex flex-col">
        <div class="flex items-end justify-between flex-row">
            <div class="flex flex-row gap-2 items-center">
                <img src="{{ $publisher->image_url }}" alt="{{ $publisher->name }}"
                    class="avatar size-16 rounded bg-cover object-center" />
                <div>
                    <h1 class="text-2xl font-bold">{{ $publisher->name }}</h1>
                    <a href="{{ $publisher->website }}" class="text-blue-500" target="_blank">{{ $publisher->website }}</a>
                </div>
            </div>
            <a href="{{ route('publisher.games.add') }}" class="btn btn-primary">Add Game</a>
        </div>
        <x-game-list />
    </div>
@endsection
