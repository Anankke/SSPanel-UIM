<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use OpenAI as OpenAISDK;

final class OpenAI extends Base
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
        $conversation = [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.',
            ],
        ];

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
        if (Config::obtain('openai_api_key') === '') {
            return 'OpenAI API key not set';
        }

        $client = OpenAISDK::client(Config::obtain('openai_api_key'));

        $response = $client->chat()->create([
            'model' => Config::obtain('openai_model_id'),
            'temperature' => 1,
            'messages' => $conversation,
        ]);

        return $response->choices[0]->message->content;
    }
}
