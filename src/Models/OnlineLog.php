<?php

declare(strict_types=1);

namespace App\Models;

use function substr;

/**
 * Online Log
 *
 * PRIMARY KEY (id) \
 * UNIQUE KEY (user_id, ip) \
 * KEY (last_time)
 *
 * @property int    $id         INT UNSIGNED NOT NULL AUTO_INCREMENT
 * @property int    $user_id    INT UNSIGNED NOT NULL
 * @property string $ip         INET6 NOT NULL \
 *      Human-readable IPv6 address. \
 *      IPv4 Address would be IPv4-mapped IPv6 Address like `::ffff:1.1.1.1`.
 * @property int    $node_id    INT UNSIGNED NOT NULL
 * @property int    $first_time INT UNSIGNED NOT NULL \
 *      The time when $ip fisrt being seen.
 * @property int    $last_time  INT UNSIGNED NOT NULL \
 *      The time when $ip last being seen.
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
     * Unlike `$this->ip`, this method would convert IPv4-mapped IPv6 Address to IPv4 Address.
     *
     * @return string Example: IPv4 Address: `1.1.1.1`; IPv6 Address: `2606:4700:4700::1111`
     */
    public function ip(): string
    {
        $ip = $this->attributes['ip'];
        if (str_starts_with($ip, '::ffff:')) {
            $v4 = substr($ip, 7);
            // Mix hexadecimal and dot decimal notations: https://www.rfc-editor.org/rfc/rfc5952#section-5
            //
            // IPv4-translated address: https://www.rfc-editor.org/rfc/rfc2765.html#section-2.1
            if (str_contains($v4, '.') && ! str_contains($v4, ':')) {
                return $v4;
            }
        }
        return $ip;
    }
}
