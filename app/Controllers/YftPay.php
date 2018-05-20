<?php
/**
 * Created by 傲慢与偏见.
 * OSUser: D-L
 * Date: 2017/10/12
 * Time: 21:08
 */

namespace App\Controllers;


use App\Models\User;
use App\Models\YftOrder;
use App\Services\Auth;
use App\Utils\YftOrderNumUtil;
use App\Services\Config;

class YftPay extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    public function yft($request, $response, $args)
    {
        $price = $request->getParams()['price'];
        return $this->view()->assign('price', $price)->display('user/yft.tpl');
    }

    public function yftPay($request, $response, $args)
    {

        $yftLib = new QuickPayFunction();
        $pay_config = new PayConfig();
        $pay_config->init();

        /**************************请求参数**************************/

        //订单名称
        $subject = $request->getParams()['subject'];//必填

        //付款金额
        $total_fee = $request->getParams()['total_fee'];//必填 需为整数

        //服务器异步通知页面路径
        $notify_url = $request->getUri()->getScheme()."://".$request->getUri()->getHost().$pay_config->pay_config['notify_url'];

        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $request->getUri()->getScheme()."://".$request->getUri()->getHost().$pay_config->pay_config["return_url"];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        $secret = Config::get('yft_secret');

        $accesskey = Config::get('yft_accesskey');

        //生成订单号
        $ss_order_no = YftOrderNumUtil::generate_yftOrder(8);

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
        $html_text = $yftLib->buildRequestForm($parameter, $ss_order_no,$pay_config);
        return $html_text;

    }

    public function yftPayResult($request, $response, $args)
    {
        $newResponse = $response->withStatus(302)->withHeader('Location', '/user/code');
        $yftLib = new QuickPayFunction();
        $pay_config = new PayConfig();
        $pay_config->init();

        //价格
        $total_fee = $request->getParams()['total_fee'];//必填
        //付款状态
        $trade_status = $request->getParams()['trade_status'];//必填
        //加密验证字符串
        $sign = $request->getParams()['sign'];//必填
        //易付通返回的订单号
        $yft_order_no = $request->getParams()['yft_order_no'];
        //面板生成的订单号
        $ss_order_no = $request->getParams()['ss_order_no'];//必填

        $verifyNotify = $yftLib->md5Verify(floatval($total_fee), $trade_status, $pay_config->pay_config['secret'], $pay_config->pay_config['accesskey'], $sign);
        if ($verifyNotify) {//验证成功
            if ($_REQUEST['trade_status'] == 'success') {
                /*
                加入您的入库及判断代码;
                >>>>>>>！！！为了保证数据传达到回调地址，会请求4次。所以必须要先判断订单状态，然后再插入到数据库，这样后面即使请求3次，也不会造成订单重复！！！！<<<<<<<
                判断返回金额与实金额是否想同;
                判断订单当前状态;
                完成以上才视为支付成功
                */
                $price = $request->getParams()['total_fee'];
                $payInfo = YftOrder::where('ss_order', '=', $ss_order_no)->orderBy('id', 'desc')->first();
                $user = User::where('id', '=', $payInfo->user_id)->orderBy('id', 'desc')->first();
                if ($payInfo != null && $payInfo->state == 0) {
                    $old = $user->money;
                    $user->money = $price + $old;
                    $user->save();
                    $payInfo->yft_order = $yft_order_no;
                    $payInfo->state = 1;
                    $payInfo->save();
                } else {
                    echo "订单号异常!请联系管理员！";
                    sleep(2);
                    return $newResponse;
                }

                echo "success";
                return "success";
            } else {
                echo "fail";
                return "fail";
            }

        } else {
            //验证失败
            echo "订单信息异常！请联系管理员";
            sleep(2);
            return $newResponse;
        }
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

        return $this->view()->assign('orderList', $orderList)->assign('countPage', $countPage)->assign('currentPage', $pageNum)->display('user/yftOrder.tpl');
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

        return $this->view()->assign('orderList', $orderList)->assign('countPage', $countPage)->assign('currentPage', $pageNum)->display('admin/yftOrder.tpl');
    }

}