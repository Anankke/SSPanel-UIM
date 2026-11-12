<?php

declare(strict_types=1);

use App\Models\Config;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Paylist;
use App\Models\User;
use App\Models\UserMoneyLog;
use App\Services\DB;
use App\Services\Gateway\Base;
use App\Services\Gateway\Epay;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();
});

afterEach(function () {
    DB::connection()->rollBack();
});

function paymentUser(string $money): User
{
    $user = new User();
    $user->email = 'payment-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->pass = '';
    $user->passwd = '';
    $user->uuid = Uuid::uuid4()->toString();
    $user->port = random_int(10000, 60000);
    $user->money = $money;
    $user->save();

    return $user;
}

function paymentInvoice(User $user, string $price, string $status = 'unpaid'): Invoice
{
    $order = new Order();
    $order->user_id = $user->id;
    $order->product_type = 'tabp';
    $order->price = $price;
    $order->status = 'pending_payment';
    $order->save();

    $invoice = new Invoice();
    $invoice->user_id = $user->id;
    $invoice->order_id = $order->id;
    $invoice->content = '[]';
    $invoice->price = $price;
    $invoice->status = $status;
    $invoice->type = 'product';
    $invoice->save();

    return $invoice;
}

function paymentAttempt(User $user, Invoice $invoice, string $total): Paylist
{
    $paylist = new Paylist();
    $paylist->userid = $user->id;
    $paylist->total = $total;
    $paylist->invoice_id = $invoice->id;
    $paylist->tradeno = bin2hex(random_bytes(8));
    $paylist->save();

    return $paylist;
}

function paymentSetting(string $item, string $value): void
{
    (new Config())->where('item', $item)->delete();
    (new Config())->insert(['item' => $item, 'value' => $value, 'type' => 'string']);
}

function epayCheckout(User $user, Invoice $invoice): void
{
    // 网关地址指向本机关闭的端口：支付记录在请求网关之前写入，请求失败不影响断言
    paymentSetting('epay_url', 'http://127.0.0.1:9/');
    paymentSetting('epay_sign_type', 'sha256');
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $GLOBALS['user'] = $user;

    $factory = new HttpFactory();
    $request = (new DecoratedServerRequestFactory($factory))
        ->createServerRequest('POST', '/user/payment/purchase/epay')
        ->withParsedBody(['invoice_id' => (string) $invoice->id, 'type' => 'alipay', 'redir' => '']);
    (new Epay())->purchase($request, (new DecoratedResponseFactory($factory, $factory))->createResponse(), []);

    unset($GLOBALS['user']);
}

function paymentGateway(): Base
{
    return new class() extends Base {
        public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
        {
            return $response;
        }

        public function notify(ServerRequest $request, Response $response, array $args): ResponseInterface
        {
            return $response;
        }

        public static function _name(): string
        {
            return 'test';
        }

        public static function _enable(): bool
        {
            return false;
        }

        public static function _readableName(): string
        {
            return 'Test';
        }

        public static function getPurchaseHTML(): string
        {
            return '';
        }
    };
}

it('marks the invoice paid on the first notification', function () {
    $user = paymentUser('0');
    $invoice = paymentInvoice($user, '100');
    $paylist = paymentAttempt($user, $invoice, '100');

    paymentGateway()->postPayment($paylist->tradeno);

    expect((new Invoice())->find($invoice->id)->status)->toBe('paid_gateway')
        ->and((new Paylist())->find($paylist->id)->status)->toBe(1)
        ->and((new User())->find($user->id)->money)->toBe(0.0);
});

it('credits the amount paid above the invoice price only once when the notification repeats', function () {
    $user = paymentUser('0');
    $invoice = paymentInvoice($user, '70', 'partially_paid');
    $paylist = paymentAttempt($user, $invoice, '100');

    paymentGateway()->postPayment($paylist->tradeno);
    paymentGateway()->postPayment($paylist->tradeno);
    paymentGateway()->postPayment($paylist->tradeno);

    expect((new User())->find($user->id)->money)->toBe(30.0)
        ->and((new UserMoneyLog())->where('user_id', $user->id)->count())->toBe(1)
        ->and((new Invoice())->find($invoice->id)->status)->toBe('paid_gateway');
});

it('records each checkout at the amount due when it starts', function () {
    $user = paymentUser('0');
    $invoice = paymentInvoice($user, '100');

    epayCheckout($user, $invoice);
    $invoice->price = '70';
    $invoice->status = 'partially_paid';
    $invoice->save();
    epayCheckout($user, $invoice);

    $attempt = (new Paylist())->where('invoice_id', $invoice->id)->orderByDesc('id')->first();
    paymentGateway()->postPayment($attempt->tradeno);

    expect($attempt->total)->toBe('70.00')
        ->and((new User())->find($user->id)->money)->toBe(0.0)
        ->and((new Invoice())->find($invoice->id)->status)->toBe('paid_gateway');
});

it('adds the whole payment to balance once when the invoice no longer takes payments', function (string $status) {
    $user = paymentUser('0');
    $invoice = paymentInvoice($user, '100', $status);
    $paylist = paymentAttempt($user, $invoice, '100');

    paymentGateway()->postPayment($paylist->tradeno);
    paymentGateway()->postPayment($paylist->tradeno);

    $logs = (new UserMoneyLog())->where('user_id', $user->id)->get();

    expect((new User())->find($user->id)->money)->toBe(100.0)
        ->and($logs)->toHaveCount(1)
        ->and($logs[0]->remark)->toContain('#' . $invoice->id)->toContain($paylist->tradeno)
        ->and((new Invoice())->find($invoice->id)->status)->toBe($status);
})->with(['cancelled', 'paid_gateway', 'refunded_balance']);
