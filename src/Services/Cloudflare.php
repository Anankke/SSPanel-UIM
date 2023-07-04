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
    public static function modifyRecord(DNS $dns, $zoneID, $recordID, $name, $content, $proxied = false): int
    {
        $details = ['type' => 'A', 'name' => $name, 'content' => $content, 'proxied' => $proxied];
        if ($dns->updateRecordDetails($zoneID, $recordID, $details)->success) {
            return 1;
        }
        return 0;
    }

    public static function addRecord(DNS $dns, $zoneID, $type, $name, $content, $ttl = 120, $proxied = false): int
    {
        if ($dns->addRecord($zoneID, $type, $name, $content, $ttl, $proxied)) {
            return 1;
        }
        return 0;
    }

    /**
     * @throws EndpointException
     */
    public static function updateRecord($name, $content, $proxied = false): void
    {
        $key = new APIKey($_ENV['cloudflare_email'], $_ENV['cloudflare_key']);
        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);
        $zoneID = null;

        $zoneID = $zones->getZoneID($_ENV['cloudflare_name']);

        $dns = new DNS($adapter);

        $r = $dns->listRecords($zoneID, '', $name);
        $recordCount = $r->result_info->count;
        $records = $r->result;

        if ($recordCount === 0) {
            self::addRecord($dns, $zoneID, 'A', $name, $content);
        } elseif ($recordCount >= 1) {
            foreach ($records as $record) {
                $recordID = $record->id;
                self::modifyRecord($dns, $zoneID, $recordID, $name, $content, $proxied);
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
        $s3 = self::initR2();

        try {
            $s3->upload(
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
        $s3 = self::initR2();

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $_ENV['r2_bucket_name'],
            'Key' => $fileName,
        ]);

        return (string) $s3->createPresignedRequest(
            $cmd,
            '+' . $_ENV['r2_client_download_timeout'] . ' minutes'
        )->getUri();
    }
}
