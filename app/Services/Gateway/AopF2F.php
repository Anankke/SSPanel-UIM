<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午9:24
 */

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Services\Config;
use App\Models\User;
use App\Models\Code;
use App\Models\Paylist;
use App\Utils\Pay;


class AopF2F extends AbstractPayment
{
    private static function get_alipay_config()
    {
        //获取支付宝接口配置
        $config = array (
            //签名方式,默认为RSA2(RSA2048)
            'sign_type' => "RSA2",
            //支付宝公钥
            'alipay_public_key' => Config::get("alipay_public_key"),
            //商户私钥
            'merchant_private_key' => Config::get("merchant_private_key"),
            //编码格式
            'charset' => "UTF-8",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //应用ID
            'app_id' => Config::get("f2fpay_app_id"),
            //异步通知地址,只有扫码支付预下单可用
            'notify_url' => Config::get("baseUrl")."/pay_callback",
            //最大查询重试次数
            'MaxQueryRetry' => "10",
            //查询间隔
            'QueryDuration' => "3"
        );

        return $config;
    }

    public static function alipay_get_qrcode($user, $amount, &$qrPay)
    {
        //创建订单
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $amount;
        $pl->save();

        //获取支付宝接口配置
        $config = self::get_alipay_config();

        //$timestamp
        /**************************请求参数**************************/
        // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
        // 需保证商户系统端不能重复，建议通过数据库sequence生成，
        $outTradeNo = $pl->id."alipay".date('Ymdhis').mt_rand(100,1000);

        // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
        $subject = "￥".$pl->total." - ".Config::get("appName")." - {$user->user_name}({$user->email})";

        // (必填) 订单总金额，单位为元，不能超过1亿元
        // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
        $totalAmount = $pl->total;

        // (不推荐使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
        // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
        //String discountableAmount = "1.00"; //

        // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
        // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
        $undiscountableAmount = "0.01";

        // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
        // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
        //$sellerId = "";

        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = "用户名:".$user->user_name." 用户ID:".$user->id." 用户充值共计".$pl->total."元";

        //商户操作员编号，添加此参数可以为商户操作员做销售统计
        $operatorId = "bak_admin0001";

        // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
        $storeId = "bak_store001";

        // 支付宝的店铺编号
        //$alipayStoreId= "2016041400077000000003314986";


        // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
        $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
        $extendParams = new \ExtendParams();
        $extendParams->setSysServiceProviderId($providerId);
        $extendParamsArr = $extendParams->getExtendParams();

        // 支付超时，线下扫码交易定义为5分钟
        $timeExpress = "5m";

        // 商品明细列表，需填写购买商品详细信息，

        // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
        $goods1 = new \GoodsDetail();
        $goods1->setGoodsId($pl->total);
        $goods1->setGoodsName("充值");
        $goods1->setPrice($pl->total);
        $goods1->setQuantity(1);
        //得到商品1明细数组
        $goods1Arr = $goods1->getGoodsDetail();
        $goodsDetailList = array($goods1Arr);

        //第三方应用授权令牌,商户授权系统商开发模式下使用
        $appAuthToken = "";//根据真实值填写

        // 创建请求builder，设置请求参数
        $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
        $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
        $qrPayRequestBuilder->setTotalAmount($totalAmount);
        $qrPayRequestBuilder->setTimeExpress($timeExpress);
        $qrPayRequestBuilder->setSubject($subject);
        $qrPayRequestBuilder->setBody($body);
        $qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
        $qrPayRequestBuilder->setExtendParams($extendParamsArr);
        $qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
        $qrPayRequestBuilder->setStoreId($storeId);
        $qrPayRequestBuilder->setOperatorId($operatorId);
        //$qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
        $qrPayRequestBuilder->setAppAuthToken($appAuthToken);

        // 调用qrPay方法获取当面付应答
        $qrPay = new \AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);

        return $qrPayResult;
    }

    function init()
    {
        // TODO: Implement init() method.
    }

    function setMethod($method)
    {
        // TODO: Implement setMethod() method.
    }

    function setNotifyUrl()
    {
        // TODO: Implement setNotifyUrl() method.
    }

    function setReturnUrl()
    {
        // TODO: Implement setReturnUrl() method.
    }

    function purchase($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        if ($amount == "") {
            $res['ret'] = 0;
            $res['msg'] = "订单金额错误：" . $amount;
            return $response->getBody()->write(json_encode($res));
        }
        $user = Auth::getUser();


        //生成二维码
        $qrPayResult = self::alipay_get_qrcode($user, $amount, $qrPay);


        //  根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {

            case "SUCCESS":
                $aliresponse = $qrPayResult->getResponse();
                $res['ret'] = 1;
                $res['msg'] = "二维码生成成功";
                $res['amount'] = $amount;
                $res['qrcode'] = $qrPay->create_erweima($aliresponse->qr_code);

                break;
            case "FAILED":
                $res['ret'] = 0;
                $res['msg'] = "支付宝创建订单二维码失败!!! 请使用其他方式付款。";

                break;
            case "UNKNOWN":
                $res['ret'] = 0;
                $res['msg'] = "系统异常，状态未知!!!!!! 请使用其他方式付款。";

                break;
            default:
                $res['ret'] = 0;
                $res['msg'] = "创建订单二维码返回异常!!!!!! 请使用其他方式付款。";

                break;
        }

        return $response->getBody()->write(json_encode($res));
    }

    protected function notify($request, $response, $args)
    {
        $aop = new \AopClient();
        $alipayrsaPublicKey = Config::get("alipay_public_key");
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;

        //获取支付宝返回参数
        $arr=$_POST;
        //调用验签的方法
        $result = $aop->rsaCheckV1($arr,$alipayrsaPublicKey,$_POST['sign_type']);
        if($result) {//验证成功
            //系统订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            // 查询系统订单
            $alipayPID = Config::get("f2fpay_p_id");

            if ($_POST['seller_id']!=$alipayPID){
                exit("success");
            }

            $trade = Paylist::where("id", '=', $out_trade_no)->where('status', 0)->where('total', $_POST['total_amount'])->first();
            if ($trade == null) {//没有符合的订单，或订单已经处理
                exit("success");
            }

            //订单查询到，处理业务
            if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                self::postPayment($trade_no, "支付宝(当面付)");

                //业务处理完毕，向支付宝系统返回成功
                echo "success";     //请不要修改或删除
            }
        } else {
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }

    protected function sign()
    {
        return 0;
    }

    function getPurchaseHTML()
    {
        return '
                        <div class="form-group pull-left">
                        <p class="modal-title" >本站支持支付宝在线充值</p>
                        <p>输入充值金额：</p>
                        <div class="form-group form-group-label">
                        <label class="floating-label" for="price">充值金额</label>
                        <input id="type" class="form-control" name="amount" />
                        </div>
                        <a class="btn btn-flat waves-attach" id="urlChange" ><span class="icon">check</span>&nbsp;充值</a>
                        </div>
                        <div class="form-group pull-right">
                        <img src="/images/qianbai-4.png" height="205" width="166" />
                        </div>';
    }

    function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    function getStatus($request, $response, $args)
    {
        $time = $request->getQueryParams()["time"];
        $codes = Code::where('userid', '=', $this->user->id)->where('usedatetime', '>', date('Y-m-d H:i:s', $time))->first();
        if ($codes != null && strpos($codes->code, "充值") !== false) {
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        } else {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }
    }
}