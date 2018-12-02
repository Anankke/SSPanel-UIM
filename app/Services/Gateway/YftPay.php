<?php
/**
 * Created by 傲慢与偏见.
 * OSUser: D-L
 * Date: 2017/10/12
 * Time: 21:08
 */

namespace App\Services\Gateway;

use App\Models\Payback;
use App\Models\User;
use App\Models\YftOrder;
use App\Services\Auth;
use App\Services\View;
use App\Utils\Telegram;
use App\Services\Config;
use App\Controllers\QuickPayFunction;

class YftPay extends AbstractPayment
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    public function yft($request, $response, $args)
    {
        $price = $request->getParams()['price'];
        return View::getSmarty()->assign('price', $price)->display('user/yft.tpl');
    }

    public function yftOrder($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $orderList = YftOrder::where("user_id", $this->user->id)->orderBy("id", "asc")->paginate(15, ['*'], 'page', $pageNum);
        $count = sizeof(YftOrder::where("user_id", $this->user->id)->get());
        $countPage = ceil($count / 15);
        $orderList->setPath('/user/yftOrder');

        return View::getSmarty()->assign('orderList', $orderList)->assign('countPage', $countPage)->assign('currentPage', $pageNum)->display('user/yftOrder.tpl');
    }

    /**
     * @desc 管理员查看所有充值记录
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function yftOrderForAdmin($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $orderList = YftOrder::where("price",">=", 0)->orderBy("id", "asc")->paginate(15, ['*'], 'page', $pageNum);
        $count = sizeof(YftOrder::where("price",">=", 0)->get());
        $countPage = ceil($count / 15);
        $orderList->setPath('/admin/yftOrder');

        return View::getSmarty()->assign('orderList', $orderList)->assign('countPage', $countPage)->assign('currentPage', $pageNum)->display('admin/yftOrder.tpl');
    }

    public function purchase($request, $response, $args)
    {
        return $this->constructPayPara($request);
    }

    public function yftPay($request, $response, $args)
    {
        return $this->constructPayPara($request);
    }

    public function notify($request, $response, $args)
    {
        $newResponse = $response->withStatus(302)->withHeader('Location', '/user/code');
        $yftLib = new QuickPayFunction();
        $pay_config = new YftPayConfig();
        $pay_config->init();

        //价格
        $total_fee = $request->getQueryParams()["total_fee"];//必填
        //易付通返回的订单号
        $yft_order_no = $request->getQueryParams()["trade_no"];
        //面板生成的订单号
        $ss_order_no = $request->getQueryParams()["out_trade_no"];//必填
        //订单说明
        $subject = $request->getQueryParams()["subject"];//必填
        //付款状态
        $trade_status = $request->getQueryParams()["trade_status"];//必填
        //加密验证字符串
        $sign = $request->getQueryParams()["sign"];//必填

        $verifyNotify = $yftLib->md5Verify(floatval($total_fee), $ss_order_no, $yft_order_no, $trade_status, $sign);
        if ($verifyNotify) {//验证成功
            if ($trade_status == 'TRADE_SUCCESS') {
                /*
                加入您的入库及判断代码;
                >>>>>>>！！！为了保证数据传达到回调地址，会请求4次。所以必须要先判断订单状态，然后再插入到数据库，这样后面即使请求3次，也不会造成订单重复！！！！<<<<<<<
                判断返回金额与实金额是否想同;
                判断订单当前状态;
                完成以上才视为支付成功
                */
                $orderInfo = new YftOrder();
                $orderInfo = $orderInfo->where("ss_order", "=", $ss_order_no)->first();
                if ($orderInfo == "" || $orderInfo == null) {
                    return "fail";
                }

                if ($orderInfo->price != $total_fee) {
                    return "fail";
                }

                $userInfo = new User();
                $userInfo = $userInfo->where("id", "=", $orderInfo->user_id)->first();

                if (sizeof($orderInfo) != 0 && $orderInfo->state == 0) {
                    $oldMoney = $userInfo->money;
                    $userInfo->money = $total_fee + $oldMoney;
                    //更新用户余额信息
                    $userInfo->save();
                    //更新订单信息
                    $orderInfo->yft_order = $yft_order_no;
                    $orderInfo->state = 1;
                    $orderInfo->save();
                    //充值返利处理 start
                    if ($userInfo->ref_by != "" && $userInfo->ref_by != 0 && $userInfo->ref_by != null && Config::get('code_payback') != 0 && Config::get('code_payback') != null) {
                        $gift_user = User::where("id", "=", $userInfo->ref_by)->first();
                        $gift_user->money = ($gift_user->money + ($total_fee * (Config::get('code_payback') / 100)));
                        $gift_user->save();

                        $Payback = new Payback();
                        $Payback->total = $total_fee;
                        $Payback->userid = $userInfo->id;
                        $Payback->ref_by = $userInfo->ref_by;
                        $Payback->ref_get = $total_fee * (Config::get('code_payback') / 100);
                        $Payback->datetime = time();
                        $Payback->save();
                    }
                    //充值返利处理 end
                    //telegram提醒
                    if (Config::get('enable_donate') == 'true' && Config::get("enable_telegram") == 'true') {
                        if ($userInfo->is_hide == 1) {
                            Telegram::Send("感谢！一位不愿透露姓名的大老爷给我们捐了 " . $total_fee . " 元呢~");
                        } else {
                            Telegram::Send("感谢！" . $userInfo->user_name . " 大老爷给我们捐了 " . $total_fee . " 元呢~");
                        }
                    }
                } else {
                    return "success";
                }
                return "success";
            } else {
                return "fail";
            }
        } else {
            //验证失败
            return "fail";
        }
    }

    public function getPurchaseHTML()
    {
        return '
                    <div class="card-main">
                        <div class="card-inner">
                            <form action="/user/code/yft" method="post" target="_blank">
                                
                                    <p class="card-heading">在线充值</p>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="price">充值金额</label>
                                        <input class="form-control" id="price" name="price" type="text">
                                    </div>
                                
                                <div class="card-action">
                                    <div class="card-action-btn pull-left">
                                        <button type="submit" class="btn btn-flat waves-attach" id="yftCoin" ><span class="icon">check</span>&nbsp;充值</button>
                                    </div>
                                </div>
                            </form>
                        </div>
					</div>
				';
    }

    public function getReturnHTML($request, $response, $args)
    {
        // TODO: Implement getReturnHTML() method.
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }

    /**
     * @param $request
     * @return string
     */
    private function constructPayPara($request)
    {
        $yftLib = new QuickPayFunction();
        $pay_config = new YftPayConfig();
        $pay_config->init();

        /**************************请求参数**************************/

        //订单名称
        $subject = $request->getParams()['subject'];//必填

        //付款金额
        $total_fee = $request->getParams()['total_fee'];//必填 需为整数

        //服务器异步通知页面路径
        $notify_url = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . $pay_config->pay_config['notify_url'];

        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . $pay_config->pay_config["return_url"];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        $secret = Config::get('yft_secret');

        $accesskey = Config::get('yft_accesskey');

        //生成订单号
		// 密码字符集，可任意添加你需要的字符
        $date = time();
        $date = "yft".date("YmdHis",$date);
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = "";
        for ($i = 0; $i < 8; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $ss_order_no = $date.$password;

        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = [];
        if ($pay_config->pay_config["type"] == "aliPay") {
            $parameter = [
                "total_fee" => $total_fee,
                "notify_url" => $notify_url,
                "return_url" => $return_url,
                "secret" => $secret,
                "out_trade_no" => $ss_order_no
            ];
        } else {
            $parameter = [
                "secret" => $secret,
                "notify_url" => $notify_url,
                "accesskey" => $accesskey,
                "return_url" => $return_url,
                "subject" => $subject,
                "total_fee" => $total_fee
            ];
        }

        //向数据库插入订单信息
        $yft_order_info = new YftOrder();
        $yft_order_info->user_id = $this->user->id;
        $yft_order_info->ss_order = $ss_order_no;
        $yft_order_info->price = $total_fee;
        $yft_order_info->state = 0;
        $yft_order_info->save();

        //建立请求
        $html_text = $yftLib->buildRequestForm($parameter, $ss_order_no, $pay_config);
        return $html_text;
    }
}