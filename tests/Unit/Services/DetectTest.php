<?php

declare(strict_types=1);

use App\Models\DetectBanLog;
use App\Models\DetectLog;
use App\Models\User;
use App\Services\DB;
use App\Services\Detect;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();

    $this->env = [
        'auto_detect_ban_number' => $_ENV['auto_detect_ban_number'] ?? null,
        'auto_detect_ban_time' => $_ENV['auto_detect_ban_time'] ?? null,
        'auto_detect_ban_allow_admin' => $_ENV['auto_detect_ban_allow_admin'] ?? null,
        'auto_detect_ban_allow_users' => $_ENV['auto_detect_ban_allow_users'] ?? null,
    ];

    $_ENV['auto_detect_ban_number'] = 1000;
    $_ENV['auto_detect_ban_time'] = 60;
    $_ENV['auto_detect_ban_allow_admin'] = false;
    $_ENV['auto_detect_ban_allow_users'] = [];
});

afterEach(function () {
    foreach ($this->env as $key => $value) {
        if ($value === null) {
            unset($_ENV[$key]);
        } else {
            $_ENV[$key] = $value;
        }
    }

    DB::connection()->rollBack();
});

function detectTestUser(): User
{
    $user = new User();
    $user->email = 'detect-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->user_name = 'detect-' . bin2hex(random_bytes(4));
    $user->pass = '';
    $user->passwd = bin2hex(random_bytes(16));
    $user->uuid = Uuid::uuid4()->toString();
    $user->api_token = bin2hex(random_bytes(16));
    $user->port = random_int(10000, 60000);
    $user->all_detect_number = 0;
    $user->is_banned = 0;
    $user->is_admin = 0;
    $user->save();

    return $user;
}

function detectTestLog(int $userId): void
{
    $log = new DetectLog();
    $log->user_id = $userId;
    $log->list_id = 0;
    $log->datetime = time();
    $log->node_id = 0;
    $log->status = 0;
    $log->save();
}

function detectTestRun(): void
{
    ob_start();
    Detect::ban();
    ob_end_clean();
}

it('adds the number of new detect logs to the user', function () {
    $user = detectTestUser();
    detectTestLog($user->id);
    detectTestLog($user->id);
    detectTestLog($user->id);

    detectTestRun();

    expect(User::find($user->id)->all_detect_number)->toBe(1)
        ->and(DetectLog::where('user_id', $user->id)->where('status', 0)->count())->toBe(0)
        ->and(User::find($user->id)->is_banned)->toBe(0);
});

it('bans the user once the threshold is reached in one run', function () {
    $_ENV['auto_detect_ban_number'] = 1;
    $user = detectTestUser();
    detectTestLog($user->id);
    detectTestLog($user->id);

    detectTestRun();

    $after = User::find($user->id);
    $banLog = DetectBanLog::where('user_id', $user->id)->first();

    expect($after->all_detect_number)->toBe(1)
        ->and($after->is_banned)->toBe(1)
        ->and($after->banned_reason)->toBe('DetectBan')
        ->and($banLog)->not->toBeNull()
        ->and((int) $banLog->all_detect_number)->toBe(1)
        ->and((int) $banLog->detect_number)->toBe(1);
});
