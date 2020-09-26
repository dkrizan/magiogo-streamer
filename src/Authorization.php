<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 07.07.20 9:02
 */

namespace App;

use GuzzleHttp\Client;
use Libs\Cache;
use Noodlehaus\Config;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;

class Authorization {

    /** @var Cache */
    private $cache;

    /** @var Config */
    private $config;

    /** @var Client */
    private $client;

    /** @var String - Authorization token */
    private $token;

    /**
     * Authorization constructor.
     * @param \Predis\Client $connection
     * @param Config $config
     * @param Client $client
     */
    public function __construct(\Predis\Client $connection, Config $config, Client $client) {
        $this->cache = new Cache($connection, "auth", $config->get('accessTokenExpiration', 1800));
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Returns authorization token
     *
     * If token is missing in cache, it loads from API.
     * @return String
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getToken() {
        if ($this->token == null) {
            $this->token = $this->getTokenFromCache();
            if ($this->token == null) {
                $this->setToken($this->login());
            }
        }
        return $this->token;
    }

    /**
     * Return authorization headers
     * @return \string[]
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHeaders() {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json'
        ];
    }

    /**
     * Initialize authorization
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function initAuth() {
        $res = $this->client->request('POST', $this->config->get('api.auth'), [
            'query' => [
                'dsid' => "Netscape.1594195987012.0.07308992526903035",
                'deviceName' => "Web Browser",
                'deviceType' => "OTT_WIN",
                'osVersion' => "0.0.0",
                'appVersion' => "0.0.0",
                'language' => "SK"
            ]
        ]);
        $data = json_decode($res->getBody()->getContents(), true);
        return $data['token']['accessToken'];
    }

    /**
     * Login to Magio API and fetch access token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function login() {
        $accessToken = $this->initAuth();
        $res = $this->client->request('POST', $this->config->get('api.login'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'loginOrNickname' => $this->config->get('auth.username'),
                'password' => $this->config->get('auth.password')
            ]
        ]);
        $data = json_decode($res->getBody()->getContents(), true);
        return $data['token']['accessToken'];
    }

    /**
     * Sets token and saves in cache
     * @param String $token
     * @throws InvalidArgumentException
     */
    private function setToken(string $token): void {
        $this->token = $token;
        $this->cache->store('token', $token);
    }

    /**
     * Retrieves token from cache
     * @return CacheItem
     * @throws InvalidArgumentException
     */
    private function getTokenFromCache() {
        return $this->cache->fetch('token');
    }
}