<?php

declare(strict_types=1);

/**
 * @group database
 * @group feature
 * @group api
 */

use App\Models\Config;
use App\Models\HourlyUsage;
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
                ->not->toHaveKeys(['u', 'd', 'transfer_enable', 'method', 'port', 'passwd', 'node_iplimit'])
                ->toHaveKeys(['id', 'uuid', 'node_speedlimit']);
        }
    });
});

describe('UserController API - Hysteria2 Node', function () {
    it('returns the user uuid used as the Hysteria2 password', function () {
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
            ->not->toHaveKeys(['u', 'd', 'transfer_enable', 'method', 'port', 'passwd', 'node_iplimit'])
            ->toHaveKeys(['id', 'uuid', 'node_speedlimit'])
            ->and($userData['uuid'])->toBe($user->uuid);
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

    describe('with hourly traffic log enabled', function () {
        beforeEach(function () {
            $this->usageUserIds = [];
            $this->trafficLogCreated = false;

            $row = Config::where('item', 'traffic_log')->first();

            if ($row === null) {
                $this->trafficLogCreated = true;
                $this->trafficLogOriginal = '0';
                (new Config())->create([
                    'item' => 'traffic_log',
                    'value' => '1',
                    'class' => 'feature',
                    'is_public' => 1,
                    'type' => 'bool',
                    'default' => '0',
                    'mark' => '',
                ]);
            } else {
                $this->trafficLogOriginal = (string) $row->value;
                Config::where('item', 'traffic_log')->update(['value' => '1']);
            }

            $this->node->traffic_rate = 1;
            $this->node->is_dynamic_rate = 0;
            $this->node->save();
        });

        afterEach(function () {
            if ($this->trafficLogCreated) {
                Config::where('item', 'traffic_log')->delete();
            } else {
                Config::where('item', 'traffic_log')->update(['value' => $this->trafficLogOriginal]);
            }

            if ($this->usageUserIds !== []) {
                HourlyUsage::whereIn('user_id', $this->usageUserIds)->delete();
            }
        });

        it('ignores entries for users that do not exist', function () {
            [$userA, $userB, $userC] = createUsers(3);
            $idA = $userA->id;
            $idB = $userB->id;
            $idC = $userC->id;
            $this->usageUserIds = [$idA, $idB, $idC];
            HourlyUsage::whereIn('user_id', $this->usageUserIds)->delete();

            // C is deleted but still present in the node's report
            $userC->delete();

            $userA->refresh();
            $userB->refresh();
            $nodeBefore = Node::find($this->node->id);

            $response = $this->post('/mod_mu/users/traffic?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
                'data' => [
                    ['user_id' => $idA, 'u' => 1000, 'd' => 2000],
                    ['user_id' => $idC, 'u' => 7000, 'd' => 9000],
                    ['user_id' => $idB, 'u' => 3000, 'd' => 5000],
                ],
            ]);

            assertResponseStatus(200, $response);
            assertJsonResponse($response);

            $data = getJsonData($response);
            expect($data['ret'])->toBe(1)
                ->and($data['msg'])->toBe('ok');

            $afterA = User::find($idA);
            $afterB = User::find($idB);

            expect($afterA->u)->toBe($userA->u + 1000)
                ->and($afterA->d)->toBe($userA->d + 2000)
                ->and($afterA->transfer_total)->toBe($userA->transfer_total + 3000)
                ->and($afterA->transfer_today)->toBe($userA->transfer_today + 3000)
                ->and($afterB->u)->toBe($userB->u + 3000)
                ->and($afterB->d)->toBe($userB->d + 5000)
                ->and($afterB->transfer_total)->toBe($userB->transfer_total + 8000)
                ->and($afterB->transfer_today)->toBe($userB->transfer_today + 8000);

            expect(User::find($idC))->toBeNull()
                ->and(HourlyUsage::where('user_id', $idC)->count())->toBe(0);

            $nodeAfter = Node::find($this->node->id);
            expect($nodeAfter->node_bandwidth)->toBe($nodeBefore->node_bandwidth + 11000)
                ->and($nodeAfter->online_user)->toBe(1);
        });

        it('records a batch of existing users exactly as before', function () {
            $okBody = '{"ret":1,"msg":"ok"}';

            [$userA, $userB] = createUsers(2);
            $idA = $userA->id;
            $idB = $userB->id;
            $this->usageUserIds = [$idA, $idB];
            HourlyUsage::whereIn('user_id', $this->usageUserIds)->delete();

            $userA->refresh();
            $userB->refresh();
            $nodeBefore = Node::find($this->node->id);

            $response = $this->post('/mod_mu/users/traffic?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'], [
                'data' => [
                    ['user_id' => $idA, 'u' => 1500, 'd' => 2500],
                    ['user_id' => $idB, 'u' => 4000, 'd' => 6000],
                ],
            ]);

            assertResponseStatus(200, $response);
            expect((string) $response->getBody())->toBe($okBody);

            $afterA = User::find($idA);
            $afterB = User::find($idB);

            expect($afterA->u)->toBe($userA->u + 1500)
                ->and($afterA->d)->toBe($userA->d + 2500)
                ->and($afterA->transfer_total)->toBe($userA->transfer_total + 4000)
                ->and($afterA->transfer_today)->toBe($userA->transfer_today + 4000)
                ->and($afterB->u)->toBe($userB->u + 4000)
                ->and($afterB->d)->toBe($userB->d + 6000)
                ->and($afterB->transfer_total)->toBe($userB->transfer_total + 10000)
                ->and($afterB->transfer_today)->toBe($userB->transfer_today + 10000);

            $nodeAfter = Node::find($this->node->id);
            expect($nodeAfter->node_bandwidth)->toBe($nodeBefore->node_bandwidth + 14000)
                ->and($nodeAfter->online_user)->toBe(1);

            $usageA = HourlyUsage::where('user_id', $idA)->get();
            $usageB = HourlyUsage::where('user_id', $idB)->get();

            expect($usageA)->toHaveCount(1)
                ->and($usageB)->toHaveCount(1)
                ->and(array_sum(json_decode($usageA[0]->usage, true)))->toBe(4000)
                ->and(array_sum(json_decode($usageB[0]->usage, true)))->toBe(10000);
        });
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
