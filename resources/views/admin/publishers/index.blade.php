@extends('layouts.app')

@section('title')
    Publishers
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        <h1 class="text-3xl font-bold ">Publishers</h1>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <a href="{{ route('admin.publishers.add') }}" class="btn btn-primary">Add New Publisher</a>
            <form action="{{ route('admin.publishers.index') }}" method="GET" class="mt-4">
                <div class="form-control">
                    <div class="join">
                        <div>
                            <label class="input join-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-[1em]" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search publisher name" />
                            </label>
                        </div>
                        <button type="submit" class="btn btn-neutral join-item px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.4em]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            @if ($publishers->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No publishers found.
                </div>
            @else
                <table class="table table-zebra w-full">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Publisher Logo</th>
                            <th scope="col">Publisher Name</th>
                            <th scope="col">Publisher Website</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publishers as $publisher)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td><img src="{{ $publisher->image_url }}" alt="{{ $publisher->name }}"
                                        class="w-12 h-12 object-cover rounded"></td>
                                <td>{{ $publisher->name }}</td>
                                <td><a href="{{ $publisher->website }}" target="_blank"
                                        class="link link-primary">{{ $publisher->website }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="mt-4">
            {{ $publishers->links() }}
        </div>

        @if (request('search'))
            <div class="mt-4 text-sm text-gray-500">
                Showing results for search: "{{ request('search') }}"
            </div>
        @endif
    </div>
@endsection
