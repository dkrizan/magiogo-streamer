<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 06.07.20 21:25
 */

namespace App;

use Libs\Cache;
use Noodlehaus\Config;
use Predis\Client;

class Scrapper {

    /** @var Cache */
    private $cache;

    /** @var Authorization */
    private $authorization;

    /** @var Channels */
    private $channels;

    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var Config */
    private $config;

    /**
     * Scrapper constructor.
     * @param Client $connection
     * @param Authorization $authorization
     * @param Channels $channels
     * @param \GuzzleHttp\Client $client
     * @param Config $config
     */
    public function __construct(
        Client $connection,
        Authorization $authorization,
        Channels $channels,
        \GuzzleHttp\Client $client,
        Config $config
    ) {
        $this->cache = new Cache($connection, "streams");
        $this->authorization = $authorization;
        $this->channels = $channels;
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Fetch channel's stream url
     * @param int $channel
     * @return string
     * @throws UnknownChannelNameException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchStreamUrl(int $channel) : string {
        $stream = $this->cache->fetch($channel);
        if ($stream == null) {
            if (!$this->channels->isValid($channel)) {
                throw new UnknownChannelNameException();
            }
            $res = $this->client->get($this->config->get('api.stream'), [
                'headers' => $this->authorization->getHeaders(),
                'query' => [
                    'service' => "LIVE",
                    'name' => "Web Browser",
                    'devtype' => "OTT_WIN",
                    'id' => $channel,
                    'prof' => "p5",
                    'drm' => "verimatrix"
                ]
            ]);
            $data = json_decode($res->getBody()->getContents(), true);
            $stream = $data['url'];
            $this->cache->store($channel, $stream);
        }
        return $stream;
    }
}