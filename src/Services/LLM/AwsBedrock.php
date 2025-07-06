<?php

declare(strict_types=1);

namespace App\Services\LLM;

use App\Models\Config;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Credentials\Credentials;

final class AwsBedrock extends Base
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
        if (Config::obtain('aws_bedrock_access_key_id') === '' ||
            Config::obtain('aws_bedrock_access_key_secret') === '') {
            return 'Access Key ID or Access Key Secret is empty';
        }

        $client = new BedrockRuntimeClient([
            'region' => Config::obtain('aws_bedrock_region'),
            'version' => 'latest',
            'profile' => 'default',
            'credentials' => new Credentials(
                Config::obtain('aws_bedrock_access_key_id'),
                Config::obtain('aws_bedrock_access_key_secret'),
            ),
        ]);
        // Note: Different models in AWS Bedrock have different inference parameters, this service currently only supports
        // Meta Llama series models
        // https://docs.aws.amazon.com/bedrock/latest/userguide/model-parameters-meta.html
        $request = [
            'contentType' => 'application/json',
            'body' => json_encode([
                'prompt' => $conversation[0]['content'],
                'temperature' => 0.5,
                'max_gen_len' => 2048,
            ]),
            'modelId' => Config::obtain('aws_bedrock_model_id'),
        ];

        return json_decode($client->invokeModel($request)['body'])->generation;
    }
}
