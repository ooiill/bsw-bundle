<?php

namespace Leon\BswBundle\Controller\Traits;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @property AbstractController $container
 * @property AdapterInterface   $cache
 */
trait DisCache
{
    /**
     * Delete cache
     *
     * @param array $keys
     *
     * @return bool
     * @throws
     */
    public function popCache(array $keys): bool
    {
        return $this->cache->deleteItems(array_map('md5', $keys));
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function disCache(): bool
    {
        return $this->cache->clear();
    }
}