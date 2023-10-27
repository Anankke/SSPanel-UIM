<?php

declare(strict_types=1);

namespace App\Services;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\Zones;
use Exception;

final class Cloudflare
{
    /**
     * @throws EndpointException
     */
    public static function updateRecord($name, $content, $proxied = false): void
    {
        $key = new APIKey($_ENV['cloudflare_email'], $_ENV['cloudflare_key']);
        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);
        $dns = new DNS($adapter);

        $zoneID = $zones->getZoneID($_ENV['cloudflare_name']);
        $r = $dns->listRecords($zoneID, '', $name);
        $recordCount = $r->result_info->count;
        $records = $r->result;

        if ($recordCount === 0) {
            $dns->addRecord($zoneID, $type, $name, $content, $ttl, $proxied);
        } elseif ($recordCount >= 1) {
            foreach ($records as $record) {
                $recordID = $record->id;
                $details = ['type' => 'A', 'name' => $name, 'content' => $content, 'proxied' => $proxied];
                $dns->updateRecordDetails($zoneID, $recordID, $details);
            }
        }
    }

    public static function initR2(): S3Client
    {
        $credentials = new Credentials($_ENV['r2_access_key_id'], $_ENV['r2_access_key_secret']);

        $options = [
            'region' => 'auto',
            'endpoint' => 'https://' . $_ENV['r2_account_id'] . '.r2.cloudflarestorage.com',
            'version' => 'latest',
            'credentials' => $credentials,
        ];

        return new S3Client($options);
    }

    public static function uploadR2($name, $file): void
    {
        $r2 = self::initR2();

        try {
            $r2->upload(
                $_ENV['r2_bucket_name'],
                $name,
                $file,
            );
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    public static function genR2PresignedUrl($fileName): string
    {
        $r2 = self::initR2();

        $cmd = $r2->getCommand('GetObject', [
            'Bucket' => $_ENV['r2_bucket_name'],
            'Key' => $fileName,
        ]);

        return (string) $r2->createPresignedRequest(
            $cmd,
            '+' . $_ENV['r2_client_download_timeout'] . ' minutes'
        )->getUri();
    }
}
