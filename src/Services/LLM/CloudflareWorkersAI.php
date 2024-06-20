<?php

declare(strict_types=1);

namespace App\Services\LLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class CloudflareWorkersAI extends Base
{
    /**
     * @throws GuzzleException
     */
    public function textPrompt(string $q): string
    {
        if ($_ENV['cf_workers_ai_account_id'] === '' || $_ENV['cf_workers_ai_api_token'] === '') {
            return 'Cloudflare Workers AI Account ID or API Token not set';
        }

        $client = new Client();

        $api_url = 'https://api.cloudflare.com/client/v4/accounts/' .
            $_ENV['cf_workers_ai_account_id'] . '/ai/run/' . $_ENV['cf_workers_ai_model_id'];

        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['cf_workers_ai_api_token'],
            'Content-Type' => 'application/json',
        ];

        $data = [
            'prompt' => $q,
        ];

        $response = json_decode($client->post($api_url, [
            'headers' => $headers,
            'json' => $data,
            'timeout' => 10,
        ])->getBody()->getContents());

        return $response->result->response;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
