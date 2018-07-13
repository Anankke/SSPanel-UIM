<?php
namespace App\Utils;
use App\Models\User;
use App\Models\Code;
use App\Models\Paylist;
use App\Models\Payback;
use App\Services\Config;
class Pay
{
    public static function getHTML($user)
    {
        $driver = Config::get("payment_system");
        switch ($driver) {
            case "doiampay":
                return Pay::doiampay_html($user);
            case "paymentwall":
                return Pay::pmw_html($user);
            case 'spay':
                return Pay::spay_html($user);
            case 'zfbjk':
                return Pay::zfbjk_html($user);
            case 'f2fpay':
                return Pay::f2fpay_html($user);
			case 'yftpay':
                return Pay::yftpay_html($user);
            case 'codepay':
                return Pay::codepay_html($user);
            case 'f2fpay_codepay':
                return Pay::f2fpay_codepay_html($user);
            default:
                return "";
        }
        return null;
    }
    /**
     * DoiamPay
     * @param  User   $user User
     * @return String       HTML
     */
    private static function f2fpay_codepay_html($user)
    {

            return '
                        <p><i class="icon icon-lg">monetization_on</i>&nbsp;余额&nbsp;<font color="red" size="5">'.$user->money.'</font>&nbsp;元</p>

                        <p><img src="/images/qianbai-4.png" height="250" width="200" /></p>
                        <div class="form-group form-group-label">
                        <label class="floating-label" for="number">请选择充值金额</label>
                        <select id="type" class="form-control" name="amount">
                            <option></option>
                            <option value="'.Config::get('amount')[0].'">'.Config::get('amount')[0].'元</option>
                            <option value="'.Config::get('amount')[1].'">'.Config::get('amount')[1].'元</option>
                            <option value="'.Config::get('amount')[2].'">'.Config::get('amount')[2].'元</option>
                            <option value="'.Config::get('amount')[3].'">'.Config::get('amount')[3].'元</option>
                            <option value="'.Config::get('amount')[4].'">'.Config::get('amount')[4].'元</option>
                        </select>
                        </div>
                        <p></p>
                        <button class="btn btn-flat waves-attach" id="urlChange"><img src="/images/alipay.jpg" width="50px" height="50px" /></button>
                        <button class="btn btn-flat waves-attach" onclick="codepay()"><img src="/images/weixin.jpg" width="50px" height="50px" /></button>
                        <script>
                            function codepay() {
                                window.location.href=("/user/code/codepay?type=3&price="+$("#type").val());
                            }
                        </script>
                        ';
    }
    public static function doiampay_html(User $user){
        return \App\Utils\DoiAMPay::render();
    }

    private static function spay_html($user)
    {
        return '
						<form action="/user/alipay" method="get" target="_blank" >
							<h3>支付宝充值</h3>
							<p>充值金额:
              <select id="type" class="form-control" name="amount">
                  <option></option>
                  <option value="'.Config::get('amount')[0].'">'.Config::get('amount')[0].'元</option>
                  <option value="'.Config::get('amount')[1].'">'.Config::get('amount')[1].'元</option>
                  <option value="'.Config::get('amount')[2].'">'.Config::get('amount')[2].'元</option>
                  <option value="'.Config::get('amount')[3].'">'.Config::get('amount')[3].'元</option>
                  <option value="'.Config::get('amount')[4].'">'.Config::get('amount')[4].'元</option>
              </select></p>
							<input type="submit" value="提交" />
						</form>
';
    }
    private static function zfbjk_html($user)
    {
        return '
						<p>请扫码，给我转账来充值，记得备注上 <code>'.$user->id.'</code>。<br></p>
						<img src="'.Config::get('zfbjk_qrcodeurl').'"/>
';
    }
    private static function f2fpay_html($user)
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
                        </div>
';
    }
	private static function yftpay_html($user)
    {
        return '
										<form action="/user/code/yft" method="post" target="_blank">
										<div class="card-inner">
											<p class="card-heading">在线充值</p>
											<p><i class="icon icon-lg">monetization_on</i>&nbsp;余额&nbsp;<font color="red" size="5">'.$user->money.'</font>&nbsp;元</p>
											<p><img src="/images/qianbai-4.png" height="250" width="200" /></p>
											<div class="form-group form-group-label">
												<label class="floating-label" for="price">输入充值金额</label>
												<input class="form-control" id="price" name="price" type="text">
											</div>
										</div>
										<div class="card-action">
											<div class="card-action-btn pull-left">
												<button type="submit" class="btn btn-flat waves-attach" id="yftCoin" ><span class="icon">check</span>&nbsp;充值</button>
											</div>
										</div>
									</form>
';
    }

    private static function codepay_html($user)
    {
        return '
                        <p class="card-heading">请输入充值金额</p>
                        <form name="codepay" action="/user/code/codepay" method="get">
                            <input class="form-control" id="price" name="price" placeholder="输入充值金额后，点击你要付款的应用图标即可" autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
                            <br>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="1" ><img src="/images/alipay.jpg" width="50px" height="50px" /></button>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="2" ><img src="/images/qqpay.jpg" width="50px" height="50px" /></button>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="3" ><img src="/images/weixin.jpg" width="50px" height="50px" /></button>

                        </form>
';
    }

    private static function pmw_html($user)
    {
        \Paymentwall_Config::getInstance()->set(array(
            'api_type' => \Paymentwall_Config::API_VC,
            'public_key' => Config::get('pmw_publickey'),
            'private_key' => Config::get('pmw_privatekey')
        ));
        $widget = new \Paymentwall_Widget(
            $user->id, // id of the end-user who's making the payment
            Config::get('pmw_widget'),      // widget code, e.g. p1; can be picked inside of your merchant account
            array(),     // array of products - leave blank for Virtual Currency API
            array(
                'email' => $user->email,
                'history'=>
                    array(
                    'registration_date'=>strtotime($user->reg_date),
                    'registration_ip'=>$user->reg_ip,
                    'payments_number'=>Code::where('userid', '=', $user->id)->where('type', '=', -1)->count(),
                    'membership'=>$user->class),
                    'customer'=>array(
                        'username'=>$user->user_name
                    )
            ) // additional parameters
        );
        return $widget->getHtmlCode(array("height"=>Config::get('pmw_height'),"width"=>"100%"));
    }
    private static function spay_gen($user, $amount)
    {
        /**************************请求参数**************************/
        $alipay_config = Spay_tool::getConfig();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $amount;
        $pl->save();
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $pl->id;
        //订单名称，必填
        $subject = $pl->id."UID".$user->id." 充值".$amount."元";
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
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        echo $html_text;
        exit(0);
    }
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
        $config = Pay::get_alipay_config();

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
        $goodsDetailList = array();

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
    private static function f2fpay_gen($user, $amount)
    {
        //$qrPayResult = Pay::query_alipay_order(2017052112230123456);
        //return ;
        //生成二维码
        $qrPayResult = Pay::alipay_get_qrcode($user, $amount, $qrPay);

        //  根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()){
            case "SUCCESS":
                echo "支付金额: RMB ".$amount." 元";
                echo "确认无误后请用支付宝App扫描二维码支付："."<br>---------------------------------------<br>";
                $response = $qrPayResult->getResponse();
                $qrcode = $qrPay->create_erweima($response->qr_code);
                echo $qrcode."<br>";
                break;
            case "FAILED":
                echo "支付宝创建订单二维码失败!!!"."<br>--------------------------<br>";
                if(!empty($qrPayResult->getResponse())){
                    print_r($qrPayResult->getResponse());
                }
                echo "请使用其他方式付款。";
                break;
            case "UNKNOWN":
                echo "系统异常，状态未知!!!"."<br>--------------------------<br>";
                if(!empty($qrPayResult->getResponse())){
                    print_r($qrPayResult->getResponse());
                }
                echo "请使用其他方式付款。";
                break;
            default:
                echo "创建订单二维码返回异常!!!"."<br>--------------------------<br>";
                echo "请使用其他方式付款。";
                break;
        }

        if ($qrPayResult->getTradeStatus()) {
            sleep(1);
            echo "轮询处理：";
        }

        return ;
    }
    public static function getGen($user, $amount)
    {
        $driver = Config::get("payment_system");
        switch ($driver) {
            case "paymentwall":
                return Pay::pmw_html();
            case 'spay':
                return Pay::spay_gen($user, $amount);
            case 'zfbjk':
                return Pay::alipay_html();
            case 'f2fpay':
               return Pay::f2fpay_gen($user, $amount);
            default:
                return "";
        }
        return null;
    }
    private static function spay_callback()
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
              $trade = Paylist::where("id", '=', $out_trade_no)->where('status', 0)->where('total', $_POST['total_fee'])->first();
              if ($trade == null) {
                  exit("success");
              }
              $trade->tradeno = $trade_no;
              $trade->status = 1;
              $trade->save();
              //status
              $trade_status = $_POST['trade_status'];
            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $user=User::find($trade->userid);
                $user->money=$user->money+$_POST['total_fee'];
                $user->save();
                $codeq=new Code();
                $codeq->code="支付宝 充值";
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$_POST['total_fee'];
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();
                    $Payback=new Payback();
                    $Payback->total=$_POST['total_fee'];
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }
            } elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $user=User::find($trade->userid);
                $user->money=$user->money+$_POST['total_fee'];
                $user->save();
                $codeq=new Code();
                $codeq->code="支付宝 充值";
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$_POST['total_fee'];
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();
                    $Payback=new Payback();
                    $Payback->total=$_POST['total_fee'];
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }
            }
              //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
              echo "success";    //请不要修改或删除
              if (Config::get('enable_donate') == 'true') {
                  if ($user->is_hide == 1) {
                      Telegram::Send("姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元呢~");
                  } else {
                      Telegram::Send("姐姐姐姐，".$user->user_name." 大老爷给我们捐了 ".$codeq->number." 元呢~");
                  }
              }
              /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
          echo "fail";
          //调试用，写文本函数记录程序运行情况是否正常
          //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
    private static function pmw_callback()
    {
        if (Config::get('pmw_publickey')!="") {
            \Paymentwall_Config::getInstance()->set(array(
                'api_type' => \Paymentwall_Config::API_VC,
                'public_key' => Config::get('pmw_publickey'),
                'private_key' => Config::get('pmw_privatekey')
            ));
            $pingback = new \Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
            if ($pingback->validate()) {
                $virtualCurrency = $pingback->getVirtualCurrencyAmount();
                if ($pingback->isDeliverable()) {
                    // deliver the virtual currency
                } elseif ($pingback->isCancelable()) {
                    // withdraw the virual currency
                }
                $user=User::find($pingback->getUserId());
                $user->money=$user->money+$pingback->getVirtualCurrencyAmount();
                $user->save();
                $codeq=new Code();
                $codeq->code="Payment Wall 充值";
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$pingback->getVirtualCurrencyAmount();
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();
                    $Payback=new Payback();
                    $Payback->total=$pingback->getVirtualCurrencyAmount();
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }
                echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
                if (Config::get('enable_donate') == 'true') {
                    if ($user->is_hide == 1) {
                        Telegram::Send("姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元呢~");
                    } else {
                        Telegram::Send("姐姐姐姐，".$user->user_name." 大老爷给我们捐了 ".$codeq->number." 元呢~");
                    }
                }
            } else {
                echo $pingback->getErrorSummary();
            }
        } else {
            echo 'error';
        }
    }
    private static function zfbjk_callback($request)
    {
        //您在www.zfbjk.com的商户ID
        $alidirect_pid = Config::get("zfbjk_pid");
        //您在www.zfbjk.com的商户密钥
        $alidirect_key = Config::get("zfbjk_key");
        $tradeNo = $request->getParam('tradeNo');
        $Money = $request->getParam('Money');
        $title = $request->getParam('title');
        $memo = $request->getParam('memo');
        $alipay_account = $request->getParam('alipay_account');
        $Gateway = $request->getParam('Gateway');
        $Sign = $request->getParam('Sign');
        if (!is_numeric($title)) {
            exit("fail");
        }
        if (strtoupper(md5($alidirect_pid . $alidirect_key . $tradeNo . $Money . $title . $memo)) == strtoupper($Sign)) {
            $trade = Paylist::where("tradeno", '=', $tradeNo)->first();
            if ($trade != null) {
                exit("success");
            } else {
                $user=User::where('id', '=', $title)->first();
                if ($user == null) {
                    exit("IncorrectOrder");
                }
                $pl = new Paylist();
                $pl->userid=$title;
                $pl->tradeno=$tradeNo;
                $pl->total=$Money;
                $pl->datetime=time();
                $pl->status=1;
                $pl->save();
                $user->money=$user->money+$Money;
                $user->save();
                $codeq=new Code();
                $codeq->code="支付宝充值";
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$Money;
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();
                    $Payback=new Payback();
                    $Payback->total=$Money;
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }
                if (Config::get('enable_donate') == 'true') {
                    if ($user->is_hide == 1) {
                        Telegram::Send("姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元呢~");
                    } else {
                        Telegram::Send("姐姐姐姐，".$user->user_name." 大老爷给我们捐了 ".$codeq->number." 元呢~");
                    }
                }
                exit("Success");
            }
        } else {
            exit('Fail');
        }
    }
    private static function f2fpay_callback()
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
            if($trade_status == 'TRADE_FINISHED'||$trade_status == 'TRADE_SUCCESS') {

                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                    //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                //更新订单状态
                $trade->tradeno = $trade_no;
                $trade->status = 1;
                $trade->save();

                //更新用户账户
				$user=User::find($trade->userid);
                $user->money=$user->money+$_POST['total_amount'];
                $user->save();

                //更新充值（捐赠）记录
                $codeq=new Code();
                $codeq->code="支付宝 充值";
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$_POST['total_amount'];
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                //更新返利
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();

                    $Payback=new Payback();
                    $Payback->total=$_POST['total_amount'];
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }

                if (Config::get('enable_donate') == 'true') {
                    if ($user->is_hide == 1) {
                        Telegram::Send("一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元!");
                    } else {
                        Telegram::Send($user->user_name." 大老爷给我们捐了 ".$codeq->number." 元！");
                    }
                }

                //业务处理完毕，向支付宝系统返回成功
                echo "success";     //请不要修改或删除
            }
        }else {
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }

    private static function codepay_callback(){
        //以下五行无需更改
        ksort($_POST); //排序post参数
        reset($_POST); //内部指针指向数组中的第一个元素
        $codepay_key=Config::get('codepay_key'); //这是您的密钥
        $sign = '';//初始化
        foreach ($_POST AS $key => $val) { //遍历POST参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }
        if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
            exit('fail'); //返回失败，等待下次回调
        } else { //合法的数据
            //业务处理
            $pay_id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
            $money = (float)$_POST['money']; //实际付款金额
            $price = (float)$_POST['price']; //订单的原价
            //$param = $_POST['param']; //自定义参数
            $pay_no = $_POST['pay_no']; //流水号
            $codeq=Code::where("code", "=", $pay_no)->first();
            if ($codeq==null){
                $user=User::find($pay_id);
                $codeq=new Code();
                $codeq->code=$pay_no;
                $codeq->isused=1;
                $codeq->type=-1;
                $codeq->number=$price;
                $codeq->usedatetime=date("Y-m-d H:i:s");
                $codeq->userid=$user->id;
                $codeq->save();
                $user->money=$user->money+$price;
                $user->save();

                //更新返利
                if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
                    $gift_user=User::where("id", "=", $user->ref_by)->first();
                    $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
                    $gift_user->save();

                    $Payback=new Payback();
                    $Payback->total=$price;
                    $Payback->userid=$user->id;
                    $Payback->ref_by=$user->ref_by;
                    $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
                    $Payback->datetime=time();
                    $Payback->save();
                }

                if (Config::get('enable_donate') == 'true') {
                    if ($user->is_hide == 1) {
                        Telegram::Send("一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元!");
                    } else {
                        Telegram::Send($user->user_name." 大老爷给我们捐了 ".$codeq->number." 元！");
                    }
                }
            }

            exit('success'); //返回成功 不要删除哦
        }

        return;
    }

   private static function notify(){
        //系统订单号
        $trade_no = $_POST['pay_no'];
        //交易用户
        $trade_id = strtok($_POST['pay_id'], "@");
        //金额
        $trade_num = $_POST['price'];
        $param = urldecode($_POST['param']);
        $codeq=Code::where("code", "=", $trade_no)->first();
        if($codeq!=null){
            exit('success'); //说明数据已经处理完毕
            return;
        }
        if($param!=Config::get('alipay')||$trade_no==''){ //鉴权失败
            exit('fail');
            return;
        }

        //更新用户账户
        $user=User::find($trade_id);
        $codeq=new Code();
        $codeq->code=$trade_no;
        $codeq->isused=1;
        $codeq->type=-1;
        $codeq->number=$_POST['price'];
        $codeq->usedatetime=date("Y-m-d H:i:s");
        $codeq->userid=$user->id;
        $codeq->save();
        $user->money=$user->money+$trade_num;
        $user->save();
        //更新返利
        if ($user->ref_by!=""&&$user->ref_by!=0&&$user->ref_by!=null) {
            $gift_user=User::where("id", "=", $user->ref_by)->first();
            $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
            $gift_user->save();

            $Payback=new Payback();
            $Payback->total=$trade_num;
            $Payback->userid=$user->id;
            $Payback->ref_by=$user->ref_by;
            $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
            $Payback->datetime=time();
            $Payback->save();
        }
        exit('success'); //返回成功 不要删除哦
    }

    public static function callback($request)
    {
        $driver = Config::get("payment_system");
        switch ($driver) {
            case "paymentwall":
                return Pay::pmw_callback();
            case 'spay':
                return Pay::spay_callback();
            case 'zfbjk':
                return Pay::zfbjk_callback($request);
            case 'f2fpay':
                return Pay::f2fpay_callback();
            case 'codepay':
                return Pay::codepay_callback();
            default:
                return "";
        }
        return null;
    }

    public static function f2fpay_pay_callback($request)
    {
        return Pay::f2fpay_callback();
    }

    public static function codepay_pay_callback($request)
    {
        return Pay::codepay_callback();
    }
}
