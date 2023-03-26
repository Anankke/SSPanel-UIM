<?php

declare(strict_types=1);

namespace App\Models;

use function substr;

/**
 * Online Log
 *
 * @property int    $id         INT UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY
 * @property int    $user_id    INT UNSIGNED NOT NULL, UNIQUE KEY(A0)
 * @property string $ip         INET6 NOT NULL, UNIQUE KEY(A1) \
 *      Human readable IPv6 address. \
 *      IPv4 Address would be IPv4-mapped IPv6 Address like `::ffff:1.1.1.1`.
 * @property int    $node_id    INT UNSIGNED NOT NULL
 * @property int    $first_time INT UNSIGNED NOT NULL \
 *      The time when $ip fisrt time connect.
 * @property int    $last_time  INT UNSIGNED NOT NULL \
 *      The time when $ip last time connect.
 *
 * @see https://mariadb.com/kb/en/inet6/ MariaDB INET6 data type
 * @see https://www.rfc-editor.org/rfc/rfc4291.html#section-2.5.5.2 IPv4-mapped IPv6 Address
 */
final class OnlineLog extends Model
{
    protected $connection = 'default';

    protected $table = 'online_log';

    /**
     * Get human-readable IPv4 or IPv6 address
     *
     * @return string Example: IPv4 Address: `1.1.1.1`; IPv6 Address: `2606:4700:4700::1111`
     */
    public function ip(): string
    {
        $ip = $this->attributes['ip'];
        if (substr($ip, 0, 7) === '::ffff:') {
            return substr($ip, 6);
        }
        return $ip;
    }
}
