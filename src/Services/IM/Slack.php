<?php

declare(strict_types=1);

namespace App\Services\IM;

use App\Models\Config;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class Slack extends Base
{
    private string $token;
    private Client $client;

    public function __construct()
    {
        $this->token = Config::obtain('slack_token');
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function send($to, $msg): void
    {
        $url = 'https://slack.com/api/chat.postMessage';

        $headers = [
            'Authorization' => 'Bearer '.$this->token,
            'Content-Type' => 'application/json',
        ];

        $body = [
            'channel' => $to,
            'text' => $msg,
        ];

        $response = $this->client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception($response->getBody()->getContents());
        }
    }
}
