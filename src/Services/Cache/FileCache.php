<?php

namespace App\Services\Cache;

use App\Services\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class FileCache implements CacheInterface
{
    public function __construct(public $cache)
    {
        $this->cache = new TagAwareAdapter(new FilesystemAdapter());
    }
}