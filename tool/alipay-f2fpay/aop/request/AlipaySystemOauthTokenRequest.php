<?php
/**
 * ALIPAY API: alipay.system.oauth.token request
 *
 * @author auto create
 * @since 1.0, 2016-04-29 01:01:26
 */
class AlipaySystemOauthTokenRequest
{
	/** 
	 * 授权码，用户对应用授权后得到。
	 **/
	private $code;
	
	/** 
	 * 值为authorization_code时，代表用code换取；值为refresh_token时，代表用refresh_token换取
	 **/
	private $grantType;
	
	/** 
	 * 刷新令牌，上次换取访问令牌时得到。见出参的refresh_token字段
	 **/
	private $refreshToken;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setCode($code)
	{
		$this->code = $code;
		$this->apiParas["code"] = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setGrantType($grantType)
	{
		$this->grantType = $grantType;
		$this->apiParas["grant_type"] = $grantType;
	}

	public function getGrantType()
	{
		return $this->grantType;
	}

	public function setRefreshToken($refreshToken)
	{
		$this->refreshToken = $refreshToken;
		$this->apiParas["refresh_token"] = $refreshToken;
	}

	public function getRefreshToken()
	{
		return $this->refreshToken;
	}

	public function getApiMethodName()
	{
		return "alipay.system.oauth.token";
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
