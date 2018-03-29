<?php
/**
 * ALIPAY API: alipay.point.order.get request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:00
 */
class AlipayPointOrderGetRequest
{
	/** 
	 * isv提供的发放号订单号，由数字和组成，最大长度为32为，需要保证每笔发放的唯一性，支付宝会对该参数做唯一性控制。如果使用同样的订单号，支付宝将返回订单号已经存在的错误
	 **/
	private $merchantOrderNo;
	
	/** 
	 * 用户标识符，用于指定集分宝发放的用户，和user_symbol_type一起使用，确定一个唯一的支付宝用户
	 **/
	private $userSymbol;
	
	/** 
	 * 用户标识符类型，现在支持ALIPAY_USER_ID:表示支付宝用户ID,ALIPAY_LOGON_ID:表示支付宝登陆号
	 **/
	private $userSymbolType;

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

	public function setUserSymbol($userSymbol)
	{
		$this->userSymbol = $userSymbol;
		$this->apiParas["user_symbol"] = $userSymbol;
	}

	public function getUserSymbol()
	{
		return $this->userSymbol;
	}

	public function setUserSymbolType($userSymbolType)
	{
		$this->userSymbolType = $userSymbolType;
		$this->apiParas["user_symbol_type"] = $userSymbolType;
	}

	public function getUserSymbolType()
	{
		return $this->userSymbolType;
	}

	public function getApiMethodName()
	{
		return "alipay.point.order.get";
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
