<?php

namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CacheBucket
{
    /**
     * Build a compact cache key.
     *
     * @param string $group
     * @param array $parts
     * @return string
     */
    public static function make(string $group, array $parts = []): string
    {
        $payload = json_encode($parts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = md5($payload ?: '');

        return "cache:{$group}:{$hash}";
    }

    /**
     * Remember a value in Redis using a compact generated key.
     *
     * @param string $group
     * @param array $parts
     * @param mixed $ttl
     * @param Closure $callback
     * @return mixed
     */
    public static function remember(string $group, array $parts, mixed $ttl, Closure $callback): mixed
    {
        $key = self::make($group, $parts);

        try {
            return Cache::store('redis')->tags([$group])->remember($key, $ttl, $callback);
        } catch (Throwable) {
            return $callback();
        }
    }

    /**
     * Flush grouped Redis cache keys.
     *
     * @param string $group
     * @return void
     */
    public static function flush(string $group): void
    {
        try {
            Cache::store('redis')->tags([$group])->flush();
        } catch (Throwable) {
        }
    }
}