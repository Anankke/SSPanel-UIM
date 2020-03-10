<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10
 * Time: 17:03
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Services\Auth;

class YftPay extends AbstractPayment
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    public function purchase($request, $response, $args)
    {
        return $this->constructPayPara($request);
    }

    public function notify($request, $response, $args)
    {
        $yftUtil = new YftPayUtil();
        $pay_config = new YftPayConfig();
        $pay_config->init();
        //价格
        $total_fee = $request->getParam("total_fee");
        //易付通返回的订单号
        $yft_order_no = $request->getParam("trade_no");
        //面板生成的订单号
        $ss_order_no = $request->getParam("out_trade_no");
        //订单说明
        $subject = $request->getParam("subject");
        //付款状态
        $trade_status = $request->getParam("trade_status");
        //加密验证字符串
        $sign = $request->getParam("sign");
        $verifyNotify = $yftUtil->md5Verify(floatval($total_fee), $ss_order_no, $yft_order_no, $trade_status, $sign);
        if ($verifyNotify && $trade_status == 'TRADE_SUCCESS') {//验证成功
            /*
            加入您的入库及判断代码;
            >>>>>>>！！！为了保证数据传达到回调地址，会请求4次。所以必须要先判断订单状态，然后再插入到数据库，这样后面即使请求3次，也不会造成订单重复！！！！<<<<<<<
            判断返回金额与实金额是否想同;
            判断订单当前状态;
            完成以上才视为支付成功
            */
            $order = Paylist::where('tradeno', $ss_order_no)->first();
            if ($order->status == 0) {
                $this->postPayment($ss_order_no, '易付 充值');
                return "success";
            }
            return "fail";
        } else {
            //验证失败
            return "fail";
        }
    }

    /**
     * @param $request
     * @return string
     */
    private function constructPayPara($request)
    {
        $yftLib = new YftPayUtil();
        $pay_config = new YftPayConfig();
        $pay_config->init();
        /**************************请求参数**************************/
        //订单名称
        $subject = $request->getParams()['subject'];//必填
        //付款金额
        $total_fee = $request->getParams()['total_fee'];//必填 需为整数
        //服务器异步通知页面路径
        $notify_url = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . "/payment/notify";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . $pay_config->pay_config["return_url"];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        $secret = $_ENV['yft_secret'];
        $ss_order_no = self::genOrderNumber();
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = [
            "total_fee" => $total_fee,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "secret" => $secret,
            "out_trade_no" => $ss_order_no
        ];
        //向数据库插入订单信息
        $order = new Paylist();
        $order->userid = $this->user->id;
        $order->total = $total_fee;
        $order->status = 0;
        $order->tradeno = $ss_order_no;
        $order->save();
        //建立请求
        $html_text = $yftLib->buildRequestForm($parameter, $ss_order_no, $pay_config);
        return $html_text;
    }

    public function getPurchaseHTML()
    {
        return '
                    <div class="card-main">
                        <div class="card-inner">
                            <form action="/user/payment/purchase" method="post" target="_blank" onsubmit="return checkPost();">
                                    <p class="card-heading">在线充值</p>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="total_fee">充值金额</label>
                                        <input class="form-control" id="total_fee" name="total_fee" type="text">
                                        <input class="form-control" name="subject" value="余额充值" type="hidden">
                                    </div>
                                <div class="card-action">
                                    <div class="card-action-btn pull-left">
                                        <button type="submit" class="btn btn-flat waves-attach" ><span class="icon">check</span>&nbsp;充值</button>
                                    </div>
                                </div>
                            </form>
                        </div>
					</div>
					<script>
					    function checkPost(){
					        var price = document.getElementById("total_fee");
					        if (price.value == ""){
                                  $("#result").modal();
                                  $$.getElementById("msg").innerHTML = "请填写充值金额";
                                  document.getElementById("total_fee").focus();
                                  return false;
                            }
					        if (price.value < 0.01){
                                  $("#result").modal();
                                  $$.getElementById("msg").innerHTML = "请填写充值金额";
                                  document.getElementById("total_fee").focus();
                                  return false;
                            }
                            return true;
					    }
                    </script>
                    <script>
            //jquery控制只输入数字或小数点后几位
            $(function () {

                $.fn.decimalinput = function (intLen, decimallen, isNegative) {
                    curIntLen = intLen || 11;
                    decimallen = decimallen || 0;
                    isNegative = typeof isNegative == \'boolean\' ? isNegative : false;

                    $(this).css("ime-mode", "disabled");

                    /*
                     KeyPress (主要用来接收字母、数字等ANSI字符)
                         1.不区分小键盘和主键盘的数字字符。
                         2.区分大小写。

                     数字 1
                     主键盘区keyCode:49
                     小键盘区keyCode:49

                       KeyDown 、KeyUp  (可以捕获键盘上除了PrScrn（在键盘右上角）之外的所有按键,可以捕获组合键)
                           1.区分小键盘和主键盘的数字字符。
                           2.不区分大小写。

                       数字 1
                     主键盘区keyCode:49
                     小键盘区keyCode:97
                     */

                    this.bind("keypress", function (e) {
                        //微软中文输入法 状态下，无法监听 keypress
                        var _v = this.value;
                        if (e.charCode === 0) return true; //非字符键 for firefox
                        var keyCode = (e.keyCode ? e.keyCode : e.which); //兼容火狐 IE
                        var curPos = getCurPosition(this);
                        var selText = getSelectedText(this);
                        var dotPos = _v.indexOf(".");
                        var curLength = _v.length;
                        console.log(curPos)

                        if (isNegative && keyCode == 45 && curPos == 0) {
                            curIntLen = intLen + 1;
                            return true;
                        }
                        curIntLen = _v.indexOf(\'-\') > -1 ? intLen + 1 : intLen;
                        if (keyCode >= 48 && keyCode <= 57) {
                            //整数位 长度控制
                            if (dotPos > -1) { //存在小数点情况
                                if (curPos <= dotPos && dotPos >= curIntLen) {
                                    return false;
                                }
                            } else {
                                if ((curLength + 1) > curIntLen && keyCode != 46) {
                                    return false;
                                }
                            }
                            //小数位 长度控制
                            if (dotPos > 0 && curPos > dotPos) {
                                if (curPos > dotPos + decimallen) return false;
                                if (selText.length > 0 || _v.substr(dotPos + 1).length < decimallen)
                                    return true;
                                else
                                    return false;
                            }
                            return true;
                        }

                        //输入"."
                        // 输入的是小数点，并且参数可以有小数，不可在字符首位输入小数点，不存在小数点
                        if (keyCode == 46 && decimallen > 0 && curPos > 0 && _v.indexOf(".") < 0) {
                            return true;
                        }
                        return false;
                    });
                    this.bind("keydown", function (e) {
                        var _v = this.value;
                        var dotPos = _v.indexOf(".");
                        var curPos = getCurPosition(this);
                        var selText = getSelectedText(this);

                        //只能全选删除
                        if (e.keyCode == 8 && selText.length > 0 && selText.length != _v.length) {
                            return false;
                        }
                        //删除键，存在小数点，光标在小数点后 ，删除小数点的后的长度不能超过 整数长度限制，
                        if (e.keyCode == 8 &&
                            dotPos > 0 &&
                            curPos == (dotPos + 1) &&
                            (_v.length - 1) > curIntLen) {
                            return false;
                        }

                    })
                    this.bind("blur", function () {
                        if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
                            this.value = this.value.substr(0, this.value.length - 1);
                        } else if (isNaN(this.value)) {
                            this.value = "";
                        }
                        if (this.value) {
                            var v = parseFloat(this.value).toFixed(decimallen);
                            if (v.indexOf(\'.\') > curIntLen) {
                                v = v.substr(v.indexOf(\'.\') - curIntLen)
                            }
                            this.value = v;
                        }
                        $(this).trigger("input");
                    });
                    this.bind("paste", function () {
                        return false;
                    });
                    this.bind("dragenter", function () { return false; });

                    this.bind("propertychange", function (e) {
                        if (isNaN(this.value))
                            this.value = this.value.replace(/[^0-9\.-]/g, "");
                    });
                    this.bind("input", function (e) {
                        if (isNaN(this.value))
                            this.value = this.value.replace(/[^0-9\.-]/g, "");
                    });
                    //获取当前光标在文本框的位置
                    function getCurPosition(domObj) {
                        var curPosition = 0;
                        if (domObj.selectionStart || domObj.selectionStart == \'0\') {
                            curPosition = domObj.selectionStart;
                        } else if (document.selection) { //for IE
                            domObj.focus();
                            var currentRange = document.selection.createRange();
                            var workRange = currentRange.duplicate();
                            domObj.select();
                            var allRange = document.selection.createRange();
                            while (workRange.compareEndPoints("StartToStart", allRange) > 0) {
                                workRange.moveStart("character", -1);
                                curPosition++;
                            }
                            currentRange.select();
                        }
                        return curPosition;
                    }
                    //获取当前文本框选中的文本
                    function getSelectedText(domObj) {
                        if (domObj.selectionStart || domObj.selectionStart == \'0\') {
                            return domObj.value.substring(domObj.selectionStart, domObj.selectionEnd);
                        } else if (document.selection) { //for IE
                            domObj.focus();
                            var sel = document.selection.createRange();
                            return sel.text;
                        } else return \'\';
                    }

                };

                $(\'#total_fee\').decimalinput(5, 2, true);
            })

        </script>
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

    private function genOrderNumber()
    {
        //生成订单号
        // 密码字符集，可任意添加你需要的字符
        $date = time();
        $date = "yft" . date("YmdHis", $date);
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = "";
        for ($i = 0; $i < 8; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $date . $password;
    }
}
