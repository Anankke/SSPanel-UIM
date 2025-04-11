<?php
declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\View;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function json_decode;
use function trim;
use App\Services\Gateway\Cryptomus\Payment as CryptomusPayment;

final class Cryptomus extends Base
{
    /**
     * @var array
     */
    protected array $cryptomus = [];

    /**
     *
     */
    public function __construct()
    {
        $this->antiXss = new AntiXSS();

        $this->cryptomus['cryptomus_api_key'] = Config::obtain('cryptomus_api_key');
        $this->cryptomus['cryptomus_uuid'] = Config::obtain('cryptomus_uuid');
        $this->cryptomus['cryptomus_subtract'] = Config::obtain('cryptomus_subtract');
        $this->cryptomus['cryptomus_lifetime'] = Config::obtain('cryptomus_lifetime');
        $this->cryptomus['cryptomus_currency'] = Config::obtain('cryptomus_currency');
    }

    /**
     * @return string
     */
    public static function _name(): string
    {
        return 'cryptomus';
    }

    /**
     * @return bool
     */
    public static function _enable(): bool
    {
        return self::getActiveGateway('cryptomus');
    }

    /**
     * @return string
     */
    public static function _readableName(): string
    {
        return 'Cryptomus';
    }

    /**
     * @param ServerRequest $request
     * @param Response $response
     * @param array $args
     * @return ResponseInterface
     * @throws Exception
     */
    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $price = $this->antiXss->xss_clean($request->getParam('price'));
        $invoiceId = $this->antiXss->xss_clean($request->getParam('invoice_id'));

        $type = $this->antiXss->xss_clean($request->getParam('type'));
        $redir = $this->antiXss->xss_clean($request->getParam('redir'));

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        $user = Auth::getUser();
        $pl = new Paylist();

        $tradeNumber = self::generateGuid();

        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->invoice_id = $invoiceId ;
        $pl->tradeno = $tradeNumber;
        $pl->gateway = self::_readableName();

        $pl->save();

        $paymentData = [
            'amount' => $price,
            'currency' => $this->cryptomus['cryptomus_currency'] ?? 'CNY',
            'order_id' => 'sspanel_' . $invoiceId ,
            'url_return' => $redir,
            'url_callback' => self::getCallbackUrl(),
            'lifetime' => $this->cryptomus['cryptomus_lifetime'] ?? '3600',
            'subtract' => $config['cryptomus_subtract'] ?? '0',
            'plugin_name' => 'sspanel:2024.1',
            'additional_data' => json_encode(['tradeno' => $tradeNumber])
        ];

        $paymentInstance = $this->getPayment();

        try {
            $payment = $paymentInstance->create($paymentData);
        } catch (\Exception $exception) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求支付失败: ' . $exception->getMessage(),
            ]);
        }

        return $response->withHeader('HX-Redirect', $payment['url'])->withJson([
            'ret' => 1,
            'msg' => '订单发起成功，正在跳转到支付页面...',
        ]);
    }


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return ResponseInterface
     */
    public function notify($request, $response, $args): ResponseInterface
    {
        $payload = trim(file_get_contents('php://input'));
        $data = json_decode($payload, true);
        $additionalData = json_decode($data['additional_data'], true);

        if (!$this->hashEqual($data)) {
            return $response->withJson(['state' => 'fail', 'msg' => 'Sign is not valid']);
        }

        $success = !empty($data['is_final']) && ($data['status'] === 'paid' || $data['status'] === 'paid_over' || $data['status'] === 'wrong_amount');
        if ($success) {
//            $orderId = preg_replace('/^sspanel(?:_upd)?_/', '', $data['order_id'] ?? '');
            $this->postPayment($additionalData['tradeno']);

            return $response->withJson([
                'ret' => 1,
                'msg' => '支付成功',
            ]);
        }

        return $response->withJson(['state' => 'fail', 'msg' => 'Payment failed']);
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/cryptomus.tpl');
    }

    /**
     * @return CryptomusPayment
     * @throws \Exception
     */
    private function getPayment(): CryptomusPayment
    {
        $merchantUuid = trim($this->cryptomus['cryptomus_uuid']);
        $paymentKey = trim($this->cryptomus['cryptomus_api_key']);

        if (!$merchantUuid && !$paymentKey) {
            throw new Exception("Please fill UUID and API key");
        }

        return new CryptomusPayment($paymentKey, $merchantUuid);
    }

    /**
     * @param $data
     * @return bool
     */
    private function hashEqual($data): bool
    {
        $paymentKey = trim($this->cryptomus['cryptomus_api_key']);

        if (!$paymentKey) {
            return false;
        }

        $signature = $data['sign'];
        if (!$signature) {
            return false;
        }

        unset($data['sign']);

        $hash = md5(base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE)) . $paymentKey);
        if (!hash_equals($hash, $signature)) {
            return false;
        }

        return true;
    }
}
