<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Node;
use App\Services\Subscribe\Clash;
use App\Services\Subscribe\Json;
use App\Services\Subscribe\SingBox;
use App\Services\Subscribe\SIP002;
use App\Services\Subscribe\SIP008;
use App\Services\Subscribe\SS;
use App\Services\Subscribe\Trojan;
use App\Services\Subscribe\V2Ray;

final class Subscribe
{
    /**
     * @param $user
     *
     * @return mixed
     */
    public static function getSubNodes($user): mixed
    {
        return Node::where('type', 1)
            ->where('node_class', '<=', $user->class)
            ->whereIn('node_group', [0, $user->node_group])
            ->where(static function ($query): void {
                $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
            })
            ->orderBy('node_class')
            ->orderBy('name')
            ->get();
    }

    public static function getClient($type): Json|SS|SIP002|V2Ray|Trojan|Clash|SIP008|SingBox
    {
        return match ($type) {
            'ss' => new SS(),
            'sip002' => new SIP002(),
            'v2ray' => new V2Ray(),
            'trojan' => new Trojan(),
            'clash' => new Clash(),
            'sip008' => new SIP008(),
            'singbox' => new SingBox(),
            default => new Json(),
        };
    }

    public static function getContent($user, $type): string
    {
        return self::getClient($type)->getContent($user);
    }
}
