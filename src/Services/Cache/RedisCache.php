<?php

namespace App\Services\Cache;

use App\Services\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class RedisCache implements CacheInterface
{
    public RedisTagAwareAdapter $cache;

    public function __construct()
    {
        $this->cache = new RedisTagAwareAdapter(RedisAdapter::createConnection('redis://localhost:6379'));
    }
}