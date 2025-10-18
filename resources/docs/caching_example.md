# DTeam Application Caching Guide

This document explains how to effectively use the cache implementation in the DTeam application.

## Basic Model Caching

The application includes a `Cacheable` trait that provides several useful caching methods for models. When applied to a model, it automatically handles cache invalidation when records are created, updated, or deleted.

### How to Use in Controllers

```php
// GameController.php
public function index()
{
    // Get all games with caching (caches for 1 hour by default)
    $games = Game::getAllCached();

    // OR with pagination
    $games = Game::getPaginatedCached(12); // 12 items per page, cached

    return view('games.index', compact('games'));
}

public function show($id)
{
    // Find a specific game with caching
    $game = Game::findCached($id);

    // Load relationships with caching (to avoid N+1 queries)
    $game->load('publisher', 'genres');

    return view('games.show', compact('game'));
}
```

### Advanced Caching for Custom Queries

```php
public function featured()
{
    // Cache a custom query using a closure
    $featuredGames = Game::getCached('featured', function() {
        return Game::where('is_featured', true)
                   ->with('genres', 'publisher')
                   ->orderBy('release_date', 'desc')
                   ->take(5)
                   ->get();
    }, 60*30); // Cache for 30 minutes

    return view('games.featured', compact('featuredGames'));
}

public function search(Request $request)
{
    $query = $request->get('q');

    // For queries that depend on user input, include the input in the cache key
    $searchResults = Game::getCached('search_' . md5($query), function() use ($query) {
        return Game::where('title', 'LIKE', "%{$query}%")
                   ->orWhere('description', 'LIKE', "%{$query}%")
                   ->with('genres')
                   ->get();
    }, 60*10); // Cache for 10 minutes

    return view('games.search', compact('searchResults', 'query'));
}
```

### Caching Queries with Existing Query Builder

```php
public function byGenre($genreId)
{
    $genre = Genre::findCached($genreId);

    // Create a query builder
    $query = Game::where('discount_percentage', '>', 0)
                ->whereHas('genres', function($q) use ($genreId) {
                    $q->where('genres.id', $genreId);
                })
                ->orderBy('discount_percentage', 'desc');

    // Cache this specific query with the cacheQuery method
    $discountedGames = Game::cacheQuery($query, 'discounted_by_genre_' . $genreId, 60*15);

    return view('games.by_genre', compact('genre', 'discountedGames'));
}
```

## Cache Invalidation

The caching trait automatically handles cache invalidation in the following scenarios:

1. When a model is created, the related collection caches are cleared
2. When a model is updated, its specific cache is cleared
3. When a model is deleted, its specific cache is cleared

For more complex relationships, you might need to manually clear related caches:

```php
public function update(Request $request, $id)
{
    $game = Game::findOrFail($id);
    $game->update($request->validated());

    // If game genre relationships are updated, clear genre-related caches
    if ($request->has('genres')) {
        $table = (new Genre)->getTable();
        Cache::forget("{$table}_all");
        Cache::forget("{$table}_paginated_1_15");
    }

    return redirect()->route('games.show', $game);
}
```

## Production vs Development Caching

The application is configured to use different cache settings based on the environment:

-   In production: Uses database cache with longer TTLs
-   In development: Uses array cache (in-memory, per-request) to avoid stale data

### Optimizing Production Deployment

For production deployments, run these commands to maximize performance:

```bash
php artisan optimize
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

These commands will cache frequently accessed components:

-   Routes: Caches the route registration, significantly reducing route resolution time
-   Config: Consolidates all configuration files into a single cached file
-   Views: Pre-compiles Blade templates into PHP for faster rendering

Remember to run `php artisan cache:clear` after deploying updates to production.

## Advanced Optimizations

### Redis Implementation

For improved cache performance, consider installing Redis:

```bash
composer require predis/predis
```

Then update your `.env` file:

```
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Redis offers better performance than database caching and supports tagging, which makes cache invalidation more efficient.

### Image Optimization

Since the application deals with game images, consider implementing:

-   Image optimization through a CDN
-   Using a package like spatie/laravel-image-optimizer
-   Implementing lazy loading for images on listing pages

### Database Optimization

Review your database schema and add indexes to frequently queried columns, particularly:

-   Foreign keys
-   Columns used in WHERE clauses
-   Columns used for sorting

### Cache Analysis

To identify slow queries that would benefit from caching:

1. Enable query logging in development
2. Monitor execution times
3. Apply caching to queries that take more than ~50ms to execute
