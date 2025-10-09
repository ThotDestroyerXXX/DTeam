@extends('layouts.app')

@section('title')
    Publishers
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        <h1 class="text-3xl font-bold ">Publishers</h1>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <!-- head -->
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Website</th>
                        <th>Country</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($publishers as $publisher)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $publisher->user->name }}</td>
                            <td><a href="{{ $publisher->website }}" target="_blank"
                                    class="link link-primary">{{ $publisher->website }}</a></td>
                            <td>{{ $publisher->user->country->name }}</td>
                            <td class="flex gap-2">
                                {{-- <a href="{{ route('admin.publishers.edit', $publisher) }}" class="btn btn-sm btn-outline">
                                    Edit
                                </a>
                                <form action="{{ route('admin.publishers.destroy', $publisher) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this publisher?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline btn-error">Delete</button>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $publishers->links() }}
        </div>
    </div>
@endsection
