<?php
/**
 * ALIPAY API: alipay.ebpp.bill.get request
 *
 * @author auto create
 * @since 1.0, 2016-04-18 11:59:10
 */
class AlipayEbppBillGetRequest
{
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
		return "alipay.ebpp.bill.get";
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
