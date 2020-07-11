<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 09.07.20 8:55
 */

namespace Libs;

use Predis\Client;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * Class Cache
 *
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * @package Libs
 *
 * @method CacheItem getItem($key)
 */
class Cache extends RedisAdapter {

    /**
     * Cache constructor.
     * @param Client $client
     * @param String $namespace
     * @param int $defaultLifetime
     */
    public function __construct(Client $client, string $namespace = '', $defaultLifetime = 43200) {
        parent::__construct($client, $namespace, $defaultLifetime);
    }

    /**
     * Fetch data from cache
     * @param string $key
     * @return CacheItem
     * @throws InvalidArgumentException
     */
    public function fetch(string $key) {
        return $this->getItem($key)->get();
    }

    /**
     * Saves data to cache
     * @param String $key
     * @param $data
     * @return CacheItem
     * @throws InvalidArgumentException
     */
    public function store(String $key, $data) {
        $item = $this->getItem($key);
        $item->set($data);
        $this->save($item);
        return $item;
    }
}