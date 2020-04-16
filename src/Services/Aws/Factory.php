<?php

namespace App\Services\Aws;

use Aws\Sdk;

class Factory
{
    public static function createAwsClient()
    {
        $sdk = new Sdk([
            'credentials' => array(
                'key' => $_ENV['aws_access_key_id'],
                'secret' => $_ENV['aws_secret_access_key'],
            ),
            'region' => $_ENV['aws_region'],
            'version' => 'latest',
            'DynamoDb' => [
                'region' => $_ENV['aws_region']
            ]
        ]);
        return $sdk;
    }

    public static function createDynamodb()
    {
        return self::createAwsClient()->createDynamoDb();
    }

    public static function createSes()
    {
    }
}
