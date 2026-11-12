<?php

declare(strict_types=1);

/**
 * @group database
 * @group feature
 * @group subscribe
 */

use App\Models\Config;
use App\Models\Node;
use App\Models\User;
use App\Services\Subscribe;

/** Standard Base64 of "aes-256-gcm:" . SS_SUB_PASSWD contains both "/" and "+", so the URL-safe alphabet is exercised. */
const SS_SUB_PASSWD = '?>?~~~fixedPasswd01';
const SS_SUB_USERINFO = 'YWVzLTI1Ni1nY206Pz4_fn5-Zml4ZWRQYXNzd2QwMQ';

function ssSubCreateUser(): User
{
    $suffix = bin2hex(random_bytes(6));
    $user = new User();
    $user->email = "sssub{$suffix}@example.invalid";
    $user->user_name = "sssub{$suffix}";
    $user->pass = password_hash('password', PASSWORD_DEFAULT);
    $user->api_token = bin2hex(random_bytes(32));
    $user->port = random_int(20000, 60000);
    $user->passwd = SS_SUB_PASSWD;
    $user->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $user->method = 'aes-256-gcm';
    $user->transfer_enable = 1099511627776;
    $user->u = 0;
    $user->d = 0;
    $user->node_iplimit = 0;
    $user->node_speedlimit = 0;
    $user->node_group = 0;
    $user->class = 0;
    $user->is_banned = 0;
    $user->class_expire = date('Y-m-d H:i:s', strtotime('+1 year'));
    $user->save();

    return $user;
}

function ssSubCreateNode(string $name, string $server, string $customConfig, int $sort = 0): Node
{
    $node = new Node();
    $node->name = $name;
    $node->server = $server;
    $node->password = bin2hex(random_bytes(32));
    $node->type = 1;
    $node->sort = $sort;
    $node->node_class = 0;
    $node->node_group = 0;
    $node->custom_config = $customConfig;
    $node->save();

    return $node;
}

/** Returns the subscription line whose fragment is the percent-encoded node name. */
function ssSubLine(string $content, string $name): string
{
    foreach (array_filter(explode(PHP_EOL, $content)) as $line) {
        if (str_ends_with($line, '#' . rawurlencode($name))) {
            return $line;
        }
    }

    throw new RuntimeException("No link named {$name} in the subscription.");
}

beforeEach(function () {
    $this->ssNodes = [];
    $this->ssUser = ssSubCreateUser();
    $suffix = bin2hex(random_bytes(4));
    $this->ssPlain = 'SS 节点 #1-' . $suffix;
    $this->ssObfs = 'ss-obfs-' . $suffix;
    $this->ssPluginOnly = 'ss-plugin-only-' . $suffix;
    $this->ssIpv6 = 'ss-ipv6-' . $suffix;
    $this->ssVmess = 'ss-vmess-' . $suffix;

    $this->ssNodes[] = ssSubCreateNode($this->ssPlain, 'ss.example.com', '{}');
    $this->ssNodes[] = ssSubCreateNode(
        $this->ssObfs,
        'obfs.example.com',
        '{"plugin":"obfs-local","plugin_option":"obfs=http;obfs-host=www.example.com"}'
    );
    $this->ssNodes[] = ssSubCreateNode($this->ssPluginOnly, 'plugin.example.com', '{"plugin":"v2ray-plugin"}');
    $this->ssNodes[] = ssSubCreateNode($this->ssIpv6, '2001:db8::1', '{}');
    $this->ssNodes[] = ssSubCreateNode(
        $this->ssVmess,
        'vmess.example.com',
        '{"offset_port_node":"443","network":"ws","security":"tls","host":"vmess.example.com"}',
        11
    );

    $this->ssConfigRow = Config::where('item', 'enable_ss_sub')->first();
    $this->ssConfigCreated = false;
    $this->ssConfigOriginal = null;
    if ($this->ssConfigRow === null) {
        $row = new Config();
        $row->item = 'enable_ss_sub';
        $row->value = '1';
        $row->class = 'subscribe';
        $row->is_public = 1;
        $row->type = 'bool';
        $row->default = '1';
        $row->mark = 'SS 订阅开关';
        $row->save();
        $this->ssConfigRow = $row;
        $this->ssConfigCreated = true;
    } else {
        $this->ssConfigOriginal = $this->ssConfigRow->value;
        $this->ssConfigRow->value = '1';
        $this->ssConfigRow->save();
    }
});

afterEach(function () {
    foreach ($this->ssNodes as $node) {
        $node->delete();
    }
    $this->ssUser->delete();

    if ($this->ssConfigCreated) {
        $this->ssConfigRow->delete();
    } else {
        $this->ssConfigRow->value = $this->ssConfigOriginal;
        $this->ssConfigRow->save();
    }
});

describe('SS subscription output', function () {
    it('renders ss:// URIs with an unpadded URL-safe Base64 body and a percent-encoded tag', function () {
        $content = Subscribe::getContent($this->ssUser, 'ss');
        $port = $this->ssUser->port;

        $line = ssSubLine($content, $this->ssPlain);
        $body = substr($line, strlen('ss://'), -strlen('#' . rawurlencode($this->ssPlain)));

        expect($line)->toStartWith('ss://')
            ->and($line)->toEndWith('#SS%20%E8%8A%82%E7%82%B9%20%231-' . substr($this->ssPlain, -8))
            ->and($body)->toMatch('/^[A-Za-z0-9_-]+$/')
            ->and(base64_decode(strtr($body, '-_', '+/'), true))
            ->toBe('aes-256-gcm:' . SS_SUB_PASSWD . '@ss.example.com:' . $port);

        expect($content)->not->toContain($this->ssVmess)
            ->and(count(array_filter(explode(PHP_EOL, trim($content)), static fn ($l) => ! str_starts_with($l, 'ss://'))))->toBe(0);
    });

    it('renders SIP002 ss:// URIs', function () {
        $content = Subscribe::getContent($this->ssUser, 'sip002');
        $port = $this->ssUser->port;

        expect(ssSubLine($content, $this->ssPlain))
            ->toBe('ss://' . SS_SUB_USERINFO . '@ss.example.com:' . $port . '#' . rawurlencode($this->ssPlain))
            ->and(ssSubLine($content, $this->ssObfs))
            ->toBe('ss://' . SS_SUB_USERINFO . '@obfs.example.com:' . $port
                . '/?plugin=obfs-local%3Bobfs%3Dhttp%3Bobfs-host%3Dwww.example.com#' . $this->ssObfs)
            ->and(ssSubLine($content, $this->ssPluginOnly))
            ->toBe('ss://' . SS_SUB_USERINFO . '@plugin.example.com:' . $port . '/?plugin=v2ray-plugin#' . $this->ssPluginOnly)
            ->and(ssSubLine($content, $this->ssIpv6))
            ->toBe('ss://' . SS_SUB_USERINFO . '@[2001:db8::1]:' . $port . '#' . $this->ssIpv6);

        $parts = parse_url(ssSubLine($content, $this->ssObfs));
        parse_str($parts['query'], $query);
        expect($parts)->toMatchArray(['scheme' => 'ss', 'host' => 'obfs.example.com', 'port' => $port, 'path' => '/'])
            ->and(base64_decode(strtr($parts['user'], '-_', '+/'), true))->toBe('aes-256-gcm:' . SS_SUB_PASSWD)
            ->and($query)->toBe(['plugin' => 'obfs-local;obfs=http;obfs-host=www.example.com']);

        expect($content)->not->toContain($this->ssVmess);
    });
});
