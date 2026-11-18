<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\User;
use App\Services\Cron;
use App\Services\DB;
use App\Utils\Tools;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();
});

afterEach(function () {
    DB::connection()->rollBack();
});

function cronTestUser(): User
{
    $user = new User();
    $user->email = 'cron-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->user_name = 'cron-' . bin2hex(random_bytes(4));
    $user->pass = '';
    $user->passwd = bin2hex(random_bytes(16));
    $user->uuid = Uuid::uuid4()->toString();
    $user->api_token = bin2hex(random_bytes(16));
    $user->port = random_int(10000, 60000);
    $user->transfer_enable = 0;
    $user->save();

    return $user;
}

function cronTestOrder(int $userId, string $productType, array $content): Order
{
    $order = new Order();
    $order->user_id = $userId;
    $order->product_type = $productType;
    $order->product_content = json_encode($content);
    $order->status = 'pending_activation';
    $order->create_time = time();
    $order->save();

    return $order;
}

it('adds a bandwidth order to the user and marks the order activated', function () {
    $user = cronTestUser();
    User::where('id', $user->id)->update(['transfer_enable' => 1000]);
    $order = cronTestOrder($user->id, 'bandwidth', ['bandwidth' => 2]);

    ob_start();
    Cron::processBandwidthOrderActivation();
    ob_end_clean();

    expect(User::find($user->id)->transfer_enable)->toBe(1000 + Tools::gbToB(2))
        ->and(Order::find($order->id)->status)->toBe('activated')
        ->and(Order::find($order->id)->update_time)->toBeGreaterThan(time() - 10);
});

it('adds the stored value on top of the current database value', function () {
    $user = cronTestUser();
    $order = cronTestOrder($user->id, 'bandwidth', ['bandwidth' => 1]);
    User::where('id', $user->id)->update(['transfer_enable' => 500]);

    ob_start();
    Cron::processBandwidthOrderActivation();
    ob_end_clean();

    expect(User::find($user->id)->transfer_enable)->toBe(500 + Tools::gbToB(1))
        ->and(Order::find($order->id)->status)->toBe('activated');
});
