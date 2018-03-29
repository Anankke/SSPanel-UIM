<?php
/**
 * ALIPAY API: alipay.asset.account.bind request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:17:01
 */
class AlipayAssetAccountBindRequest
{
	/** 
	 * 绑定场景，目前仅支持如下：
wechat：微信公众平台；
transport：物流转运平台；
appOneBind：一对一app绑定； 
注意：必须是这些值，区分大小写。
	 **/
	private $bindScene;
	
	/** 
	 * 使用该app提供用户信息的商户，可以和app相同。
	 **/
	private $providerId;
	
	/** 
	 * 用户在商户网站的会员标识。商户需确保其唯一性，不可变更。
	 **/
	private $providerUserId;
	
	/** 
	 * 用户在商户网站的会员名（登录号或昵称）。
	 **/
	private $providerUserName;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setBindScene($bindScene)
	{
		$this->bindScene = $bindScene;
		$this->apiParas["bind_scene"] = $bindScene;
	}

	public function getBindScene()
	{
		return $this->bindScene;
	}

	public function setProviderId($providerId)
	{
		$this->providerId = $providerId;
		$this->apiParas["provider_id"] = $providerId;
	}

	public function getProviderId()
	{
		return $this->providerId;
	}

	public function setProviderUserId($providerUserId)
	{
		$this->providerUserId = $providerUserId;
		$this->apiParas["provider_user_id"] = $providerUserId;
	}

	public function getProviderUserId()
	{
		return $this->providerUserId;
	}

	public function setProviderUserName($providerUserName)
	{
		$this->providerUserName = $providerUserName;
		$this->apiParas["provider_user_name"] = $providerUserName;
	}

	public function getProviderUserName()
	{
		return $this->providerUserName;
	}

	public function getApiMethodName()
	{
		return "alipay.asset.account.bind";
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
