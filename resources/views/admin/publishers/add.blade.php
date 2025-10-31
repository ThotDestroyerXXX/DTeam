@extends('layouts.app')

@section('title')
    Publishers
@endsection

@section('content')
    <div class="container flex flex-col gap-4">
        <h1 class="text-3xl font-bold">Add Publisher</h1>
        <form method="POST" action="{{ route('admin.publishers.store') }}" class='flex flex-col gap-4'>
            @csrf
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Publisher Email</legend>
                <input type="email" name="email" class="input" placeholder="Email" required />
            </fieldset>
            <button type="submit" class="btn btn-primary w-fit">Add Publisher</button>
        </form>
    </div>
@endsection
