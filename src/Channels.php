<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 09.07.20 20:48
 */

namespace App;

use Libs\Cache;
use Noodlehaus\Config;
use Predis\Client;

class Channels {

    /** @var Cache */
    private $cache;

    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var Authorization */
    private $authorization;

    /** @var Config */
    private $config;

    /** @var array */
    protected $channels = [];

    /**
     * Channels constructor.
     * @param Client $connection
     * @param \GuzzleHttp\Client $client
     * @param Authorization $authorization
     * @param Config $config
     */
    public function __construct(
        Client $connection,
        \GuzzleHttp\Client $client,
        Authorization $authorization,
        Config $config
    ) {
        $this->cache = new Cache($connection, 'channels');
        $this->client = $client;
        $this->authorization = $authorization;
        $this->config = $config;
    }

    /**
     * Returns channel ID
     * @param int $id
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function isValid(int $id) {
        return isset($this->getChannels()[$id]);
    }

    /**
     * Returns list of channels
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @return array
     */
    public function loadChannels() {
        if (($channels = $this->cache->fetch('channels')) == NULL) {
            $response = $this->client->get($this->config->get('api.channels'), [
                'headers' => $this->authorization->getHeaders(),
                'query' => [
                    'list' => 'LIVE',
                    'queryScope' => 'LIVE'
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $channels = [];
            foreach ($data['items'] as $item) {
                $channel = $item['channel'];
                $channels[$channel['channelId']] = $channel['name'];
            }
            $this->cache->store('channels', $channels);
        }
        return $channels;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getChannels(): array {
        if (!$this->channels) {
            $this->channels = $this->loadChannels();
        }
        return $this->channels;
    }
}