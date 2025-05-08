<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheTracker
{
    /**
     * Cache tracking key based on user id
     */
    public static function track(string $listKey, string $cacheKey, int $ttlInHours = 24): void
    {
        $existing = Cache::get($listKey, []);
        $updated = array_unique([...$existing, $cacheKey]);

        Cache::put($listKey, $updated, now()->addHours($ttlInHours));
    }

    public static function clearTracked(string $listKey): void
    {
        $keys = Cache::get($listKey, []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget($listKey); // Also clear the tracking list
    }
}
