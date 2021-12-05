<?php
namespace App\Services\Gateway;

use App\Services\Auth;
use App\Services\View;
use App\Models\Paylist;
use App\Models\Setting;
use Exception;

class Token188 extends AbstractPayment
{
    protected $sdk;

    public static function _name() 
    {
        return 'token188';
    }

    public static function _enable() 
    {
        return self::getActiveGateway('token188');
    }

    public static function _readableName()
    {
        return "USDT 转账";
    }

    public function __construct()
    {
        $configs = Setting::getClass('token188');

        $this->sdk = new Token188SDK([
            'token188_url'      => $configs['token188_url'],
            'token188_mchid'    => $configs['token188_mchid'],
            'token188_key'      => $configs['token188_key']
        ]);
    }

    public function purchase($request, $response, $args)
    {
        $amount = (int) $request->getParam('amount');
        $user = Auth::getUser();

        if ($amount <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单金额错误'
            ]);
        }

        $pl             = new Paylist();
        $pl->userid     = $user->id;
        $pl->tradeno    = self::generateGuid();
        $pl->total      = $amount;
        $pl->save();

        try {
            $res = $this->sdk->pay([
                'trade_no'      => $pl->tradeno,
                'total_fee'     => $pl->total,
                'notify_url'    => self::getCallbackUrl(),
                'return_url'    => self::getUserReturnUrl()
            ]);

            return $response->withJson([
                'ret'       => 1,
                'qrcode'    => $res,
                'amount'    => $pl->total,
                'pid'       => $pl->tradeno
            ]);
        }
        catch (Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function notify($request, $response, $args)
    {
        $content = file_get_contents('php://input');
        $params = json_decode($content, true);

        //convert JSON into array
        if ($this->sdk->verify($params)) {
            $pid = $params['outTradeNo'];
            $this->postPayment($pid, 'token188');
            die('success');
            //The response should be 'success' only
        }

        die('fail');
    }

    public static function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/token188.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    public function getStatus($request, $response, $args)
    {
        $pid = $request->getParam('pid');
        $p = Paylist::where('tradeno', $pid)->first();

        return $response->withJson([
            'ret'       => 1,
            'result'    => $p->status,
        ]);
    }
}
