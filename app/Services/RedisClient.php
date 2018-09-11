<?php


namespace App\Services;

use Predis\Client;

class RedisClient
{
    public $client;

    public function __construct()
    {
        $config = [
            'scheme' => Config::get('redis_scheme'),
'password' => Config::get('redis_password'),
            'host'     => Config::get('redis_host'),
            'port'     => Config::get('redis_port'),
            'database' => Config::get('redis_database'),
        ];
        $this->client = new Client($config);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function get($key)
    {
        return $this->client->get($key);
    }

    public function set($key, $value)
    {
        $this->client->set($key, $value);
    }

    public function setex($key, $time, $value)
    {
        $this->client->setex($key, $time, $value);
    }

    public function del($key)
    {
        $this->client->del($key);
    }
}
