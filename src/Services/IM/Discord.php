<?php

declare(strict_types=1);

namespace App\Services\IM;

use App\Models\Config;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use const VERSION;

final class Discord extends Base
{
    private string $token;
    private Client $client;

    public function __construct()
    {
        $this->token = Config::obtain('discord_bot_token');
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function send($to, $msg): void
    {
        $dm_url = 'https://discord.com/api/v10/users/@me/channels';

        $headers = [
            'Authorization' => "Bot {$this->token}",
            'User-Agent' => 'DiscordBot (' . $_ENV['appName'] . ', ' . VERSION . ')',
            'Content-Type' => 'application/json',
        ];

        $dm_body = [
            'recipient_id' => $to,
        ];

        $dm_response = $this->client->post($dm_url, [
            'headers' => $headers,
            'json' => $dm_body,
        ]);

        $channel_id = json_decode($dm_response->getBody()->getContents())->id;
        $channel_url = "https://discord.com/api/v10/channels/{$channel_id}/messages";

        $msg_body = [
            'content' => $msg,
        ];

        $msg_response = $this->client->post($channel_url, [
            'headers' => $headers,
            'json' => $msg_body,
        ]);

        if ($msg_response->getStatusCode() !== 200) {
            throw new Exception($msg_response->getBody()->getContents());
        }
    }
}
