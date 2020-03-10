<?php


namespace App\Services;

use Predis\Client;

class RedisClient
{
    public $client;

    public function __construct()
    {
        $config = [
            'scheme' => $_ENV['redis_scheme'],
            'password' => $_ENV['redis_password'],
            'host' => $_ENV['redis_host'],
            'port' => $_ENV['redis_port'],
            'database' => $_ENV['redis_database'],
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
