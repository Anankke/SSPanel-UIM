<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/20
 * Time: 下午3:31
 */
header("Content-type: text/html; charset=utf-8");
require_once 'model/builder/AlipayTradePayContentBuilder.php';
require_once 'service/AlipayTradeService.php';

if (!empty($_POST['out_trade_no'])&& trim($_POST['out_trade_no'])!="") {
    // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
    // 需保证商户系统端不能重复，建议通过数据库sequence生成，
    //$outTradeNo = "barpay" . date('Ymdhis') . mt_rand(100, 1000);
    $outTradeNo = $_POST['out_trade_no'];

    // (必填) 订单标题，粗略描述用户的支付目的。如“XX品牌XXX门店消费”
    $subject = $_POST['subject'];

    // (必填) 订单总金额，单位为元，不能超过1亿元
    // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
    $totalAmount = $_POST['total_amount'];

    // (必填) 付款条码，用户支付宝钱包手机app点击“付款”产生的付款条码
    $authCode = $_POST['auth_code']; //28开头18位数字

    // (可选,根据需要使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
    // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
    //String discountableAmount = "1.00"; //

    // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
    // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
    $undiscountableAmount = "0.01";

    // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
    // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
    $sellerId = "";

    // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
    $body = "购买商品2件共15.00元";

    //商户操作员编号，添加此参数可以为商户操作员做销售统计
    $operatorId = "test_operator_id";

    // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
    $storeId = "test_store_id";

    // 支付宝的店铺编号
    $alipayStoreId = "test_alipay_store_id";

    // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，详情请咨询支付宝技术支持
    $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
    $extendParams = new ExtendParams();
    $extendParams->setSysServiceProviderId($providerId);
    $extendParamsArr = $extendParams->getExtendParams();

    // 支付超时，线下扫码交易定义为5分钟
    $timeExpress = "5m";

    // 商品明细列表，需填写购买商品详细信息，
    $goodsDetailList = array();

    // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
    $goods1 = new GoodsDetail();
    $goods1->setGoodsId("good_id001");
    $goods1->setGoodsName("XXX商品1");
    $goods1->setPrice(3000);
    $goods1->setQuantity(1);
    //得到商品1明细数组
    $goods1Arr = $goods1->getGoodsDetail();

    // 继续创建并添加第一条商品信息，用户购买的产品为“xx牙刷”，单价为5.05元，购买了两件
    $goods2 = new GoodsDetail();
    $goods2->setGoodsId("good_id002");
    $goods2->setGoodsName("XXX商品2");
    $goods2->setPrice(1000);
    $goods2->setQuantity(1);
    //得到商品1明细数组
    $goods2Arr = $goods2->getGoodsDetail();

    $goodsDetailList = array($goods1Arr, $goods2Arr);

    //第三方应用授权令牌,商户授权系统商开发模式下使用
    $appAuthToken = "";//根据真实值填写

    // 创建请求builder，设置请求参数
    $barPayRequestBuilder = new AlipayTradePayContentBuilder();
    $barPayRequestBuilder->setOutTradeNo($outTradeNo);
    $barPayRequestBuilder->setTotalAmount($totalAmount);
    $barPayRequestBuilder->setAuthCode($authCode);
    $barPayRequestBuilder->setTimeExpress($timeExpress);
    $barPayRequestBuilder->setSubject($subject);
    $barPayRequestBuilder->setBody($body);
    $barPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
    $barPayRequestBuilder->setExtendParams($extendParamsArr);
    $barPayRequestBuilder->setGoodsDetailList($goodsDetailList);
    $barPayRequestBuilder->setStoreId($storeId);
    $barPayRequestBuilder->setOperatorId($operatorId);
    $barPayRequestBuilder->setAlipayStoreId($alipayStoreId);

    $barPayRequestBuilder->setAppAuthToken($appAuthToken);

    // 调用barPay方法获取当面付应答
    $barPay = new AlipayTradeService($config);
    $barPayResult = $barPay->barPay($barPayRequestBuilder);

    switch ($barPayResult->getTradeStatus()) {
        case "SUCCESS":
            echo "支付宝支付成功:" . "<br>--------------------------<br>";
            print_r($barPayResult->getResponse());
            break;
        case "FAILED":
            echo "支付宝支付失败!!!" . "<br>--------------------------<br>";
            if (!empty($barPayResult->getResponse())) {
                print_r($barPayResult->getResponse());
            }
            break;
        case "UNKNOWN":
            echo "系统异常，订单状态未知!!!" . "<br>--------------------------<br>";
            if (!empty($barPayResult->getResponse())) {
                print_r($barPayResult->getResponse());
            }
            break;
        default:
            echo "不支持的交易状态，交易返回异常!!!";
            break;
    }
    return;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>支付宝当面付 条码支付</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        *{
            margin:0;
            padding:0;
        }
        ul,ol{
            list-style:none;
        }
        .title{
            color: #ADADAD;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 16px 5px 10px;
        }
        .hidden{
            display:none;
        }

        .new-btn-login-sp{
            border:1px solid #D74C00;
            padding:1px;
            display:inline-block;
        }

        .new-btn-login{
            background-color: transparent;
            background-image: url("../img/new-btn-fixed.png");
            border: medium none;
        }
        .new-btn-login{
            background-position: 0 -198px;
            width: 82px;
            color: #FFFFFF;
            font-weight: bold;
            height: 28px;
            line-height: 28px;
            padding: 0 10px 3px;
        }
        .new-btn-login:hover{
            background-position: 0 -167px;
            width: 82px;
            color: #FFFFFF;
            font-weight: bold;
            height: 28px;
            line-height: 28px;
            padding: 0 10px 3px;
        }
        .bank-list{
            overflow:hidden;
            margin-top:5px;
        }
        .bank-list li{
            float:left;
            width:153px;
            margin-bottom:5px;
        }

        #main{
            width:750px;
            margin:0 auto;
            font-size:14px;
            font-family:'宋体';
        }
        #logo{
            background-color: transparent;
            background-image: url("../img/new-btn-fixed.png");
            border: medium none;
            background-position:0 0;
            width:166px;
            height:35px;
            float:left;
        }
        .red-star{
            color:#f00;
            width:10px;
            display:inline-block;
        }
        .null-star{
            color:#fff;
        }
        .content{
            margin-top:5px;
        }

        .content dt{
            width:160px;
            display:inline-block;
            text-align:right;
            float:left;

        }
        .content dd{
            margin-left:100px;
            margin-bottom:5px;
        }
        #foot{
            margin-top:10px;
        }
        .foot-ul li {
            text-align:center;
        }
        .note-help {
            color: #999999;
            font-size: 12px;
            line-height: 130%;
            padding-left: 3px;
        }

        .cashier-nav {
            font-size: 14px;
            margin: 15px 0 10px;
            text-align: left;
            height:30px;
            border-bottom:solid 2px #CFD2D7;
        }
        .cashier-nav ol li {
            float: left;
        }
        .cashier-nav li.current {
            color: #AB4400;
            font-weight: bold;
        }
        .cashier-nav li.last {
            clear:right;
        }
        .alipay_link {
            text-align:right;
        }
        .alipay_link a:link{
            text-decoration:none;
            color:#8D8D8D;
        }
        .alipay_link a:visited{
            text-decoration:none;
            color:#8D8D8D;
        }
    </style>
</head>
<body text=#000000 bgColor="#ffffff" leftMargin=0 topMargin=4>
<div id="main">
    <div id="head">
        <dl class="alipay_link">
            <a target="_blank" href="http://www.alipay.com/"><span>支付宝首页</span></a>|
            <a target="_blank" href="https://b.alipay.com/home.htm"><span>商家服务</span></a>|
            <a target="_blank" href="http://help.alipay.com/support/index_sh.htm"><span>帮助中心</span></a>
        </dl>
        <span class="title">支付宝 当面付2.0 条码支付接口</span>
    </div>
    <div class="cashier-nav">
        <ol>
            <li class="current">1、确认信息 →</li>
            <li>2、点击确认 →</li>
            <li class="last">3、确认完成</li>
        </ol>
    </div>
    <form name=alipayment action="" method=post target="_blank">
        <div id="body" style="clear:left">
            <dl class="content">
                <dt>商户订单号：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="out_trade_no" />
						<span>商户网站订单系统中唯一订单号，必填
</span>
                </dd>
                <dt>订单名称：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="subject" />
						<span>必填
</span>
                </dd>

                <dt>付款金额：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="total_amount" />
						<span>必填
</span>
                </dd>

                <dt>付款条码：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="auth_code" />
						<span>必填
</span>
                </dd>


                <dt></dt>
                <dd>
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                </dd>
            </dl>
        </div>
    </form>
    <div id="foot">
        <ul class="foot-ul">
            <li><font class="note-help">如果您点击“确认”按钮，即表示您同意该次的执行操作。 </font></li>
            <li>
                支付宝版权所有 2011-2015 ALIPAY.COM
            </li>
        </ul>
    </div>
</div>
</body>
</html>

