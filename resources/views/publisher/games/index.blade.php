@extends('layouts.app')

@section('title')
    Games
@endsection

@section('content')
    <div class="container mx-auto p-4 gap-4 flex flex-col">
        <x-publisher.game-list-with-header :publisher="$publisher" :games="$games" />
    </div>
@endsection
