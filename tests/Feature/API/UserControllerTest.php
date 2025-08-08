<?php

/**
 * @group database
 * @group feature  
 * @group api
 */

use App\Models\User;
use App\Models\Node;
use Tests\SlimTestCase;

beforeEach(function () {
    // Initialize database before any operations
    $this->useDatabase = true;
    $this->setUp();
    
    // Clear previous test data
    Node::where('name', 'Test Node')->delete();
    User::where('email', 'LIKE', 'test%@example.com')->delete();
    
    // Create test node
    $this->node = new Node();
    $this->node->name = 'Test Node';
    $this->node->server = 'test.example.com';
    $this->node->password = bin2hex(random_bytes(32));
    $this->node->type = 1; // shadowsocks
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

describe('UserController API - Shadowsocks Node', function () {
    it('returns user list for shadowsocks node', function () {
        // Create test users
        $users = createUsers(3);
        
        // Send request
        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['ret'])->toBe(1)
            ->and($data['data'])->toHaveCount(3);
        
        // Verify returned fields
        // Note: Controller's $keys_unset are fields to be deleted
        // For sort 14, removes: u, d, transfer_enable, method, port, passwd, node_iplimit
        $userData = $data['data'][0];
        
        // These fields should be removed
        expect($userData)->not->toHaveKeys(['u', 'd', 'transfer_enable', 'method', 'port', 'passwd', 'node_iplimit']);
        
        // These fields should exist
        expect($userData)->toHaveKeys(['id', 'uuid', 'node_speedlimit']);
    });
});

describe('UserController API - V2Ray Node', function () {
    it('returns user list for v2ray node', function () {
        // Update node type
        $this->node->sort = 1;
        $this->node->save();
        
        // Create test users
        $users = createUsers(2);
        
        // Send request
        $response = $this->get('/mod_mu/users?node_id=' . $this->node->id . '&key=' . $_ENV['muKey']);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['ret'])->toBe(1);
        
        // Verify V2Ray node returns uuid
        $userData = $data['data'][0];
        expect($userData)
            ->toHaveKey('uuid')
            ->not->toHaveKey('passwd');
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
            ]
        ];
        
        $response = $this->post('/mod_mu/users/traffic?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
            'data' => json_encode($trafficData),
        ]);
        
        // Verify response
        assertResponseStatus(200, $response);
        assertJsonResponse($response);
        
        $data = getJsonData($response);
        expect($data['data'])->toBe('ok');
        
        // Verify traffic update
        $user->refresh();
        expect($user->u)->toBe($initialU + 1024 * 1024)
            ->and($user->d)->toBe($initialD + 2048 * 1024);
    });
});

describe('UserController API Node Status', function () {
    it('updates node online status', function () {
        // Send heartbeat
        $response = $this->post('/mod_mu/nodes/alive?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
            'load' => '0.5',
            'uptime' => '86400',
        ]);
        
        // Verify response
        assertResponseStatus(200, $response);
        
        // Verify node status update
        $this->node->refresh();
        expect($this->node->status)->toBe('online')
            ->and(strtotime($this->node->last_check_at))->toBeGreaterThan(time() - 60);
    })->skip('Node alive endpoint not implemented yet');
});

describe('UserController API Caching', function () {
    it('supports etag caching', function () {
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
    })->skip('ETag caching not implemented in current API');
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