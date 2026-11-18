<?php

declare(strict_types=1);

/**
 * @group database
 * @group feature
 * @group api
 */

use App\Models\Node;
use App\Models\User;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    Node::where('name', 'Traffic Validation Node')->delete();
    User::where('email', 'LIKE', 'traffic-validation-%@example.com')->delete();

    $this->node = new Node();
    $this->node->name = 'Traffic Validation Node';
    $this->node->server = 'validation.example.com';
    $this->node->password = bin2hex(random_bytes(32));
    $this->node->type = 1;
    $this->node->sort = 14;
    $this->node->node_class = 0;
    $this->node->node_group = 0;
    $this->node->traffic_rate = 1;
    $this->node->is_dynamic_rate = 0;
    $this->node->save();

    $this->postTraffic = fn (array $data) => $this->post(
        '/mod_mu/users/traffic?node_id=' . $this->node->id . '&key=' . $_ENV['muKey'],
        ['data' => $data]
    );
});

afterEach(function () {
    $this->node->delete();
    User::where('email', 'LIKE', 'traffic-validation-%@example.com')->delete();
});

function trafficValidationUser(int $u = 0, int $d = 0): User
{
    $user = new User();
    $user->email = 'traffic-validation-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->user_name = 'traffic-validation-' . bin2hex(random_bytes(4));
    $user->pass = '';
    $user->passwd = bin2hex(random_bytes(16));
    $user->uuid = Uuid::uuid4()->toString();
    $user->api_token = bin2hex(random_bytes(16));
    $user->port = random_int(10000, 60000);
    $user->u = $u;
    $user->d = $d;
    $user->transfer_enable = 1099511627776;
    $user->node_iplimit = 0;
    $user->node_speedlimit = 0;
    $user->node_group = 0;
    $user->class = 0;
    $user->is_banned = 0;
    $user->save();

    return $user;
}

it('rejects the batch when one entry has a negative u', function () {
    $userA = trafficValidationUser(100, 200);
    $userB = trafficValidationUser(300, 400);
    $nodeBefore = Node::find($this->node->id);

    $response = ($this->postTraffic)([
        ['user_id' => $userA->id, 'u' => 1000, 'd' => 2000],
        ['user_id' => $userB->id, 'u' => -1, 'd' => 500],
    ]);

    $body = json_decode((string) $response->getBody(), true);

    expect($body['ret'])->toBe(0)
        ->and($body['msg'])->toBe('Invalid data.')
        ->and(User::find($userA->id)->u)->toBe(100)
        ->and(User::find($userA->id)->d)->toBe(200)
        ->and(User::find($userB->id)->u)->toBe(300)
        ->and(User::find($userB->id)->d)->toBe(400)
        ->and(Node::find($this->node->id)->node_bandwidth)->toBe($nodeBefore->node_bandwidth);
});

it('rejects the batch when d is negative', function () {
    $user = trafficValidationUser(100, 200);

    $response = ($this->postTraffic)([
        ['user_id' => $user->id, 'u' => 1000, 'd' => -2000],
    ]);

    $body = json_decode((string) $response->getBody(), true);

    expect($body['ret'])->toBe(0)
        ->and($body['msg'])->toBe('Invalid data.')
        ->and(User::find($user->id)->u)->toBe(100)
        ->and(User::find($user->id)->d)->toBe(200);
});

it('rejects the batch when u is a non numeric string', function () {
    $user = trafficValidationUser(100, 200);

    $response = ($this->postTraffic)([
        ['user_id' => $user->id, 'u' => 'abc', 'd' => 500],
    ]);

    $body = json_decode((string) $response->getBody(), true);

    expect($body['ret'])->toBe(0)
        ->and($body['msg'])->toBe('Invalid data.')
        ->and(User::find($user->id)->u)->toBe(100)
        ->and(User::find($user->id)->d)->toBe(200);
});

it('accepts numeric strings like before', function () {
    $user = trafficValidationUser(100, 200);

    $response = ($this->postTraffic)([
        ['user_id' => $user->id, 'u' => '12', 'd' => '34'],
    ]);

    $body = json_decode((string) $response->getBody(), true);

    expect($body['ret'])->toBe(1)
        ->and($body['msg'])->toBe('ok')
        ->and(User::find($user->id)->u)->toBe(112)
        ->and(User::find($user->id)->d)->toBe(234);
});

it('treats missing and null u or d as zero', function () {
    $user = trafficValidationUser(100, 200);

    $response = ($this->postTraffic)([
        ['user_id' => $user->id, 'd' => 5],
        ['user_id' => $user->id, 'u' => null, 'd' => null],
    ]);

    $body = json_decode((string) $response->getBody(), true);

    expect($body['ret'])->toBe(1)
        ->and(User::find($user->id)->u)->toBe(100)
        ->and(User::find($user->id)->d)->toBe(205);
});
