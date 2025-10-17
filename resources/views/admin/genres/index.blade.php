@extends('layouts.app')

@section('title')
    Genres
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        <h1 class="text-3xl font-bold ">Genres</h1>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <a href="{{ route('admin.genres.add') }}" class="btn btn-primary">Add New Genre</a>
            <form action="{{ route('admin.genres.index') }}" method="GET" class="mt-4">
                <div class="form-control">
                    <div class="join">
                        <div>
                            <label class="input join-item">
                                {{-- search svg --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-[1em]" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" placeholder="search genre" name="search" required />
                            </label>
                        </div>
                        <button class="btn btn-neutral join-item px-2"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-[1.4em]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            @if ($genres->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No genres found.
                </div>
            @else
                <table class="table table-zebra w-full">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Genre Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($genres as $genre)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $genre->name }}</td>
                                <td>
                                    <a href="{{ route('admin.genres.edit', $genre) }}"
                                        class="btn btn-sm btn-neutral">Edit</a>
                                    <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-sm btn-error text-primary-content">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="mt-4">
            {{ $genres->links() }}
        </div>
    </div>
@endsection
