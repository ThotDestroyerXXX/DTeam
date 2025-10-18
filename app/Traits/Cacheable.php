<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Get the cache key for the model instance.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return sprintf(
            'model_%s_%s_%s',
            $this->getTable(),
            $this->getKeyName(),
            $this->getKey()
        );
    }

    /**
     * Get a cached version of a model by ID.
     *
     * @param int $id
     * @param int $ttl Time to live in seconds
     * @return mixed
     */
    public static function findCached($id, $ttl = 3600)
    {
        $cacheKey = sprintf('%s_%s_%s', (new static)->getTable(), 'id', $id);

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Get a cached collection of models.
     *
     * @param string $key Cache key suffix
     * @param callable $query Query to execute if not cached
     * @param int $ttl Time to live in seconds
     * @return mixed
     */
    public static function getCached($key, callable $query, $ttl = 3600)
    {
        $cacheKey = sprintf('%s_%s', (new static)->getTable(), $key);

        return Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $query();
        });
    }

    /**
     * Cache the results of a query scope
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $key Additional key to identify this specific query
     * @param int $ttl Cache lifetime in seconds
     * @return mixed
     */
    public static function cacheQuery($query, $key, $ttl = 3600)
    {
        // Generate a unique key based on the SQL and bindings
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $queryKey = md5($sql . serialize($bindings));

        $cacheKey = (new static)->getTable() . '_query_' . $key . '_' . $queryKey;

        return Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $query->get();
        });
    }

    /**
     * Clear the cache for this model.
     *
     * @return void
     */
    public function flushCache()
    {
        // Clear specific model cache
        Cache::forget($this->getCacheKey());

        // Clear related caches
        $table = $this->getTable();
        Cache::forget("{$table}_all");
        Cache::forget("{$table}_paginated");
        Cache::forget("{$table}_recent");

        // For more complex scenarios, we might need to implement a tag-based
        // cache system or use a cache driver that supports wildcards
    }

    /**
     * Get all models with cache
     *
     * @param int $ttl Cache time in seconds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllCached($ttl = 3600)
    {
        $cacheKey = (new static)->getTable() . '_all';

        return Cache::remember($cacheKey, $ttl, function () {
            return static::all();
        });
    }

    /**
     * Get paginated models with cache
     *
     * @param int $perPage Number of items per page
     * @param int $ttl Cache time in seconds
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getPaginatedCached($perPage = 15, $ttl = 3600)
    {
        $page = request()->get('page', 1);
        $cacheKey = (new static)->getTable() . '_paginated_' . $page . '_' . $perPage;

        return Cache::remember($cacheKey, $ttl, function () use ($perPage) {
            return static::paginate($perPage);
        });
    }

    /**
     * Handle model events to automatically clear cache.
     *
     * @return void
     */
    protected static function bootCacheable()
    {
        static::updated(function ($model) {
            $model->flushCache();
        });

        static::deleted(function ($model) {
            $model->flushCache();
        });

        static::created(function ($model) {
            // Flush cache for collections
            $cacheKey = sprintf('%s_all', $model->getTable());
            Cache::forget($cacheKey);

            // Flush cache for paginated collections - we use a pattern here
            $table = $model->getTable();
            Cache::forget("{$table}_paginated_1_15"); // Most common pagination
            Cache::forget("{$table}_recent");
        });
    }
}
