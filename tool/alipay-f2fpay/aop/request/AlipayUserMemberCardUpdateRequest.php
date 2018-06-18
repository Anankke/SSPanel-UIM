<?php
/**
 * ALIPAY API: alipay.user.member.card.update request
 *
 * @author auto create
 * @since 1.0, 2015-09-23 14:14:10
 */
class AlipayUserMemberCardUpdateRequest
{
	/** 
	 * 商户会员卡余额
	 **/
	private $balance;
	
	/** 
	 * 会员卡卡号
	 **/
	private $bizCardNo;
	
	/** 
	 * 发卡商户信息，json格式。
目前仅支持如下key：
&#61548;	merchantUniId：商户唯一标识
&#61548;	merchantUniIdType：支持以下3种取值。
LOGON_ID：商户登录ID，邮箱或者手机号格式；
UID：商户的支付宝用户号，以2088开头的16位纯数字组成；
BINDING_MOBILE：商户支付宝账号绑定的手机号。
   注意：
本参数主要用于发卡平台接入场景，request_from为PLATFORM时，不能为空。
	 **/
	private $cardMerchantInfo;
	
	/** 
	 * 扩展参数，json格式。
用于商户的特定业务信息的传递，只有商户与支付宝约定了传递此参数且约定了参数含义，此参数才有效。
	 **/
	private $extInfo;
	
	/** 
	 * 商户会员卡号。 
比如淘宝会员卡号、商户实体会员卡号、商户自有CRM虚拟卡号等
	 **/
	private $externalCardNo;
	
	/** 
	 * ALIPAY：支付宝
PARTNER：商户
PLATFORM：平台商
	 **/
	private $issuerType;
	
	/** 
	 * 商户会员卡会员等级
	 **/
	private $level;
	
	/** 
	 * 时间戳参数-orrur_time（精确至毫秒），标识业务发生的时间
orrur_time 必须为long类型整数,时间维度目前未限制
	 **/
	private $orrurTime;
	
	/** 
	 * 商户会员卡积分
	 **/
	private $point;
	
	/** 
	 * 请求来源。
PLATFORM：发卡平台商
PARTNER：直联商户
	 **/
	private $requestFrom;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setBalance($balance)
	{
		$this->balance = $balance;
		$this->apiParas["balance"] = $balance;
	}

	public function getBalance()
	{
		return $this->balance;
	}

	public function setBizCardNo($bizCardNo)
	{
		$this->bizCardNo = $bizCardNo;
		$this->apiParas["biz_card_no"] = $bizCardNo;
	}

	public function getBizCardNo()
	{
		return $this->bizCardNo;
	}

	public function setCardMerchantInfo($cardMerchantInfo)
	{
		$this->cardMerchantInfo = $cardMerchantInfo;
		$this->apiParas["card_merchant_info"] = $cardMerchantInfo;
	}

	public function getCardMerchantInfo()
	{
		return $this->cardMerchantInfo;
	}

	public function setExtInfo($extInfo)
	{
		$this->extInfo = $extInfo;
		$this->apiParas["ext_info"] = $extInfo;
	}

	public function getExtInfo()
	{
		return $this->extInfo;
	}

	public function setExternalCardNo($externalCardNo)
	{
		$this->externalCardNo = $externalCardNo;
		$this->apiParas["external_card_no"] = $externalCardNo;
	}

	public function getExternalCardNo()
	{
		return $this->externalCardNo;
	}

	public function setIssuerType($issuerType)
	{
		$this->issuerType = $issuerType;
		$this->apiParas["issuer_type"] = $issuerType;
	}

	public function getIssuerType()
	{
		return $this->issuerType;
	}

	public function setLevel($level)
	{
		$this->level = $level;
		$this->apiParas["level"] = $level;
	}

	public function getLevel()
	{
		return $this->level;
	}

	public function setOrrurTime($orrurTime)
	{
		$this->orrurTime = $orrurTime;
		$this->apiParas["orrur_time"] = $orrurTime;
	}

	public function getOrrurTime()
	{
		return $this->orrurTime;
	}

	public function setPoint($point)
	{
		$this->point = $point;
		$this->apiParas["point"] = $point;
	}

	public function getPoint()
	{
		return $this->point;
	}

	public function setRequestFrom($requestFrom)
	{
		$this->requestFrom = $requestFrom;
		$this->apiParas["request_from"] = $requestFrom;
	}

	public function getRequestFrom()
	{
		return $this->requestFrom;
	}

	public function getApiMethodName()
	{
		return "alipay.user.member.card.update";
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
