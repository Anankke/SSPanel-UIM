<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/30
 * Time: 8:14 PM
 */

namespace App\Services\Gateway;
use App\Services\View;
use App\Services\Auth;
use App\Services\Config;

use App\Models\Paylist;

class SPay extends AbstractPayment
{

    public function purchase($request, $response, $args)
    {
        /**************************请求参数**************************/
        $amount = $request->getParam()["price"];
        $user = Auth::getUser();
        $alipay_config = Spay_tool::getConfig();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $amount;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $pl->tradeno;
        //订单名称，必填
        $subject = $pl->id."UID:".$user->id." 充值".$amount."元";
        //付款金额，必填
        $total_fee = (float)$amount;
        //商品描述，可空
        $body = $user->id;
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "notify_url"    => $alipay_config['notify_url'],
            "return_url"    => $alipay_config['return_url'],
            "out_trade_no"    => $out_trade_no,
            "total_fee"    => $total_fee
        );
        //建立请求
        $alipaySubmit = new Spay_submit($alipay_config);
        $reqUrl = $alipaySubmit->buildRequestForm($parameter);
        return json_encode(['code'=>0, 'url'=>$reqUrl]);
    }

    public function notify($request, $response, $args)
    {
        //计算得出通知验证结果
        $alipayNotify = new Spay_notify(Spay_tool::getConfig());
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            if ($_POST['trade_status'] == 'TRADE_FINISHED' or $_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $this->postPayment($out_trade_no, "SPay 支付");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";    //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    public function getPurchaseHTML()
    {
        return '
                    <div class="card-inner">
						<div class="form-group pull-left">
                        <p class="modal-title" >本站支持支付宝在线充值</p>
                        <p>输入充值金额：</p>
                        <div class="form-group form-group-label">
                        <label class="floating-label" for="price">充值金额</label>
                        <input id="type" class="form-control maxwidth-edit" name="amount" />
                        </div>
                        <a class="btn btn-flat waves-attach" id="submitSpay" ><span class="icon">check</span>&nbsp;充值</a>
                        </div>
                    </div>
                        <script>
                        window.onload = function(){
        $("#submitSpay").click(function() {
            var price = parseFloat($("#type").val());
            console.log("将要使用 SPay 方法充值" + price + "元");
            if (isNaN(price)) {
                $("#result").modal();
                $("#msg").html("非法的金额!");
            }
            $(\'#readytopay\').modal();
            $("#readytopay").on(\'shown.bs.modal\', function () {
                $.ajax({
                    \'url\': "/user/payment/purchase",
                    \'data\': {
                        \'price\': price,
                    },
                    \'dataType\': \'json\',
                    \'type\': "POST",
                    success: function (data) {
                        if (data.code == 0) {
                            $("#result").modal();
                            $("#msg").html("正在跳转到支付宝...");
                            console.log(data);
                            window.location.href = data.url;
                        } else {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                            console.log(data);
                        }
                    }
                });
            });
        });
    };</script>                        
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
}