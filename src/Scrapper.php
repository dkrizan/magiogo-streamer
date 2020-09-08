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

    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var Config */
    private $config;

    /**
     * Scrapper constructor.
     * @param Client $connection
     * @param Authorization $authorization
     * @param \GuzzleHttp\Client $client
     * @param Config $config
     */
    public function __construct(
        Client $connection,
        Authorization $authorization,
        \GuzzleHttp\Client $client,
        Config $config
    ) {
        $this->cache = new Cache($connection, "streams");
        $this->authorization = $authorization;
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Fetch channel's stream url
     * @param String $channel
     * @return string
     * @throws UnknownChannelNameException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchStreamUrl(String $channel) : string {
        $stream = $this->cache->fetch($channel);
        if ($stream == null) {
            $accessToken = $this->authorization->getToken();
            try {
                $channelId = Channels::getChannelId($channel);
            } catch (UnknownChannelNameException $e) {
                die ($e->getMessage());
            }
            $res = $this->client->get($this->config->get('api.stream'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'service' => "LIVE",
                    'name' => "Web Browser",
                    'devtype' => "OTT_WIN",
                    'id' => $channelId,
                    'prof' => "p0",
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