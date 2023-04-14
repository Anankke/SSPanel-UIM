<?php

declare(strict_types=1);

namespace App\Utils;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use const BASE_PATH;

final class GeoIP2
{
    private Reader $city_reader;
    private Reader $country_reader;

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        $this->city_reader = new Reader(BASE_PATH . '/storage/GeoLite2-City/GeoLite2-City.mmdb');
        $this->country_reader = new Reader(BASE_PATH . '/storage/GeoLite2-Country/GeoLite2-Country.mmdb');
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCity(string $ip): ?string
    {
        $record = $this->city_reader->city($ip);
        return $record->city->names[$_ENV['geoip_locale']] ?? $record->city->name;
    }

    /**
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getCountry(string $ip): ?string
    {
        $record = $this->country_reader->country($ip);
        return $record->country->names[$_ENV['geoip_locale']] ?? $record->country->name;
    }
}
