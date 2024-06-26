<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use GuzzleHttp\Exception\GuzzleException;
use function json_decode;

final class GoogleAI extends Base
{
    public function textPrompt(string $q): string
    {
        return $this->makeRequest([
            [
                'parts' => [
                    [
                        'text' => $q,
                    ],
                ],
                'role' => 'user',
            ],
        ]);
    }

    public function textPromptWithContext(array $context): string
    {
        $conversation = [];

        if (count($context) > 0) {
            foreach ($context as $role => $content) {
                $conversation[] = [
                    'parts' => [
                        [
                            'text' => $content,
                        ],
                    ],
                    'role' => $role === 'user' ? 'user' : 'model',
                ];
            }
        }

        return $this->makeRequest($conversation);
    }

    private function makeRequest(array $conversation): string
    {
        if (Config::obtain('google_ai_api_key') === '') {
            return 'Google AI API key not set';
        }

        $api_url = 'https://generativelanguage.googleapis.com/v1/models/' .
            Config::obtain('google_ai_model_id') . ':generateContent?key=' . Config::obtain('google_ai_api_key');

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $data = [
            'contents' => $conversation,
            'generationConfig' => [
                'temperature' => 1,
                'candidateCount' => 1,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE',
                ],
            ],
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

        return $response->candidates[0]->content->parts[0]->text;
    }
}
