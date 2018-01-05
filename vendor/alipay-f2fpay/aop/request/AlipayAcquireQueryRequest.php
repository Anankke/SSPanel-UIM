<?php
/**
 * ALIPAY API: alipay.acquire.query request
 *
 * @author auto create
 * @since 1.0, 2014-05-28 11:58:01
 */
class AlipayAcquireQueryRequest
{
	/** 
	 * 支付宝合作商户网站唯一订单号
	 **/
	private $outTradeNo;
	
	/** 
	 * 该交易在支付宝系统中的交易流水号。
最短16位，最长64位。
如果同时传了out_trade_no和trade_no，则以trade_no为准。
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

	
	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->apiParas["out_trade_no"] = $outTradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
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
		return "alipay.acquire.query";
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
