<?php

declare(strict_types=1);

use App\Models\Config;
use App\Models\User;
use App\Services\DB;
use App\Services\Reward;
use App\Utils\Tools;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();
});

afterEach(function () {
    DB::connection()->rollBack();
});

function rewardTestConfig(string $item, string $value): void
{
    $config = Config::where('item', $item)->first();

    if ($config === null) {
        Config::create([
            'item' => $item,
            'value' => $value,
            'class' => 'feature',
            'is_public' => 0,
            'type' => 'int',
            'default' => $value,
            'mark' => '',
        ]);

        return;
    }

    Config::where('item', $item)->update(['value' => $value]);
}

function rewardTestUser(int $transferEnable = 0): User
{
    $user = new User();
    $user->email = 'reward-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->user_name = 'reward-' . bin2hex(random_bytes(4));
    $user->pass = '';
    $user->passwd = bin2hex(random_bytes(16));
    $user->uuid = Uuid::uuid4()->toString();
    $user->api_token = bin2hex(random_bytes(16));
    $user->port = random_int(10000, 60000);
    $user->transfer_enable = $transferEnable;
    $user->last_check_in_time = 0;
    $user->is_shadow_banned = 0;
    $user->save();

    return $user;
}

describe('Reward::issueCheckinReward', function () {
    beforeEach(function () {
        rewardTestConfig('checkin_min', '5');
        rewardTestConfig('checkin_max', '5');
    });

    it('adds the traffic and records the check-in time', function () {
        $user = rewardTestUser();
        $before = (int) User::find($user->id)->transfer_enable;

        expect(Reward::issueCheckinReward($user->id))->toBe(5);

        $after = User::find($user->id);

        expect($after->transfer_enable)->toBe($before + Tools::mbToB(5))
            ->and($after->last_check_in_time)->toBeGreaterThan(time() - 10);
    });

    it('reports already checked in for a second call on the same day', function () {
        $user = rewardTestUser();
        Reward::issueCheckinReward($user->id);
        $afterFirst = (int) User::find($user->id)->transfer_enable;

        expect(Reward::issueCheckinReward($user->id))->toBeFalse()
            ->and(User::find($user->id)->transfer_enable)->toBe($afterFirst);
    });

    it('allows the next check-in once last_check_in_time is from a previous day', function () {
        $user = rewardTestUser();
        User::where('id', $user->id)->update(['last_check_in_time' => strtotime('yesterday 12:00')]);

        expect(Reward::issueCheckinReward($user->id))->toBe(5);
    });

    it('refuses a shadow banned user', function () {
        $user = rewardTestUser();
        User::where('id', $user->id)->update(['is_shadow_banned' => 1]);
        $before = (int) User::find($user->id)->transfer_enable;

        expect(Reward::issueCheckinReward($user->id))->toBeFalse()
            ->and(User::find($user->id)->transfer_enable)->toBe($before);
    });

    it('records the check-in even when the drawn traffic is zero', function () {
        rewardTestConfig('checkin_min', '0');
        rewardTestConfig('checkin_max', '0');
        $user = rewardTestUser();

        expect(Reward::issueCheckinReward($user->id))->toBe(0)
            ->and(User::find($user->id)->last_check_in_time)->toBeGreaterThan(time() - 10);
    });

    it('adds to the value currently stored in the database', function () {
        $user = rewardTestUser();
        User::where('id', $user->id)->update(['transfer_enable' => 9000]);

        Reward::issueCheckinReward($user->id);

        expect(User::find($user->id)->transfer_enable)->toBe(9000 + Tools::mbToB(5));
    });
});

describe('Reward::issueRegReward', function () {
    it('adds the invite registration traffic to the inviter', function () {
        rewardTestConfig('invite_reg_traffic_reward', '2');
        rewardTestConfig('invite_reg_money_reward', '0');
        $inviter = rewardTestUser(1000);
        $invitee = rewardTestUser();
        $invitee->ref_by = $inviter->id;
        $invitee->save();

        Reward::issueRegReward($invitee->id, $inviter->id);

        expect(User::find($inviter->id)->transfer_enable)->toBe(1000 + Tools::gbToB(2));
    });

    it('adds the reward again for a second invite', function () {
        rewardTestConfig('invite_reg_traffic_reward', '2');
        rewardTestConfig('invite_reg_money_reward', '0');
        $inviter = rewardTestUser(1000);
        $first = rewardTestUser();
        $second = rewardTestUser();

        Reward::issueRegReward($first->id, $inviter->id);
        Reward::issueRegReward($second->id, $inviter->id);

        expect(User::find($inviter->id)->transfer_enable)->toBe(1000 + 2 * Tools::gbToB(2));
    });

    it('leaves the inviter untouched when the traffic reward is disabled', function () {
        rewardTestConfig('invite_reg_traffic_reward', '0');
        rewardTestConfig('invite_reg_money_reward', '0');
        $inviter = rewardTestUser(1000);
        $invitee = rewardTestUser();

        Reward::issueRegReward($invitee->id, $inviter->id);

        expect(User::find($inviter->id)->transfer_enable)->toBe(1000);
    });
});
