<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function substr;

/**
 * @property int    $id         记录ID
 * @property int    $user_id    用户ID
 * @property string $ip         IP地址
 * @property int    $node_id    节点ID
 * @property int    $first_time 首次在线时间
 * @property int    $last_time  最后在线时间
 *
 * @mixin Builder
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

    public function nodeName(): string
    {
        return (new Node())->where('id', $this->node_id)->value('name');
    }
}
