<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/18
 * Time: 下午2:09
 */
require_once 'GoodsDetail.php';
require_once 'ExtendParams.php';
require_once 'RoyaltyDetailInfo.php';
require_once 'ContentBuilder.php';

class AlipayTradePayContentBuilder extends ContentBuilder
{
    //支付场景码,此处为条码支付bar_code
    private $scene;

    //支付授权码,用户支付宝钱包app点击"付款",在条码下对应的一串数字
    private $authCode;

    // 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
    // 需保证商户系统端不能重复，建议通过数据库sequence生成，
    private $outTradeNo;

    // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
    // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
    private $sellerId;

    // 订单总金额，整形，此处单位为元，精确到小数点后2位，不能超过1亿元
    // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
    private $totalAmount;

    // 订单可打折金额，此处单位为元，精确到小数点后2位
    // 可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
    // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
    private $discountableAmount;

    // 订单不可打折金额，此处单位为元，精确到小数点后2位，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
    // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
    private $undiscountableAmount;

    // 订单标题，粗略描述用户的支付目的。如“XX品牌XXX门店消费”
    private $subject;

    // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
    private $body;

    // 商品明细列表，需填写购买商品详细信息，
    private $goodsDetailList = array();

    // 商户操作员编号，添加此参数可以为商户操作员做销售统
    private $operatorId;

    // 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
    private $storeId;

    // 支付宝商家平台中配置的商户门店号，详询支付宝技术支持
    private $alipayStoreId;

    // 商户机具终端编号，当以机具方式接入支付宝时必传，详询支付宝技术支持
    private $terminalId;

    // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，详情请咨询支付宝技术支持
    private $extendParams = array();

    // (推荐使用，相对时间) 支付超时时间，5m 5分钟
    private $timeExpress;

    private $bizContent = NULL;

    private $bizParas = array();


    public function __construct()
    {
        $this->bizParas['scene'] = "bar_code";
    }

    public function AlipayTradePayContentBuilder()
    {
        $this->__construct();
    }

    public function getBizContent()
    {
        /*$this->bizContent = "{";
        foreach ($this->bizParas as $k=>$v){
            $this->bizContent.= "\"".$k."\":\"".$v."\",";
        }
        $this->bizContent = substr($this->bizContent,0,-1);
        $this->bizContent.= "}";*/
        if(!empty($this->bizParas)){
            $this->bizContent = json_encode($this->bizParas,JSON_UNESCAPED_UNICODE);
        }

        return $this->bizContent;
    }
    
    public function getAuthCode()
    {
        return $this->authCode;
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
        $this->bizParas['auth_code'] = $authCode;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizParas['out_trade_no'] = $outTradeNo;
    }
    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
        $this->bizParas['seller_id'] = $sellerId;
    }

    public function getSellerId()
    {
        return $this->sellerId;
    }

    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
        $this->bizParas['total_amount'] = $totalAmount;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setDiscountableAmount($discountableAmount)
    {
        $this->discountableAmount = $discountableAmount;
        $this->bizParas['discountable_amount'] = $discountableAmount;
    }

    public function getDiscountableAmount()
    {
        return $this->discountableAmount;
    }

    public function setUndiscountableAmount($undiscountableAmount)
    {
        $this->undiscountableAmount = $undiscountableAmount;
        $this->bizParas['undiscountable_amount'] = $undiscountableAmount;
    }

    public function getUndiscountableAmount()
    {
        return $this->undiscountableAmount;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        $this->bizParas['subject'] = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setBody($body)
    {
        $this->body = $body;
        $this->bizParas['body'] = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
        $this->bizParas['operator_id'] = $operatorId;
    }

    public function getOperatorId()
    {
        return $this->operatorId;
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->bizParas['store_id'] = $storeId;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
        $this->bizParas['terminal_id'] = $terminalId;
    }

    public function getTerminalId()
    {
        return $this->terminalId;
    }

    public function setTimeExpress($timeExpress)
    {
        $this->timeExpress = $timeExpress;
        $this->bizParas['timeout_express'] = $timeExpress;
    }

    public function getTimeExpress()
    {
        return $this->timeExpress;
    }

    public function getAlipayStoreId()
    {
        return $this->alipayStoreId;
    }


    public function setAlipayStoreId($alipayStoreId)
    {
        $this->alipayStoreId = $alipayStoreId;
        $this->bizParas['alipay_store_id'] = $alipayStoreId;
    }

    public function getExtendParams()
    {
        return $this->extendParams;
    }

    public function setExtendParams($extendParams)
    {
        $this->extendParams = $extendParams;
        $this->bizParas['extend_params'] = $extendParams;
    }

    public function getGoodsDetailList()
    {
        return $this->goodsDetailList;
    }

    public function setGoodsDetailList($goodsDetailList)
    {
        $this->goodsDetailList = $goodsDetailList;
        $this->bizParas['goods_detail'] = $goodsDetailList;
    }

}

?>