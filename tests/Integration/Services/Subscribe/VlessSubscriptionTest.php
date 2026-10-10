<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Subscribe;

use App\Models\Node;
use App\Services\Subscribe;
use App\Controllers\WebAPI\NodeController;
use App\Controllers\WebAPI\UserController;
use App\Controllers\SubController;
use GuzzleHttp\Psr7\HttpFactory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class VlessSubscriptionTest extends TestCase
{
    private const USER_UUID = '123e4567-e89b-42d3-a456-426614174000';

    private object $user;

    private array $previousEnv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousEnv = $_ENV;

        $database = new Capsule();
        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->setAsGlobal();
        $database->bootEloquent();
        $database->schema()->create('node', static function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('type');
            $table->integer('sort');
            $table->string('name');
            $table->string('server');
            $table->text('custom_config');
            $table->integer('node_class');
            $table->integer('node_group');
            $table->integer('node_bandwidth_limit');
            $table->integer('node_bandwidth');
            $table->integer('node_speedlimit')->default(0);
            $table->integer('node_heartbeat')->default(0);
        });
        $database->schema()->create('user', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('class')->default(0);
            $table->integer('node_group')->default(0);
            $table->integer('is_admin')->default(0);
            $table->integer('is_banned')->default(0);
            $table->string('class_expire');
            $table->integer('u')->default(0);
            $table->integer('d')->default(0);
            $table->integer('transfer_enable')->default(1000000);
            $table->integer('node_speedlimit')->default(0);
            $table->integer('node_iplimit')->default(0);
            $table->string('method')->default('aes-128-gcm');
            $table->integer('port')->default(12345);
            $table->string('passwd')->default('unused');
        });
        $database->schema()->create('online_log', static function (Blueprint $table): void {
            $table->integer('user_id');
            $table->integer('last_time');
        });
        $database->schema()->create('link', static function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('userid');
            $table->string('token');
        });
        $database->table('user')->insert(['id' => 1, 'uuid' => self::USER_UUID, 'class_expire' => '2099-01-01 00:00:00']);
        $database->table('link')->insert(['userid' => 1, 'token' => 'vless-test-token']);
        $database->schema()->create('config', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('item');
            $table->text('value');
            $table->string('type');
        });
        $database->table('config')->insert(['item' => 'enable_v2_sub', 'value' => '1', 'type' => 'bool']);

        $this->user = (object) [
            'uuid' => self::USER_UUID,
            'class' => 0,
            'node_group' => 0,
            'is_admin' => false,
        ];

        $_ENV['Clash_Config'] = [];
        $_ENV['Clash_Group_Indexes'] = [];
        $_ENV['Clash_Group_Config'] = ['proxy-groups' => []];
        $_ENV['SingBox_Config'] = [
            'outbounds' => [
                ['type' => 'selector', 'tag' => 'select', 'outbounds' => ['auto']],
                ['type' => 'urltest', 'tag' => 'auto', 'outbounds' => []],
            ],
            'experimental' => ['cache_file' => []],
        ];
        $_ENV['V2RayJson_Config'] = ['outbounds' => []];
        $_ENV['appName'] = 'test';
        $_ENV['Subscribe'] = true;
        $_ENV['subUrl'] = 'https://panel.example.com';
        $_ENV['baseUrl'] = 'https://panel.example.com';
        $_ENV['enable_rate_limit'] = false;
        $_ENV['keep_connect'] = false;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->previousEnv;
        parent::tearDown();
    }

    #[DataProvider('vmessFlags')]
    public function testLegacySort11RemainsVmess(array $flag): void
    {
        $this->insertNode($flag + ['offset_port_node' => '443', 'offset_port_user' => '8443', 'network' => 'tcp']);

        self::assertStringStartsWith('vmess://', Subscribe::getContent($this->user, 'v2ray'));
        self::assertSame('vmess', $this->clashNode()['type']);
        self::assertSame('vmess', $this->singBoxNode()['type']);
        self::assertSame('vmess', $this->xrayNode()['protocol']);
    }

    public static function vmessFlags(): array
    {
        return [
            'missing' => [[]],
            'zero integer' => [['enable_vless' => 0]],
            'zero string' => [['enable_vless' => '0']],
            'false' => [['enable_vless' => false]],
        ];
    }

    #[DataProvider('vlessFlags')]
    public function testVlessTcpUsesUserPortAndUuid(mixed $flag): void
    {
        $this->insertNode([
            'enable_vless' => $flag,
            'offset_port_node' => '443',
            'offset_port_user' => '8443',
            'network' => 'tcp',
            'security' => 'none',
        ]);

        $uri = trim(Subscribe::getContent($this->user, 'v2ray'));
        self::assertStringStartsWith('vless://' . self::USER_UUID . '@node.example.com:8443?', $uri);
        self::assertStringContainsString('encryption=none&security=none&type=tcp', $uri);

        $clash = $this->clashNode();
        self::assertSame('vless', $clash['type']);
        self::assertSame(8443, $clash['port']);
        self::assertSame(self::USER_UUID, $clash['uuid']);

        $singBox = $this->singBoxNode();
        self::assertSame('vless', $singBox['type']);
        self::assertSame(8443, $singBox['server_port']);
        self::assertSame(self::USER_UUID, $singBox['uuid']);
        self::assertArrayNotHasKey('alter_id', $singBox);

        $xray = $this->xrayNode();
        self::assertSame('vless', $xray['protocol']);
        self::assertSame(8443, $xray['settings']['vnext'][0]['port']);
        self::assertSame(self::USER_UUID, $xray['settings']['vnext'][0]['users'][0]['id']);
        self::assertSame('none', $xray['settings']['vnext'][0]['users'][0]['encryption']);
    }

    public static function vlessFlags(): array
    {
        return ['string' => ['1'], 'integer' => [1], 'boolean' => [true]];
    }

    public function testVisionWithoutRealityAndNodePortFallback(): void
    {
        $this->insertNode([
            'enable_vless' => '1',
            'offset_port_node' => '9443',
            'network' => 'tcp',
            'security' => 'tls',
            'host' => 'tls.example.com',
            'flow' => 'xtls-rprx-vision',
        ]);

        $uri = Subscribe::getContent($this->user, 'v2ray');
        self::assertStringContainsString('@node.example.com:9443?', $uri);
        self::assertStringContainsString('security=tls&type=tcp&flow=xtls-rprx-vision&sni=tls.example.com', $uri);
        self::assertSame(9443, $this->clashNode()['port']);
        self::assertSame('xtls-rprx-vision', $this->clashNode()['flow']);
        self::assertSame(9443, $this->singBoxNode()['server_port']);
        self::assertSame('tls.example.com', $this->singBoxNode()['tls']['server_name']);
        self::assertSame(9443, $this->xrayNode()['settings']['vnext'][0]['port']);
        self::assertSame('tls', $this->xrayNode()['streamSettings']['security']);
    }

    public function testVisionRealityUsesDerivedPublicKeyWithoutLeakingPrivateKey(): void
    {
        $private = str_repeat("\x11", 32);
        $privateKey = rtrim(strtr(base64_encode($private), '+/', '-_'), '=');
        $publicKey = rtrim(strtr(base64_encode(sodium_crypto_scalarmult_base($private)), '+/', '-_'), '=');
        $this->insertNode([
            'enable_vless' => '1',
            'enable_reality' => true,
            'flow' => 'xtls-rprx-vision',
            'network' => 'tcp',
            'security' => 'reality',
            'offset_port_node' => '443',
            'offset_port_user' => '8443',
            'reality-opts' => [
                'dest' => 'cover.example.com:443',
                'server_names' => ['cover.example.com'],
                'private_key' => $privateKey,
                'short_ids' => ['0123456789abcdef'],
            ],
        ], '2001:db8::1', '测试 #1');

        $uri = trim(Subscribe::getContent($this->user, 'v2ray'));
        self::assertStringStartsWith('vless://' . self::USER_UUID . '@[2001:db8::1]:8443?', $uri);
        self::assertStringContainsString('security=reality&type=tcp&flow=xtls-rprx-vision', $uri);
        self::assertStringContainsString('sni=cover.example.com&fp=chrome&pbk=' . $publicKey . '&sid=0123456789abcdef', $uri);
        self::assertStringEndsWith('#%E6%B5%8B%E8%AF%95%20%231', $uri);

        $clash = $this->clashNode();
        self::assertSame('vless', $clash['type']);
        self::assertSame('xtls-rprx-vision', $clash['flow']);
        self::assertSame('cover.example.com', $clash['servername']);
        self::assertSame($publicKey, $clash['reality-opts']['public-key']);
        self::assertSame('0123456789abcdef', $clash['reality-opts']['short-id']);
        self::assertTrue($clash['reality-opts']['support-x25519mlkem768']);

        $singBox = $this->singBoxNode();
        self::assertSame('vless', $singBox['type']);
        self::assertSame('xtls-rprx-vision', $singBox['flow']);
        self::assertSame($publicKey, $singBox['tls']['reality']['public_key']);
        self::assertSame('0123456789abcdef', $singBox['tls']['reality']['short_id']);
        self::assertSame('chrome', $singBox['tls']['utls']['fingerprint']);

        $xray = $this->xrayNode();
        self::assertSame('vless', $xray['protocol']);
        self::assertSame('xtls-rprx-vision', $xray['settings']['vnext'][0]['users'][0]['flow']);
        self::assertSame('reality', $xray['streamSettings']['security']);
        self::assertSame($publicKey, $xray['streamSettings']['realitySettings']['publicKey']);
        self::assertSame('0123456789abcdef', $xray['streamSettings']['realitySettings']['shortId']);

        foreach (['v2ray', 'clash', 'singbox', 'v2rayjson'] as $format) {
            $content = Subscribe::getContent($this->user, $format);
            self::assertStringNotContainsString($privateKey, $content);
            self::assertStringNotContainsString('private_key', $content);
            self::assertStringNotContainsString('cover.example.com:443', $content);
        }
    }

    public function testInvalidRealityNodeDoesNotInterruptHealthyNodes(): void
    {
        $this->insertNode(['enable_vless' => 1, 'network' => 'tcp', 'offset_port_node' => '443']);
        $this->insertNode([
            'enable_vless' => 1,
            'enable_reality' => true,
            'reality-opts' => [
                'server_names' => ['cover.example.com'],
                'short_ids' => ['0123456789abcdef'],
                'private_key' => 'bad-secret',
            ],
        ], 'bad.example.com', 'Bad');

        foreach (['v2ray', 'clash', 'singbox', 'v2rayjson'] as $format) {
            $content = Subscribe::getContent($this->user, $format);
            self::assertStringContainsString('node.example.com', $content);
            self::assertStringNotContainsString('bad.example.com', $content);
            self::assertStringNotContainsString('bad-secret', $content);
        }
    }

    #[DataProvider('networkAliases')]
    public function testNetworkAliasesRemainConsistent(?string $network, string $expected): void
    {
        $config = ['enable_vless' => 1, 'offset_port_node' => '443', 'path' => '/alias'];
        if ($network !== null) {
            $config['network'] = $network;
        }
        $this->insertNode($config);
        self::assertStringContainsString('type=' . $expected, Subscribe::getContent($this->user, 'v2ray'));
        self::assertSame($expected, $this->clashNode()['network']);
        self::assertSame($expected, $this->xrayNode()['streamSettings']['network']);
        if ($expected === 'ws') {
            self::assertSame('/alias', $this->xrayNode()['streamSettings']['wsSettings']['path']);
            self::assertSame('/alias', $this->singBoxNode()['transport']['path']);
        } elseif ($expected === 'tcp') {
            self::assertArrayNotHasKey('transport', $this->singBoxNode());
        }
    }

    public static function networkAliases(): array
    {
        return [[null, 'tcp'], ['', 'tcp'], ['raw', 'tcp'], ['websocket', 'ws'], ['WS', 'ws'], ['splithttp', 'xhttp']];
    }

    public function testAdminRejectsInvalidVlessConfigWithoutExposingSecret(): void
    {
        $controller = (new \ReflectionClass(\App\Controllers\Admin\NodeController::class))->newInstanceWithoutConstructor();
        $validate = new \ReflectionMethod($controller, 'validateCustomConfig');
        $config = ['enable_vless' => 1, 'offset_port_node' => '443', 'network' => 'websocket'];
        self::assertNull($validate->invoke($controller, 11, json_encode($config)));
        self::assertNotNull($validate->invoke($controller, 11, json_encode(array_replace($config, ['flow' => 'xtls-rprx-vision']))));
        self::assertNotNull($validate->invoke($controller, 11, json_encode(array_replace($config, ['offset_port_node' => '0']))));
        $invalid = array_replace($config, ['enable_reality' => true, 'network' => 'tcp',
            'reality-opts' => ['server_names' => ['cover.example.com'], 'short_ids' => ['0123456789abcdef'], 'private_key' => 'bad-secret']]);
        $error = $validate->invoke($controller, 11, json_encode($invalid));
        self::assertIsString($error);
        self::assertStringNotContainsString('bad-secret', $error);
    }

    #[DataProvider('emptyProfiles')]
    public function testFullProfilesAndEmptySingBoxRemainLoadable(bool $empty, bool $xhttp): void
    {
        if (! $empty) {
            $this->insertNode(['enable_vless' => 1, 'network' => $xhttp ? 'xhttp' : 'tcp', 'offset_port_node' => '443',
                'security' => 'tls', 'host' => 'tls.example.com']);
        }
        $_ENV += ['dns_type_853' => 'tls', 'dns_server_853' => '1.1.1.1', 'dns_server_port_853' => 853,
            'dns_type_443' => 'https', 'dns_server_443' => '1.1.1.1', 'dns_server_port_443' => 443,
            'dns_path_443' => '/dns-query', 'dns_select' => 'alidns', 'tcp_concurrent' => true,
            'jsdelivr_url' => 'https://cdn.jsdelivr.net'];
        require __DIR__ . '/../../../../config/appprofile.example.php';
        $singBox = Subscribe::getContent($this->user, 'singbox');
        $profile = json_decode($singBox, true, 512, JSON_THROW_ON_ERROR);
        self::assertNotEmpty($profile['outbounds'][1]['outbounds']);
        if ($empty || $xhttp) {
            self::assertSame('reject', $profile['route']['rules'][0]['action']);
            self::assertSame('selector', $profile['outbounds'][1]['type']);
            self::assertStringNotContainsString('"type":"vless"', $singBox);
        } else {
            self::assertSame('vless', $profile['outbounds'][3]['type']);
        }
        $directory = getenv('VLESS_CONTRACT_DIR');
        $profiles = $directory !== false && $directory !== '' ? $directory . '/profiles' : null;
        if ($profiles !== null && ! is_dir($profiles)) {
            mkdir($profiles, 0700);
        }
        if ($profiles !== null) {
            file_put_contents($profiles . '/singbox-' . ($empty ? 'empty' : ($xhttp ? 'xhttp-only' : 'tcp')) . '.json', $singBox);
        }
        if (! $empty && ! $xhttp) {
            $xray = Subscribe::getContent($this->user, 'v2rayjson');
            $config = json_decode($xray, true, 512, JSON_THROW_ON_ERROR);
            self::assertSame('error', $config['log']['loglevel']);
            self::assertTrue($config['inbounds'][0]['settings']['udp']);
            self::assertNotEmpty($config['dns']['servers']);
            if ($profiles !== null) {
                file_put_contents($profiles . '/xray-tcp.json', $xray);
                file_put_contents($profiles . '/mihomo-tcp.yaml', Subscribe::getContent($this->user, 'clash'));
            }
            $_ENV['V2RayJson_Config']['log'] = ['error' => ['level' => 'error', 'type' => 'console'], 'access' => ['type' => 'none']];
            $_ENV['V2RayJson_Config']['dns'] = ['nameServer' => [['address' => '1.1.1.1']]];
            $_ENV['V2RayJson_Config']['inbounds'][0]['settings'] = ['udpEnabled' => true];
            $_ENV['V2RayJson_Config']['inbounds'][1]['settings'] = [];
            $legacy = Subscribe::getContent($this->user, 'v2rayjson');
            self::assertSame('none', json_decode($legacy, true)['log']['access']);
            if ($profiles !== null) {
                file_put_contents($profiles . '/xray-legacy-template.json', $legacy);
            }
        }
    }

    public static function emptyProfiles(): array
    {
        return [[false, false], [false, true], [true, false]];
    }

    #[DataProvider('transports')]
    public function testRealNodeAndUserApiToSubscriptionContract(string $network, string $security, string $flow): void
    {
        if (! defined('VERSION')) {
            define('VERSION', '26.9.0');
        }
        $custom = [
            'enable_vless' => 1, 'network' => $network, 'security' => $security, 'flow' => $flow,
            'offset_port_node' => '443', 'offset_port_user' => '443',
            'host' => 'cover.example.com', 'path' => '/vless-test', 'servicename' => 'vless-test',
        ];
        if ($security === 'reality') {
            $custom['enable_reality'] = true;
            $custom['reality-opts'] = [
                'dest' => 'cover.example.com:443', 'server_names' => ['cover.example.com'],
                'short_ids' => ['0123456789abcdef'],
                'private_key' => rtrim(strtr(base64_encode(str_repeat("\x11", 32)), '+/', '-_'), '='),
            ];
        }
        $this->insertNode($custom);
        $factory = new HttpFactory();
        $requests = new DecoratedServerRequestFactory($factory);
        $responses = new DecoratedResponseFactory($factory, $factory);
        $request = $requests->createServerRequest('GET', 'https://panel.example.com/mod_mu/nodes/1/info');
        $nodeController = (new \ReflectionClass(NodeController::class))->newInstanceWithoutConstructor();
        $nodeReply = $nodeController->getInfo($request, $responses->createResponse(), ['id' => 1]);
        $nodePayload = json_decode((string) $nodeReply->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(1, $nodePayload['ret']);
        self::assertSame($custom, $nodePayload['data']['custom_config']);

        $userController = (new \ReflectionClass(UserController::class))->newInstanceWithoutConstructor();
        $userReply = $userController->index($request->withQueryParams(['node_id' => 1]), $responses->createResponse(), []);
        $userPayload = json_decode((string) $userReply->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(self::USER_UUID, $userPayload['data'][0]['uuid']);

        $subController = (new \ReflectionClass(SubController::class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty(\App\Controllers\BaseController::class, 'antiXss'))->setValue($subController, new \voku\helper\AntiXSS());
        foreach (['v2ray', 'clash', 'singbox', 'v2rayjson'] as $format) {
            $reply = $subController->index($request, $responses->createResponse(), ['token' => 'vless-test-token', 'subtype' => $format]);
            self::assertSame(200, $reply->getStatusCode());
            self::assertSame(Subscribe::getContent($this->user, $format), (string) $reply->getBody());
        }
        $clash = $this->clashNode();
        if ($network === 'ws' || $network === 'httpupgrade') {
            self::assertSame('/vless-test', $clash['ws-opts']['path']);
        } elseif ($network === 'grpc') {
            self::assertSame('vless-test', $clash['grpc-opts']['grpc-service-name']);
        }
        $directory = getenv('VLESS_CONTRACT_DIR');
        if ($directory !== false && $directory !== '') {
            file_put_contents($directory . '/' . $network . '-' . $security . ($flow !== '' ? '-vision' : '') . '.json', json_encode([
                'node_response' => $nodePayload, 'user_response' => $userPayload, 'client_outbound' => $this->xrayNode(),
                'clash_proxy' => $this->clashNode(),
                'singbox_outbound' => $network === 'xhttp' ? null : $this->singBoxNode(),
            ], JSON_THROW_ON_ERROR));
        }
    }

    public static function transports(): array
    {
        return [
            ['tcp', 'none', ''], ['tcp', 'tls', ''], ['tcp', 'tls', 'xtls-rprx-vision'],
            ['tcp', 'reality', 'xtls-rprx-vision'], ['ws', 'tls', ''], ['grpc', 'tls', ''],
            ['httpupgrade', 'tls', ''], ['xhttp', 'tls', ''],
        ];
    }

    private function insertNode(array $custom, string $server = 'node.example.com', string $name = 'Example'): void
    {
        Node::query()->create([
            'type' => 1,
            'sort' => 11,
            'name' => $name,
            'server' => $server,
            'custom_config' => json_encode($custom, JSON_THROW_ON_ERROR),
            'node_class' => 0,
            'node_group' => 0,
            'node_bandwidth_limit' => 0,
            'node_bandwidth' => 0,
        ]);
    }

    private function clashNode(): array
    {
        return yaml_parse(Subscribe::getContent($this->user, 'clash'))['proxies'][0];
    }

    private function singBoxNode(): array
    {
        return json_decode(Subscribe::getContent($this->user, 'singbox'), true, 512, JSON_THROW_ON_ERROR)['outbounds'][2];
    }

    private function xrayNode(): array
    {
        return json_decode(Subscribe::getContent($this->user, 'v2rayjson'), true, 512, JSON_THROW_ON_ERROR)['outbounds'][0];
    }
}
