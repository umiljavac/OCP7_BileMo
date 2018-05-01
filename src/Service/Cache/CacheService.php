<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/04/2018
 * Time: 11:12
 */

namespace App\Service\Cache;

use Psr\Cache\CacheItemPoolInterface;

class CacheService
{
    private $cachePool;

    public function __construct(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param $key
     *
     * @return bool
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function isCached($key)
    {
        $item = $this->cachePool->getItem($key); //md5
        return $item->isHit();
    }

    /**
     * @param $key
     * @param $data
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function cacheData($key, $data)
    {
        $item = $this->cachePool->getItem($key); //md5
        $item->set($data);
        $this->cachePool->save($item);
    }

    /**
     * @param $key
     *
     * @return mixed
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCachedResources($key)
    {
        $item = $this->cachePool->getItem($key); //md5
        $fetchData = $item->get();
        return $fetchData;
    }

    /**
     * @param $key
     *
     * @return bool|string
     */
    public function setCacheKey($key)
    {
        return substr(str_replace(['/', '?', '=', '&'], '_', $key), 1);
    }

    /**
     * @param $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteItem($key)
    {
        $this->cachePool->deleteItem($key); //md5
    }
}
