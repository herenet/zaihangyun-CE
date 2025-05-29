<?php

namespace App\Libs;

use Illuminate\Cache\RedisStore;

class JsonRedisStore extends RedisStore
{
    /**
     * Store an item in the cache for a given number of seconds.
     */
    public function put($key, $value, $seconds)
    {
        return $this->connection()->setex(
            $this->prefix.$key,
            (int) max(1, $seconds),
            json_encode($value, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Store an item in the cache indefinitely.
     */
    public function forever($key, $value)
    {
        return $this->connection()->set(
            $this->prefix.$key,
            json_encode($value, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Retrieve an item from the cache by key.
     */
    public function get($key)
    {
        $value = $this->connection()->get($this->prefix.$key);

        if (is_null($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        
        // 如果 JSON 解码失败，返回原始值
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }

        return $decoded;
    }

    /**
     * Retrieve multiple items from the cache by key.
     */
    public function many(array $keys)
    {
        $results = [];

        $values = $this->connection()->mget(array_map(function ($key) {
            return $this->prefix.$key;
        }, $keys));

        foreach ($values as $index => $value) {
            if (is_null($value)) {
                $results[$keys[$index]] = null;
            } else {
                $decoded = json_decode($value, true);
                $results[$keys[$index]] = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
            }
        }

        return $results;
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     */
    public function putMany(array $values, $seconds)
    {
        $this->connection()->multi();

        $manyResult = null;

        foreach ($values as $key => $value) {
            $result = $this->put($key, $value, $seconds);
            $manyResult = is_null($manyResult) ? $result : $result && $manyResult;
        }

        $this->connection()->exec();

        return $manyResult ?: false;
    }
}