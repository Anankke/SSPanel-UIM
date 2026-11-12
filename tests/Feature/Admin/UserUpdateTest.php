<?php

declare(strict_types=1);

use App\Models\User;

const USER_UPDATE_EMAIL_PATTERN = 'userupdate%@example.invalid';

function userUpdateCreateUser(): User
{
    $suffix = bin2hex(random_bytes(6));
    $user = new User();
    $user->email = "userupdate{$suffix}@example.invalid";
    $user->user_name = "userupdate{$suffix}";
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

/**
 * The payload the admin edit page sends: every field of the form as a string, checkboxes as "true" or "false".
 */
function userUpdatePayload(User $user, array $overrides = []): array
{
    return array_merge([
        'email' => $user->email,
        'user_name' => $user->user_name,
        'pass' => '',
        'money_adjust' => '',
        'money_remark' => '',
        'ref_by' => '0',
        'port' => (string) $user->port,
        'method' => 'aes-256-gcm',
        'transfer_enable' => '1TB',
        'node_group' => '0',
        'class' => '0',
        'class_expire' => date('Y-m-d H:i:s', strtotime('+1 year')),
        'auto_reset_day' => '0',
        'auto_reset_bandwidth' => '0',
        'node_speedlimit' => '0',
        'node_iplimit' => '0',
        'locale' => 'zh-CN',
        'banned_reason' => '',
        'remark' => '',
        'is_admin' => 'false',
        'is_shadow_banned' => 'false',
        'is_banned' => 'false',
    ], $overrides);
}

beforeEach(function () {
    $this->previousUser = $GLOBALS['user'] ?? null;
    $admin = new User();
    $admin->isLogin = true;
    $admin->is_admin = 1;
    $GLOBALS['user'] = $admin;

    $this->targetUser = userUpdateCreateUser();
});

afterEach(function () {
    $GLOBALS['user'] = $this->previousUser;
    (new User())->where('email', 'like', USER_UPDATE_EMAIL_PATTERN)->delete();
});

describe('admin user update', function () {
    it('saves the edit form of a user', function () {
        $remark = 'remark-' . bin2hex(random_bytes(4));

        $response = $this->put(
            '/admin/user/' . $this->targetUser->id,
            userUpdatePayload($this->targetUser, ['remark' => $remark])
        );
        $body = json_decode((string) $response->getBody(), true);

        expect($response->getStatusCode())->toBe(200)
            ->and($body['ret'] ?? null)->toBe(1);

        $this->targetUser->refresh();

        expect($this->targetUser->remark)->toBe($remark);
    });
});
