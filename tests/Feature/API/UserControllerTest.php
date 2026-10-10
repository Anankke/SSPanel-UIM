<?php

declare(strict_types=1);

/**
 * @group database
 * @group feature
 * @group api
 */

use App\Models\Node;
use App\Models\User;

beforeEach(function () {
    // Clear previous test data
    Node::where('name', 'Test Node')->delete();
    User::where('email', 'LIKE', 'test%@example.com')->delete();
    
    // Create test node
    $this->node = new Node();
    $this->node->name = 'Test Node';
    $this->node->server = 'test.example.com';
    $this->node->password = bin2hex(random_bytes(32));
    $this->node->type = 1;
    $this->node->sort = 14;
    $this->node->node_class = 0; // Allow class 0 users
    $this->node->node_group = 0; // No group restriction
    $this->node->save();
});

afterEach(function () {
    if (isset($this->node)) {
        $this->node->delete();
    }
    User::where('email', 'LIKE', 'test%@example.com')->delete();
});

describe('UserController API - Trojan Node', function () {
    it('returns user list for trojan node', function () {
        // Create test users
        $users = createUsers(3);
        
        // Send request
        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['ret'])->toBe(1);
        
        foreach ($users as $user) {
            $userData = findUserData($data['data'], $user->id);

            expect($userData)
                ->not->toHaveKeys(['u', 'd', 'transfer_enable', 'method', 'port', 'passwd'])
                ->toHaveKeys(['id', 'uuid', 'node_speedlimit', 'node_iplimit', 'alive_ip']);
        }
    });
});

describe('UserController API - Hysteria2 Node', function () {
    it('returns UUID authentication and limiter fields', function () {
        $this->node->sort = 15;
        $this->node->custom_config = json_encode([
            'offset_port_node' => '443',
            'hysteria2' => ['version' => 2],
        ]);
        $this->node->save();
        $user = createUsers(1)[0];

        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        assertResponseStatus(200, $response);
        $userData = findUserData(getJsonData($response)['data'], $user->id);

        expect($userData)
            ->toHaveKeys(['id', 'uuid', 'node_speedlimit', 'node_iplimit', 'alive_ip'])
            ->and($userData['uuid'])->toBe($user->uuid);
    });
});

describe('XrayR node and exhausted-user state', function () {
    it('reports a bandwidth-exhausted node as disabled and re-enables it after reset', function () {
        $this->node->node_bandwidth_limit = 1024;
        $this->node->node_bandwidth = 1024;
        $this->node->save();

        $url = '/mod_mu/nodes/' . $this->node->id . '/info?key=' . $_ENV['muKey'];
        $headers = ['X-XrayR-Capabilities' => 'node-state-v1'];
        $disabledResponse = $this->get($url, $headers);
        $disabledData = getJsonData($disabledResponse);

        expect($disabledData['ret'])->toBe(1)
            ->and($disabledData['data']['enabled'])->toBeFalse();
        $disabledEtag = $disabledResponse->getHeaderLine('ETag');

        $legacyResponse = $this->get($url);
        expect(getJsonData($legacyResponse)['ret'])->toBe(0);

        $this->node->node_bandwidth = 0;
        $this->node->save();
        $enabledResponse = $this->get($url, $headers + ['If-None-Match' => $disabledEtag]);
        $enabledData = getJsonData($enabledResponse);

        expect($enabledResponse->getStatusCode())->toBe(200)
            ->and($enabledData['data']['enabled'])->toBeTrue();
    });

    it('honors keep_connect by retaining an exhausted user at 1 Mbps', function () {
        $originalKeepConnect = $_ENV['keep_connect'] ?? false;
        $user = createUsers(1)[0];
        $user->transfer_enable = 1;
        $user->u = 1;
        $user->d = 1;
        $user->save();
        $url = '/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'];

        try {
            $_ENV['keep_connect'] = true;
            $limitedResponse = $this->get($url);
            $limitedUser = findUserData(getJsonData($limitedResponse)['data'], $user->id);
            expect($limitedUser['node_speedlimit'])->toBe(1);

            $_ENV['keep_connect'] = false;
            $removedResponse = $this->get($url);
            $returnedIds = array_column(getJsonData($removedResponse)['data'], 'id');
            expect($returnedIds)->not->toContain($user->id);
        } finally {
            $_ENV['keep_connect'] = $originalKeepConnect;
        }
    });
});

describe('UserController API - Shadowsocks 2022 Node', function () {
    it('returns user list for shadowsocks 2022 node', function () {
        // Update node type
        $this->node->sort = 1;
        $this->node->save();
        
        // Create test users
        $sourceUser = createUsers(1)[0];
        $sourceUser->passwd = 'password';
        $sourceUser->save();
        
        // Send request
        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['ret'])->toBe(1);
        
        // Verify Shadowsocks 2022 returns a derived user password
        $userData = findUserData($data['data'], $sourceUser->id);
        expect($userData)
            ->toHaveKey('passwd')
            ->not->toHaveKey('uuid')
            ->and($userData['passwd'])->toBe('YzAwNjdkNGFmNGU4N2YwMA==')
            ->not->toBe($sourceUser->passwd);
    });
});

describe('UserController API Authentication', function () {
    it('rejects invalid node key', function () {
        // Use wrong key
        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=invalid_key');
        
        // Verify returns 401
        assertResponseStatus(401, $response);
    });
});

describe('UserController API Traffic Reporting', function () {
    it('updates user traffic', function () {
        // Create test user
        $user = createUsers(1)[0];
        $initialU = $user->u;
        $initialD = $user->d;
        
        // Report traffic
        $trafficData = [
            [
                'user_id' => $user->id,
                'u' => 1024 * 1024, // 1MB
                'd' => 2048 * 1024, // 2MB
            ],
        ];
        
        $response = $this->post('/mod_mu/users/traffic?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
            'data' => $trafficData,
        ]);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['ret'])->toBe(1)
            ->and($data['msg'])->toBe('ok');
        
        // Verify traffic update
        $user->refresh();
        expect($user->u)->toBe($initialU + 1024 * 1024)
            ->and($user->d)->toBe($initialD + 2048 * 1024);
    });
});

describe('UserController API Caching', function () {
    it('supports etag caching', function () {
        createUsers(1);

        // First request
        $response1 = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        
        assertResponseStatus(200, $response1);
        $etag = $response1->getHeaderLine('ETag');
        expect($etag)->not->toBeEmpty();
        
        // Second request with ETag
        $response2 = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
            'If-None-Match' => $etag,
        ]);
        
        // Should return 304
        assertResponseStatus(304, $response2);
    });
});

// Helper functions specific to this test file

/**
 * Assert response status code
 */
if (!function_exists('assertResponseStatus')) {
    function assertResponseStatus(int $expected, $response): void
    {
        expect($response->getStatusCode())->toBe($expected);
    }
}

/**
 * Get JSON data from response
 */
if (!function_exists('getJsonData')) {
    function getJsonData($response): array
    {
        return json_decode((string) $response->getBody(), true);
    }
}

/**
 * Create test users for API tests
 */
function createUsers(int $count): array
{
    $users = [];
    for ($i = 0; $i < $count; $i++) {
        $user = new User();
        $user->email = "test{$i}@example.com";
        $user->user_name = "testuser{$i}";
        $user->pass = password_hash('password', PASSWORD_DEFAULT);
        $user->api_token = bin2hex(random_bytes(32)); // Add unique API token
        $user->port = 10000 + $i;
        $user->passwd = bin2hex(random_bytes(16));
        $user->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $user->method = 'aes-256-gcm';
        $user->transfer_enable = 1099511627776; // 1TB
        $user->u = rand(0, 1000000);
        $user->d = rand(0, 1000000);
        $user->node_iplimit = 0;
        $user->node_speedlimit = 0;
        $user->node_group = 0;
        $user->class = 0;
        $user->is_banned = 0;
        $user->class_expire = date('Y-m-d H:i:s', strtotime('+1 year'));
        $user->save();
        $users[] = $user;
    }
    return $users;
}

function findUserData(array $users, int $id): array
{
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }

    throw new RuntimeException("User {$id} was not returned by the API.");
}
