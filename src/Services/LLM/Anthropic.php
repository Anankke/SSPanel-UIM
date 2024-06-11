<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class Anthropic extends Base
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
        $conversation = [];

        if (count($context) > 0) {
            foreach ($context as $role => $content) {
                $conversation[] = [
                    'role' => $role === 'user' ? 'user' : 'assistant',
                    'content' => $content,
                ];
            }
        }

        return $this->makeRequest($conversation);
    }

    private function makeRequest(array $conversation): string
    {
        if (Config::obtain('anthropic_api_key') === '') {
            return 'Anthropic API key not set';
        }

        $api_url = 'https://api.anthropic.com/v1/messages';

        $headers = [
            'x-api-key' => Config::obtain('anthropic_api_key'),
            'content-type' => 'application/json',
        ];

        $data = [
            'model' => Config::obtain('anthropic_model_id'),
            'temperature' => 1,
            'messages' => $conversation,
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

        return $response->content[0]->text;
    }
}
