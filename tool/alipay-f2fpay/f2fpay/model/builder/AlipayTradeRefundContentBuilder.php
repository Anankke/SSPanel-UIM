<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/19
 * Time: 下午2:09
 */
require_once 'ContentBuilder.php';

class AlipayTradeRefundContentBuilder extends ContentBuilder
{
    // 支付宝交易号，当面付支付成功后支付宝会返回给商户系统。通过此支付宝交易号进行交易退款
    private $tradeNo;

    // (推荐) 外部订单号，可通过外部订单号申请退款，推荐使用
    private $outTradeNo;

    // 退款金额，该金额必须小于等于订单的支付金额，此处单位为元，精确到小数点后2位
    private $refundAmount;

    // (可选，需要支持重复退货时必填) 商户退款请求号，相同支付宝交易号下的不同退款请求号对应同一笔交易的不同退款申请，
    // 对于相同支付宝交易号下多笔相同商户退款请求号的退款交易，支付宝只会进行一次退款
    private $outRequestNo;

    // (必填) 退款原因，可以说明用户退款原因，方便为商家后台提供统计
    private $refundReason;

    // (必填) 商户门店编号，退款情况下可以为商家后台提供退款权限判定和统计等作用，详询支付宝技术支持
    private $storeId;

    // 商户操作员编号，添加此参数可以为商户操作员做销售统
    private $operatorId;

    // 商户机具终端编号，当以机具方式接入支付宝时必传，详询支付宝技术支持
    private $terminalId;

    private $bizContentarr = array();

    private $bizContent = NULL;

   /* public function __construct()
    {
        $this->response = $response;
    }

    public function AlipayTradeRefundContentBuilder()
    {
        $this->__construct();
    }*/

    public function getBizContent()
    {
        if(!empty($this->bizContentarr)){
            $this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
        }
        
        return $this->bizContent;
    }

    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
        $this->bizContentarr['trade_no'] = $tradeNo;
    }

    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizContentarr['out_trade_no'] = $outTradeNo;
    }

    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
        $this->bizContentarr['operator_id'] = $operatorId;
    }

    public function getOperatorId()
    {
        return $this->operatorId;
    }

    public function setOutRequestNo($outRequestNo)
    {
        $this->outRequestNo = $outRequestNo;
        $this->bizContentarr['out_request_no'] = $outRequestNo;
    }

    public function getOutRequestNo()
    {
        return $this->outRequestNo;
    }

    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;
        $this->bizContentarr['refund_amount'] = $refundAmount;
    }

    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;
        $this->bizContentarr['refund_reason'] = $refundReason;
    }

    public function getRefundReason()
    {
        return $this->refundReason;
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->bizContentarr['store_id'] = $storeId;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
        $this->bizContentarr['terminal_id'] =$terminalId;
    }

    public function getTerminalId()
    {
        return $this->terminalId;
    }
}

?>