<?php

namespace App\Services\Aws;

use Aws\Sdk;
use App\Services\Config;

class Factory
{
    public static function createAwsClient()
    {
        $sdk = new Sdk([
            'credentials' => array(
                'key' => Config::get('aws_access_key_id'),
                'secret' => Config::get('aws_secret_access_key'),
            ),
            'region' => Config::get('aws_region'),
            'version' => 'latest',
            'DynamoDb' => [
                'region' => Config::get('aws_region')
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
