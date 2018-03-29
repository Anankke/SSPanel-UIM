<?php
/**
 * ALIPAY API: alipay.acquire.refund request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:17:03
 */
class AlipayAcquireRefundRequest
{
	/** 
	 * 卖家的操作员ID。
	 **/
	private $operatorId;
	
	/** 
	 * 操作员的类型：
0：支付宝操作员
1：商户的操作员
如果传入其它值或者为空，则默认设置为1。
	 **/
	private $operatorType;
	
	/** 
	 * 商户退款请求单号，用以标识本次交易的退款请求。
如果不传入本参数，则以out_trade_no填充本参数的值。同时，认为本次请求为全额退款，要求退款金额和交易支付金额一致。
	 **/
	private $outRequestNo;
	
	/** 
	 * 商户网站唯一订单号
	 **/
	private $outTradeNo;
	
	/** 
	 * 业务关联ID集合，用于放置商户的退款单号、退款流水号等信息，json格式
	 **/
	private $refIds;
	
	/** 
	 * 退款金额；退款金额不能大于订单金额，全额退款必须与订单金额一致。
	 **/
	private $refundAmount;
	
	/** 
	 * 退款原因说明。
	 **/
	private $refundReason;
	
	/** 
	 * 该交易在支付宝系统中的交易流水号。
最短16位，最长64位。
如果同时传了out_trade_no和trade_no，则以trade_no为准
	 **/
	private $tradeNo;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setOperatorId($operatorId)
	{
		$this->operatorId = $operatorId;
		$this->apiParas["operator_id"] = $operatorId;
	}

	public function getOperatorId()
	{
		return $this->operatorId;
	}

	public function setOperatorType($operatorType)
	{
		$this->operatorType = $operatorType;
		$this->apiParas["operator_type"] = $operatorType;
	}

	public function getOperatorType()
	{
		return $this->operatorType;
	}

	public function setOutRequestNo($outRequestNo)
	{
		$this->outRequestNo = $outRequestNo;
		$this->apiParas["out_request_no"] = $outRequestNo;
	}

	public function getOutRequestNo()
	{
		return $this->outRequestNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->apiParas["out_trade_no"] = $outTradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setRefIds($refIds)
	{
		$this->refIds = $refIds;
		$this->apiParas["ref_ids"] = $refIds;
	}

	public function getRefIds()
	{
		return $this->refIds;
	}

	public function setRefundAmount($refundAmount)
	{
		$this->refundAmount = $refundAmount;
		$this->apiParas["refund_amount"] = $refundAmount;
	}

	public function getRefundAmount()
	{
		return $this->refundAmount;
	}

	public function setRefundReason($refundReason)
	{
		$this->refundReason = $refundReason;
		$this->apiParas["refund_reason"] = $refundReason;
	}

	public function getRefundReason()
	{
		return $this->refundReason;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->apiParas["trade_no"] = $tradeNo;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function getApiMethodName()
	{
		return "alipay.acquire.refund";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

     $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
    return $this->needEncrypt;
  }

}
