<?php
/**
 * ALIPAY API: alipay.ebpp.bill.pay request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:55
 */
class AlipayEbppBillPayRequest
{
	/** 
	 * 支付宝的业务订单号，具有唯一性。
	 **/
	private $alipayOrderNo;
	
	/** 
	 * openapi的spanner上增加规则转发到pcimapi集群上
	 **/
	private $dispatchClusterTarget;
	
	/** 
	 * 扩展字段
	 **/
	private $extend;
	
	/** 
	 * 输出机构的业务流水号，需要保证唯一性。
	 **/
	private $merchantOrderNo;
	
	/** 
	 * 支付宝订单类型。公共事业缴纳JF,信用卡还款HK
	 **/
	private $orderType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setAlipayOrderNo($alipayOrderNo)
	{
		$this->alipayOrderNo = $alipayOrderNo;
		$this->apiParas["alipay_order_no"] = $alipayOrderNo;
	}

	public function getAlipayOrderNo()
	{
		return $this->alipayOrderNo;
	}

	public function setDispatchClusterTarget($dispatchClusterTarget)
	{
		$this->dispatchClusterTarget = $dispatchClusterTarget;
		$this->apiParas["dispatch_cluster_target"] = $dispatchClusterTarget;
	}

	public function getDispatchClusterTarget()
	{
		return $this->dispatchClusterTarget;
	}

	public function setExtend($extend)
	{
		$this->extend = $extend;
		$this->apiParas["extend"] = $extend;
	}

	public function getExtend()
	{
		return $this->extend;
	}

	public function setMerchantOrderNo($merchantOrderNo)
	{
		$this->merchantOrderNo = $merchantOrderNo;
		$this->apiParas["merchant_order_no"] = $merchantOrderNo;
	}

	public function getMerchantOrderNo()
	{
		return $this->merchantOrderNo;
	}

	public function setOrderType($orderType)
	{
		$this->orderType = $orderType;
		$this->apiParas["order_type"] = $orderType;
	}

	public function getOrderType()
	{
		return $this->orderType;
	}

	public function getApiMethodName()
	{
		return "alipay.ebpp.bill.pay";
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
