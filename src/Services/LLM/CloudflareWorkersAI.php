<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class CloudflareWorkersAI extends Base
{
    public function textPrompt(string $q): string
    {
        return $this->makeRequest([
            [
                'role' => 'user',
                'content' => $q,
            ],
        ]);
    }

    public function textPromptWithContext(array $context): string
    {
        return 'This service does not support context';
    }

    private function makeRequest(array $conversation): string
    {
        if (Config::obtain('cf_workers_ai_account_id') === '' ||
            Config::obtain('cf_workers_ai_api_token') === '') {
            return 'Cloudflare Workers AI Account ID or API Token not set';
        }

        $api_url = 'https://api.cloudflare.com/client/v4/accounts/' .
            Config::obtain('cf_workers_ai_account_id') . '/ai/run/' . Config::obtain('cf_workers_ai_model_id');

        $headers = [
            'Authorization' => 'Bearer ' . Config::obtain('cf_workers_ai_api_token'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            'prompt' => $conversation[0]['content'],
        ];

        try {
            $response = json_decode($this->client->post($api_url, [
                'headers' => $headers,
                'json' => $data,
                'timeout' => 30,
            ])->getBody()->getContents());
        } catch (GuzzleException $e) {
            return '';
        }

        return $response->result->response;
    }
}
