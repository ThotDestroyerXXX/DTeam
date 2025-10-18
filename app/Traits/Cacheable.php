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
            '%s_%s_%s',
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
     * Clear the cache for this model.
     *
     * @return void
     */
    public function flushCache()
    {
        Cache::forget($this->getCacheKey());
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

            // Flush cache for paginated collections
            $cacheKey = sprintf('%s_paginated', $model->getTable());
            Cache::forget($cacheKey);
        });
    }
}
