<?php

declare(strict_types=1);

namespace App\Services;

use OpenAI;

final class ChatGPT
{
    public static function askOnce(string $q): string
    {
        if ($_ENV['openai_api_key'] === '') {
            return 'OpenAI API key not set';
        }

        if ($q === '') {
            return 'No question provided';
        }

        $client = OpenAI::client($_ENV['openai_api_key']);
        $response = $client->chat()->create([
            'model' => $_ENV['ai_model'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $q,
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}
