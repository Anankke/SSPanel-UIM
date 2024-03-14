<?php

declare(strict_types=1);

namespace App\Services\LLM;

use OpenAI as OpenAISDK;

final class OpenAI extends Base
{
    public function textPrompt(string $q): string
    {
        if ($_ENV['openai_api_key'] === '') {
            return 'OpenAI API key not set';
        }

        $client = OpenAISDK::client($_ENV['openai_api_key']);

        $response = $client->chat()->create([
            'model' => $_ENV['openai_model'],
            'temperature' => 2,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $q,
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function textPromptWithContext(string $q, array $context): string
    {
        return '';
    }
}
