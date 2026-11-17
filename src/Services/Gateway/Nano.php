<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Invoice;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\DB;
use App\Services\Gateway\Nano\Amount;
use App\Services\Gateway\Nano\Binding;
use App\Services\Gateway\Nano\NodeClient;
use App\Services\Gateway\Nano\ReplayGuard;
use App\Services\Gateway\Nano\Verifier;
use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function time;
use function trim;

/**
 * Nano (XNO) payment gateway.
 *
 * Nano is a feeless, issuer-free network, so a buyer pays from an account whose
 * key they alone hold and the panel only reads the public ledger. The gateway
 * is off until the operator fills in a receiving account and a node URL
 * (self::_enable() reads the same active-gateway list as every other gateway),
 * and it adds no runtime dependency: the address checksum, the raw math, the
 * BLAKE2b digest, the invoice binding and the block check are all in the
 * stdlib-only App\Services\Gateway\Nano leaf.
 *
 * Two things make a settlement safe:
 *   - Binding (option C of xno-binding v1): each paylist row has its own
 *     representative account, derived from its trade number, the receiving
 *     account and the exact raw price. The payer sets that representative on
 *     their send, so a block can settle exactly one invoice at one price and
 *     cannot be re-pointed at another.
 *   - Replay refusal that survives a restart: a settled block hash is written
 *     into the UNIQUE `nano_payment_claims` table before the credit, so the
 *     same block can never be counted twice even across a restart, and the
 *     credit itself goes through the maintainer's atomic Base::postPayment().
 */
final class Nano extends Base
{
    private string $account;
    private string $nodeUrl;
    private string $rate;
    private int $maxAgeSeconds;

    public function __construct()
    {
        $this->antiXss = new AntiXSS();
        $this->account = trim((string) Config::obtain('nano_account'));
        $this->nodeUrl = trim((string) Config::obtain('nano_node_url'));
        $this->rate = trim((string) Config::obtain('nano_rate'));
        $maxAge = (int) Config::obtain('nano_max_age');
        $this->maxAgeSeconds = $maxAge > 0 ? $maxAge : 86400;
    }

    public static function _name(): string
    {
        return 'nano';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('nano');
    }

    public static function _readableName(): string
    {
        return 'Nano';
    }

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $invoiceId = $this->antiXss->xss_clean($request->getParam('invoice_id'));
        $invoice = (new Invoice())->find($invoiceId);

        if ($invoice === null) {
            return $response->withJson(['ret' => 0, 'msg' => 'Invoice not found']);
        }

        if ($invoice->price <= 0) {
            return $response->withJson(['ret' => 0, 'msg' => '非法的金额']);
        }

        if ($this->account === '' || $this->nodeUrl === '' || $this->rate === '') {
            return $response->withJson(['ret' => 0, 'msg' => 'Nano gateway is not configured']);
        }

        $user = Auth::getUser();
        $pl = new Paylist();
        $tradeNumber = self::generateGuid();
        $pl->userid = $user->id;
        $pl->total = $invoice->price;
        $pl->invoice_id = $invoiceId;
        $pl->tradeno = $tradeNumber;
        $pl->gateway = self::_readableName();
        $pl->save();

        $priceRaw = $this->priceRaw((string) $invoice->price);

        return $response->withJson([
            'ret' => 1,
            'msg' => '订单发起成功，请使用 Nano 钱包完成支付',
            'nano' => [
                'account' => $this->account,
                'amount_raw' => $priceRaw,
                'amount_xno' => Amount::multiplyDecimals((string) $invoice->price, $this->rate),
                'representative' => Binding::representativeAccount($tradeNumber, $this->account, $priceRaw),
                'trade_no' => $tradeNumber,
                'max_age_seconds' => $this->maxAgeSeconds,
            ],
        ]);
    }

    /**
     * The check-and-settle endpoint. The pay page polls it with the trade
     * number (and may pass a specific block hash); it reads candidate sends from
     * the operator's node, settles the first that the Verifier accepts and that
     * has not already been claimed, and is a safe no-op once the invoice is paid.
     */
    public function notify($request, $response, $args): ResponseInterface
    {
        $tradeNo = trim((string) $request->getParam('trade_no'));
        $paylist = $tradeNo === '' ? null : (new Paylist())->where('tradeno', $tradeNo)->first();

        if ($paylist === null) {
            return $response->withJson(['ret' => 0, 'msg' => 'Unknown trade number']);
        }

        if ((int) $paylist->status !== 0) {
            return $response->withJson(['ret' => 1, 'msg' => '支付成功']);
        }

        if ($this->account === '' || $this->nodeUrl === '') {
            return $response->withJson(['ret' => 0, 'msg' => 'Nano gateway is not configured']);
        }

        $priceRaw = $this->priceRaw((string) $paylist->total);
        $representative = Binding::representativeAccount($tradeNo, $this->account, $priceRaw);

        $node = new NodeClient($this->nodeUrl);
        $given = trim((string) $request->getParam('block'));
        $candidates = $given !== '' ? [$given] : $node->candidateSendHashes($this->account);

        foreach ($candidates as $hash) {
            $blockInfo = $node->blockInfo($hash);
            if ($blockInfo === null) {
                continue;
            }

            $result = Verifier::verify($blockInfo, $this->account, $priceRaw, $representative, $this->maxAgeSeconds, time());
            if (!$result['ok']) {
                continue;
            }

            // Durable, restart-proof replay refusal, then the atomic credit.
            if (!(new ReplayGuard(DB::getPdo()))->claim($hash, $tradeNo)) {
                continue;
            }

            $this->postPayment($tradeNo);

            return $response->withJson(['ret' => 1, 'msg' => '支付成功']);
        }

        return $response->withJson(['ret' => 0, 'msg' => '尚未检测到支付，请稍后再试']);
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/nano.tpl');
    }

    /**
     * The exact raw owed for an invoice: its total multiplied by the operator's
     * XNO-per-unit rate, with no float anywhere, then turned into raw.
     */
    private function priceRaw(string $invoiceTotal): string
    {
        return Amount::xnoToRaw(Amount::multiplyDecimals($invoiceTotal, $this->rate));
    }
}
