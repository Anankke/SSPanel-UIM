<?php
/**
 * ALIPAY API: alipay.mobile.public.info.modify request
 *
 * @author auto create
 * @since 1.0, 2016-01-14 20:28:01
 */
class AlipayMobilePublicInfoModifyRequest
{
	/** 
	 * 服务窗名称，2-20个字之间；不得含有违反法律法规和公序良俗的相关信息；不得侵害他人名誉权、知识产权、商业秘密等合法权利；不得以太过广泛的、或产品、行业词组来命名，如：女装、皮革批发；不得以实名认证的媒体资质账号创建服务窗，或媒体相关名称命名服务窗，如：XX电视台、XX杂志等
	 **/
	private $appName;
	
	/** 
	 * 授权运营书，企业商户若为被经营方授权，需上传加盖公章的扫描件，请使用照片上传接口上传图片获得image_url
	 **/
	private $authPic;
	
	/** 
	 * 营业执照地址，建议尺寸 320 x 320px，支持.jpg .jpeg .png 格式，小于3M
	 **/
	private $licenseUrl;
	
	/** 
	 * 服务窗头像地址，建议尺寸 320 x 320px，支持.jpg .jpeg .png 格式，小于3M
	 **/
	private $logoUrl;
	
	/** 
	 * 服务窗欢迎语，200字以内，首次使用服务窗必须
	 **/
	private $publicGreeting;
	
	/** 
	 * 第一张门店照片地址，建议尺寸 320 x 320px，支持.jpg .jpeg .png 格式，小于3M
	 **/
	private $shopPic1;
	
	/** 
	 * 第二张门店照片地址
	 **/
	private $shopPic2;
	
	/** 
	 * 第三张门店照片地址
	 **/
	private $shopPic3;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setAppName($appName)
	{
		$this->appName = $appName;
		$this->apiParas["app_name"] = $appName;
	}

	public function getAppName()
	{
		return $this->appName;
	}

	public function setAuthPic($authPic)
	{
		$this->authPic = $authPic;
		$this->apiParas["auth_pic"] = $authPic;
	}

	public function getAuthPic()
	{
		return $this->authPic;
	}

	public function setLicenseUrl($licenseUrl)
	{
		$this->licenseUrl = $licenseUrl;
		$this->apiParas["license_url"] = $licenseUrl;
	}

	public function getLicenseUrl()
	{
		return $this->licenseUrl;
	}

	public function setLogoUrl($logoUrl)
	{
		$this->logoUrl = $logoUrl;
		$this->apiParas["logo_url"] = $logoUrl;
	}

	public function getLogoUrl()
	{
		return $this->logoUrl;
	}

	public function setPublicGreeting($publicGreeting)
	{
		$this->publicGreeting = $publicGreeting;
		$this->apiParas["public_greeting"] = $publicGreeting;
	}

	public function getPublicGreeting()
	{
		return $this->publicGreeting;
	}

	public function setShopPic1($shopPic1)
	{
		$this->shopPic1 = $shopPic1;
		$this->apiParas["shop_pic1"] = $shopPic1;
	}

	public function getShopPic1()
	{
		return $this->shopPic1;
	}

	public function setShopPic2($shopPic2)
	{
		$this->shopPic2 = $shopPic2;
		$this->apiParas["shop_pic2"] = $shopPic2;
	}

	public function getShopPic2()
	{
		return $this->shopPic2;
	}

	public function setShopPic3($shopPic3)
	{
		$this->shopPic3 = $shopPic3;
		$this->apiParas["shop_pic3"] = $shopPic3;
	}

	public function getShopPic3()
	{
		return $this->shopPic3;
	}

	public function getApiMethodName()
	{
		return "alipay.mobile.public.info.modify";
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
