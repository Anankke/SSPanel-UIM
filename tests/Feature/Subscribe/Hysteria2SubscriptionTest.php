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
use App\Services\Subscribe\Hysteria2;

function hy2SubCreateUser(): User
{
    $suffix = bin2hex(random_bytes(6));
    $user = new User();
    $user->email = "hy2sub{$suffix}@example.invalid";
    $user->user_name = "hy2sub{$suffix}";
    $user->pass = password_hash('password', PASSWORD_DEFAULT);
    $user->api_token = bin2hex(random_bytes(32));
    $user->port = random_int(20000, 60000);
    $user->passwd = bin2hex(random_bytes(16));
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

function hy2SubCreateNode(string $name, int $sort, string $server, string $customConfig): Node
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

function hy2SubFind(array $items, string $key, string $value): array
{
    foreach ($items as $item) {
        if (($item[$key] ?? null) === $value) {
            return $item;
        }
    }

    throw new RuntimeException("No item with {$key}={$value} in the rendered subscription.");
}

function hy2SubValidCustomConfig(): string
{
    return json_encode([
        'offset_port_node' => '8443',
        'host' => 'hy.example.com',
        'allow_insecure' => false,
        'hysteria2' => [
            'version' => 2,
            'finalmask' => [
                'udp' => [[
                    'type' => 'salamander',
                    'settings' => ['password' => 'obfs-secret'],
                ]],
                'quicParams' => ['brutalUp' => '60 mbps'],
            ],
            'portHopping' => ['enabled' => true, 'ports' => '20000-30000'],
        ],
    ]);
}

beforeEach(function () {
    $this->hy2EnvBackup = $_ENV;
    // The subscription templates are not part of the test config, load the shipped example profile.
    require BASE_PATH . '/config/appprofile.example.php';

    $this->hy2Nodes = [];
    $this->hy2User = hy2SubCreateUser();

    // V2Ray subscriptions are gated by this switch, make sure the row exists and is on.
    $this->hy2ConfigRow = Config::where('item', 'enable_v2_sub')->first();
    $this->hy2ConfigCreated = false;
    $this->hy2ConfigOriginal = null;
    if ($this->hy2ConfigRow === null) {
        $row = new Config();
        $row->item = 'enable_v2_sub';
        $row->value = '1';
        $row->class = 'subscribe';
        $row->is_public = 1;
        $row->type = 'bool';
        $row->default = '1';
        $row->mark = 'V2Ray 订阅开关';
        $row->save();
        $this->hy2ConfigRow = $row;
        $this->hy2ConfigCreated = true;
    } else {
        $this->hy2ConfigOriginal = $this->hy2ConfigRow->value;
        $this->hy2ConfigRow->value = '1';
        $this->hy2ConfigRow->save();
    }
});

afterEach(function () {
    foreach ($this->hy2Nodes as $node) {
        $node->delete();
    }
    $this->hy2User->delete();

    if ($this->hy2ConfigCreated) {
        $this->hy2ConfigRow->delete();
    } else {
        $this->hy2ConfigRow->value = $this->hy2ConfigOriginal;
        $this->hy2ConfigRow->save();
    }

    $_ENV = $this->hy2EnvBackup;
});

describe('Hysteria2 subscription output', function () {
    beforeEach(function () {
        $this->hy2Name = 'hy2-render-' . bin2hex(random_bytes(4));
        $this->hy2Nodes[] = hy2SubCreateNode($this->hy2Name, 15, 'hy2.example.com', hy2SubValidCustomConfig());
        $this->hy2Uri = 'hysteria2://' . $this->hy2User->uuid . '@hy2.example.com:8443/?sni=hy.example.com'
            . '&obfs=salamander&obfs-password=obfs-secret&mport=20000-30000#' . $this->hy2Name;
    });

    it('renders a Clash/Mihomo hysteria2 proxy', function () {
        $proxies = yaml_parse(Subscribe::getContent($this->hy2User, 'clash'))['proxies'];
        $proxy = hy2SubFind($proxies, 'name', $this->hy2Name);

        expect($proxy)
            ->toMatchArray([
                'type' => 'hysteria2',
                'server' => 'hy2.example.com',
                'port' => 8443,
                'password' => $this->hy2User->uuid,
                'sni' => 'hy.example.com',
                'obfs' => 'salamander',
                'obfs-password' => 'obfs-secret',
                'ports' => '20000-30000',
                'up' => '60 mbps',
            ])
            ->not->toHaveKey('down');
    });

    it('renders a sing-box hysteria2 outbound', function () {
        $outbounds = json_decode(Subscribe::getContent($this->hy2User, 'singbox'), true)['outbounds'];
        $outbound = hy2SubFind($outbounds, 'tag', $this->hy2Name);

        expect($outbound)
            ->toMatchArray([
                'type' => 'hysteria2',
                'server' => 'hy2.example.com',
                'password' => $this->hy2User->uuid,
                'obfs' => ['type' => 'salamander', 'password' => 'obfs-secret'],
                'server_ports' => ['20000:30000'],
            ])
            ->not->toHaveKey('server_port')
            ->and($outbound['tls']['server_name'])->toBe('hy.example.com')
            ->and($outbound['up_mbps'])->toEqual(60);
    });

    it('renders a hysteria2 share link in the V2Ray subscription', function () {
        $lines = array_filter(explode(PHP_EOL, Subscribe::getContent($this->hy2User, 'v2ray')));

        expect($lines)->toContain($this->hy2Uri);
    });

    it('renders only the URI line in the hysteria2 subscription', function () {
        expect(Subscribe::getClient('hysteria2'))->toBeInstanceOf(Hysteria2::class)
            ->and(Subscribe::getContent($this->hy2User, 'hysteria2'))->toBe($this->hy2Uri . PHP_EOL);
    });

    it('does not render an outbound in the V2RayJson subscription', function () {
        $content = Subscribe::getContent($this->hy2User, 'v2rayjson');

        expect(json_decode($content, true))->toBeArray()
            ->and($content)->not->toContain($this->hy2Name);
    });
});

describe('Hysteria2 subscription with unusable nodes', function () {
    beforeEach(function () {
        $suffix = bin2hex(random_bytes(4));
        $this->hy2Valid = 'hy2-valid-' . $suffix;
        $this->hy2Trojan = 'hy2-trojan-' . $suffix;
        $this->hy2Vmess = 'hy2-vmess-' . $suffix;
        $this->hy2Broken = ['hy2-broken-string-' . $suffix, 'hy2-broken-nohy2-' . $suffix];

        $this->hy2Nodes[] = hy2SubCreateNode($this->hy2Valid, 15, 'hy2.example.com', hy2SubValidCustomConfig());
        $this->hy2Nodes[] = hy2SubCreateNode($this->hy2Broken[0], 15, 'broken1.example.com', '"abc"');
        $this->hy2Nodes[] = hy2SubCreateNode(
            $this->hy2Broken[1],
            15,
            'broken2.example.com',
            '{"offset_port_node":"443","host":"broken2.example.com"}'
        );
        $this->hy2Nodes[] = hy2SubCreateNode(
            $this->hy2Trojan,
            14,
            'trojan.example.com',
            '{"offset_port_node":"443","host":"trojan.example.com"}'
        );
        $this->hy2Nodes[] = hy2SubCreateNode(
            $this->hy2Vmess,
            11,
            'vmess.example.com',
            '{"offset_port_node":"443","security":"tls","network":"ws","host":"vmess.example.com","path":"/"}'
        );
    });

    it('skips only the broken node in the Clash subscription', function () {
        $content = Subscribe::getContent($this->hy2User, 'clash');
        $names = array_column(yaml_parse($content)['proxies'], 'name');

        expect($names)->toContain($this->hy2Valid, $this->hy2Trojan, $this->hy2Vmess)
            ->and($content)->not->toContain($this->hy2Broken[0])
            ->and($content)->not->toContain($this->hy2Broken[1]);
    });

    it('skips only the broken node in the sing-box subscription', function () {
        $content = Subscribe::getContent($this->hy2User, 'singbox');
        $tags = array_column(json_decode($content, true)['outbounds'], 'tag');

        expect($tags)->toContain($this->hy2Valid, $this->hy2Trojan, $this->hy2Vmess)
            ->and($content)->not->toContain($this->hy2Broken[0])
            ->and($content)->not->toContain($this->hy2Broken[1]);
    });

    it('skips only the broken node in the V2Ray subscription', function () {
        $content = Subscribe::getContent($this->hy2User, 'v2ray');
        $lines = array_filter(explode(PHP_EOL, $content));
        $vmessNames = [];
        foreach ($lines as $line) {
            if (str_starts_with($line, 'vmess://')) {
                $vmessNames[] = json_decode(base64_decode(substr($line, 8)), true)['ps'];
            }
        }

        expect($content)->toContain('#' . $this->hy2Valid)
            ->and($vmessNames)->toContain($this->hy2Vmess)
            ->and($content)->not->toContain($this->hy2Broken[0])
            ->and($content)->not->toContain($this->hy2Broken[1]);
    });

    it('skips only the broken node in the hysteria2 subscription', function () {
        $content = Subscribe::getContent($this->hy2User, 'hysteria2');
        $lines = array_filter(explode(PHP_EOL, $content));

        expect($lines)->toHaveCount(1)
            ->and(array_values($lines)[0])->toEndWith('#' . $this->hy2Valid)
            ->and($content)->not->toContain($this->hy2Broken[0])
            ->and($content)->not->toContain($this->hy2Broken[1]);
    });
});
