<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/10
 * Time: 上午9:54
 */

namespace App\Utils;

use App\Services\Config;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;

class CloudflareDriver
{

    // @todo: parameters
    public static function modifyRecord(DNS $dns, $zoneID, $recordID, $name, $content, $proxied = false)
    {
        $details = ['type' => 'A', 'name' => $name, 'content' => $content, 'proxied' => $proxied];
        if ($dns->updateRecordDetails($zoneID, $recordID, $details)->success == true) {
            return 1;
        }
        return 0;
    }

    public static function addRecord(DNS $dns, $zoneID, $type, $name, $content, $ttl = 120, $proxied = false)
    {
        if ($dns->addRecord($zoneID, $type, $name, $content, $ttl, $proxied) == true) {
            return 1;
        }
        return 0;
    }

    public static function updateRecord($name, $content, $proxied = false)
    {
        $key = new APIKey(Config::get('cloudflare_email'), Config::get('cloudflare_key'));
        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);

        $zoneID = $zones->getZoneID(Config::get('cloudflare_name'));

        $dns = new DNS($adapter);

        $r = $dns->listRecords($zoneID, '', $name);
        $recordCount = $r->result_info->count;
        $records = $r->result;

        if ($recordCount == 0) {
            self::addRecord($dns, $zoneID, 'A', $name, $content);
        } elseif ($recordCount >= 1) {
            foreach ($records as $record) {
                $recordID = $record->id;
                self::modifyRecord($dns, $zoneID, $recordID, $name, $content, $proxied);
            }
        }
    }
}
