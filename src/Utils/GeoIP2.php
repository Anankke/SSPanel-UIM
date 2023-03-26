<?php

declare(strict_types=1);

namespace App\Utils;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

final class GeoIP2
{
    private Reader $reader;
    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        $this->reader = new Reader(BASE_PATH . '/storage/GeoLite2-City/GeoLite2-City.mmdb');
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCity(string $ip): ?string
    {
        $record = $this->reader->city($ip);
        return $record->city->name;
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ip): ?string
    {
        $record = $this->reader->country($ip);
        return $record->country->name;
    }
}
