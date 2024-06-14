<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use App\Services\LLM\Anthropic;
use App\Services\LLM\AwsBedrock;
use App\Services\LLM\CloudflareWorkersAI;
use App\Services\LLM\GoogleAI;
use App\Services\LLM\HuggingFace;
use App\Services\LLM\OpenAI;
use App\Services\LLM\VertexAI;

final class LLM
{
    public static function getBackend(): VertexAI|CloudflareWorkersAI|GoogleAI|HuggingFace|OpenAI|Anthropic|AwsBedrock
    {
        return match (Config::obtain('llm_backend')) {
            'google-ai' => new GoogleAI(),
            'vertex-ai' => new VertexAI(),
            'huggingface' => new HuggingFace(),
            'cf-workers-ai' => new CloudflareWorkersAI(),
            'anthropic' => new Anthropic(),
            'aws-bedrock' => new AwsBedrock(),
            default => new OpenAI(),
        };
    }

    public static function genTextResponse(string $q): string
    {
        if (Config::obtain('llm_backend') === '') {
            return 'No LLM backend configured';
        }

        if ($q === '') {
            return 'No question provided';
        }

        return self::getBackend()->textPrompt($q);
    }

    public static function genTextResponseWithContext(array $context = []): string
    {
        if (Config::obtain('llm_backend') === '') {
            return 'No LLM backend configured';
        }

        if ($context === []) {
            return 'No context provided';
        }

        return self::getBackend()->textPromptWithContext($context);
    }
}
