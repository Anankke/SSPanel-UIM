<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RedisException;
use function json_decode;
use function round;

final class Exchange
{
    /**
     * @throws GuzzleException
     * @throws RedisException
     */
    public function exchange(float $amount, string $from, string $to): float
    {
        return round($amount * $this->getExchangeRate($from, $to), 2);
    }

    /**
     * @throws GuzzleException
     * @throws RedisException
     */
    public function getExchangeRate(string $from, string $to): float
    {
        $redis = (new Cache())->initRedis();
        $rate = $redis->get('exchange_rate:' . $from . '_' . $to);

        if (! $rate) {
            $client = new Client();
            $response = $client->get('https://cdn.moneyconvert.net/api/latest.json');
            $data = json_decode($response->getBody()->getContents(), true);
            $rate = $data['rates'][$to] / $data['rates'][$from];
            $redis->setex('exchange_rate:' . $from . '_' . $to, 3600, $rate);
        }

        return (float) $rate;
    }
}
