<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $query = Genre::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $genres = $query->where('is_active', true)->paginate(10)->withQueryString();

        return view('admin.genres.index', [
            'genres' => $genres
        ]);
    }

    public function destroy(Genre $genre)
    {
        $genre->update(['is_active' => false]);
        return redirect()->route('admin.genres.index')->with('success', 'Genre deleted successfully.');
    }

    public function add()
    {
        return view('admin.genres.add');
    }

    public function store(GenreRequest $request)
    {
        $inputs = $request->validated();

        Genre::create($inputs);

        return redirect()->route('admin.genres.index')->with('success', 'Genre created successfully.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', [
            'genre' => $genre
        ]);
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $inputs = $request->validated();

        $genre->update($inputs);

        return redirect()->route('admin.genres.index')->with('success', 'Genre updated successfully.');
    }
}
