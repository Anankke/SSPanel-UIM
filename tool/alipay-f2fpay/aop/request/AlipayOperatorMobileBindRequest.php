<?php
/**
 * ALIPAY API: alipay.operator.mobile.bind request
 *
 * @author auto create
 * @since 1.0, 2014-10-22 15:46:40
 */
class AlipayOperatorMobileBindRequest
{
	/** 
	 * 标识该运营商是否需要验证用户的手机号绑定过快捷卡
1：需要
0：不需要
	 **/
	private $checkSigncard;
	
	/** 
	 * 支付宝处理完请求后，如验证失败，当前页面自动跳转到商户网站里指定页面的http路径。
	 **/
	private $fReturnUrl;
	
	/** 
	 * 标识该运营商是否提供了查询手机归属的spi接口。
1：提供了
0：没提供
	 **/
	private $hasSpi;
	
	/** 
	 * 标识该运营商名称
	 **/
	private $operatorName;
	
	/** 
	 * 标识该运营商所在省份
	 **/
	private $provinceName;
	
	/** 
	 * 支付宝处理完请求后，如验证成功，当前页面自动跳转到商户网站里指定页面的http路径。
	 **/
	private $sReturnUrl;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setCheckSigncard($checkSigncard)
	{
		$this->checkSigncard = $checkSigncard;
		$this->apiParas["check_signcard"] = $checkSigncard;
	}

	public function getCheckSigncard()
	{
		return $this->checkSigncard;
	}

	public function setfReturnUrl($fReturnUrl)
	{
		$this->fReturnUrl = $fReturnUrl;
		$this->apiParas["f_return_url"] = $fReturnUrl;
	}

	public function getfReturnUrl()
	{
		return $this->fReturnUrl;
	}

	public function setHasSpi($hasSpi)
	{
		$this->hasSpi = $hasSpi;
		$this->apiParas["has_spi"] = $hasSpi;
	}

	public function getHasSpi()
	{
		return $this->hasSpi;
	}

	public function setOperatorName($operatorName)
	{
		$this->operatorName = $operatorName;
		$this->apiParas["operator_name"] = $operatorName;
	}

	public function getOperatorName()
	{
		return $this->operatorName;
	}

	public function setProvinceName($provinceName)
	{
		$this->provinceName = $provinceName;
		$this->apiParas["province_name"] = $provinceName;
	}

	public function getProvinceName()
	{
		return $this->provinceName;
	}

	public function setsReturnUrl($sReturnUrl)
	{
		$this->sReturnUrl = $sReturnUrl;
		$this->apiParas["s_return_url"] = $sReturnUrl;
	}

	public function getsReturnUrl()
	{
		return $this->sReturnUrl;
	}

	public function getApiMethodName()
	{
		return "alipay.operator.mobile.bind";
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
