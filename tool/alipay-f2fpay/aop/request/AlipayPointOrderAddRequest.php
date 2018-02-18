<?php
/**
 * ALIPAY API: alipay.point.order.add request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:00
 */
class AlipayPointOrderAddRequest
{
	/** 
	 * 向用户展示集分宝发放备注
	 **/
	private $memo;
	
	/** 
	 * isv提供的发放订单号，由数字和字母组成，最大长度为32位，需要保证每笔订单发放的唯一性，支付宝对该参数做唯一性校验。如果订单号已存在，支付宝将返回订单号已经存在的错误
	 **/
	private $merchantOrderNo;
	
	/** 
	 * 发放集分宝时间
	 **/
	private $orderTime;
	
	/** 
	 * 发放集分宝的数量
	 **/
	private $pointCount;
	
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

	
	public function setMemo($memo)
	{
		$this->memo = $memo;
		$this->apiParas["memo"] = $memo;
	}

	public function getMemo()
	{
		return $this->memo;
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

	public function setOrderTime($orderTime)
	{
		$this->orderTime = $orderTime;
		$this->apiParas["order_time"] = $orderTime;
	}

	public function getOrderTime()
	{
		return $this->orderTime;
	}

	public function setPointCount($pointCount)
	{
		$this->pointCount = $pointCount;
		$this->apiParas["point_count"] = $pointCount;
	}

	public function getPointCount()
	{
		return $this->pointCount;
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
		return "alipay.point.order.add";
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
