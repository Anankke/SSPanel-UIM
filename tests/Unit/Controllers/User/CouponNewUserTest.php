<?php

declare(strict_types=1);

use App\Controllers\User\CouponController;
use App\Controllers\User\OrderController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\DB;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();
});

afterEach(function () {
    unset($GLOBALS['user']);
    DB::connection()->rollBack();
});

function couponTestUser(bool $withOrder): User
{
    $user = new User();
    $user->email = 'coupon-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->pass = '';
    $user->passwd = '';
    $user->uuid = Uuid::uuid4()->toString();
    $user->port = random_int(10000, 60000);
    $user->save();

    if ($withOrder) {
        $order = new Order();
        $order->user_id = $user->id;
        $order->product_type = 'tabp';
        $order->price = '10';
        $order->status = 'activated';
        $order->save();
    }

    return $user;
}

function couponTestProduct(): Product
{
    $product = new Product();
    $product->type = 'tabp';
    $product->name = 'Coupon test product';
    $product->price = '100';
    $product->content = '{}';
    $product->limit = json_encode([
        'class_required' => '',
        'node_group_required' => '',
        'new_user_required' => 0,
    ]);
    $product->status = 1;
    $product->stock = -1;
    $product->save();

    return $product;
}

function couponTestCoupon(string $newUser): UserCoupon
{
    $coupon = new UserCoupon();
    $coupon->code = 'NEWUSER' . $newUser . strtoupper(bin2hex(random_bytes(4)));
    $coupon->content = json_encode(['type' => 'percentage', 'value' => '10']);
    $coupon->limit = json_encode([
        'product_id' => '',
        'use_time' => '-1',
        'total_use_time' => '-1',
        'new_user' => $newUser,
        'disabled' => 0,
    ]);
    $coupon->expire_time = 0;
    $coupon->save();

    return $coupon;
}

function couponTestRequest(UserCoupon $coupon, Product $product): array
{
    $factory = new HttpFactory();
    $request = (new DecoratedServerRequestFactory($factory))
        ->createServerRequest('POST', '/user/coupon_check')
        ->withParsedBody(['coupon' => $coupon->code, 'product_id' => (string) $product->id]);

    return [$request, (new DecoratedResponseFactory($factory, $factory))->createResponse()];
}

function couponTestCheck(User $user, UserCoupon $coupon, Product $product): array
{
    $GLOBALS['user'] = $user;
    [$request, $response] = couponTestRequest($coupon, $product);
    $result = (new CouponController())->check($request, $response, []);

    return json_decode((string) $result->getBody(), true);
}

function couponTestBuy(User $user, UserCoupon $coupon, Product $product): ResponseInterface
{
    $GLOBALS['user'] = $user;
    [$request, $response] = couponTestRequest($coupon, $product);

    return (new OrderController())->product($request, $response, []);
}

function couponTestOrderCount(User $user): int
{
    return (new Order())->where('user_id', $user->id)->count();
}

it('rejects a new-user-only coupon for a user who already has orders', function () {
    $user = couponTestUser(true);
    $product = couponTestProduct();
    $coupon = couponTestCoupon('1');

    $checked = couponTestCheck($user, $coupon, $product);
    $result = couponTestBuy($user, $coupon, $product);

    expect($checked['ret'])->toBe(0)
        ->and($result->hasHeader('HX-Redirect'))->toBeFalse()
        ->and(json_decode((string) $result->getBody(), true)['ret'])->toBe(0)
        ->and(couponTestOrderCount($user))->toBe(1)
        ->and((new UserCoupon())->find($coupon->id)->use_count)->toBe(0);
});

it('accepts a new-user-only coupon for a user without orders', function () {
    $user = couponTestUser(false);
    $product = couponTestProduct();
    $coupon = couponTestCoupon('1');

    $checked = couponTestCheck($user, $coupon, $product);
    $result = couponTestBuy($user, $coupon, $product);

    expect($checked['ret'])->toBe(1)
        ->and($result->hasHeader('HX-Redirect'))->toBeTrue()
        ->and(couponTestOrderCount($user))->toBe(1);
});

it('accepts an unrestricted coupon whether or not the user has orders', function (bool $withOrder) {
    $user = couponTestUser($withOrder);
    $product = couponTestProduct();
    $coupon = couponTestCoupon('0');
    $orders = couponTestOrderCount($user);

    $checked = couponTestCheck($user, $coupon, $product);
    $result = couponTestBuy($user, $coupon, $product);

    expect($checked['ret'])->toBe(1)
        ->and($result->hasHeader('HX-Redirect'))->toBeTrue()
        ->and(couponTestOrderCount($user))->toBe($orders + 1);
})->with([
    'returning user' => [true],
    'new user' => [false],
]);
