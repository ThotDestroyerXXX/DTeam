<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);

        // Create a unique cache key based on search and pagination
        $cacheKey = 'genres_' . ($search ? md5($search) : 'all') . '_page_' . $page;

        $genres = Cache::remember($cacheKey, 1800, function () use ($search) {
            $query = Genre::query();

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            return $query->where('is_active', true)->paginate(10)->withQueryString();
        });

        return view('admin.genres.index', [
            'genres' => $genres
        ]);
    }

    public function destroy(Genre $genre)
    {
        $genre->update(['is_active' => false]);

        // Clear all genre-related caches when a genre is deleted
        $this->clearGenreCaches();

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

        // Clear genre caches after creating a new genre
        $this->clearGenreCaches();

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

        // Clear genre caches after updating
        $this->clearGenreCaches();

        return redirect()->route('admin.genres.index')->with('success', 'Genre updated successfully.');
    }

    /**
     * Clear all genre-related caches
     */
    private function clearGenreCaches()
    {
        // Clear specific genre caches
        Cache::forget('genres_all_page_1');

        // Clear the popular games cache in StoreController
        Cache::forget('popular_games');

        // Use cache tags if your driver supports it
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['genres'])->flush();
        } else {
            // For database driver, we'll clear specific known keys
            for ($i = 1; $i <= 5; $i++) { // Clear first 5 pages which are most common
                Cache::forget('genres_all_page_' . $i);
            }
        }
    }
}
