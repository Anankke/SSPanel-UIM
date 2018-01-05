<?php
/**
 * ALIPAY API: alipay.ecard.edu.public.bind request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:41
 */
class AlipayEcardEduPublicBindRequest
{
	/** 
	 * 机构编码
	 **/
	private $agentCode;
	
	/** 
	 * 公众账号协议Id
	 **/
	private $agreementId;
	
	/** 
	 * 支付宝userId
	 **/
	private $alipayUserId;
	
	/** 
	 * 一卡通姓名
	 **/
	private $cardName;
	
	/** 
	 * 一卡通卡号
	 **/
	private $cardNo;
	
	/** 
	 * 公众账号id
	 **/
	private $publicId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setAgentCode($agentCode)
	{
		$this->agentCode = $agentCode;
		$this->apiParas["agent_code"] = $agentCode;
	}

	public function getAgentCode()
	{
		return $this->agentCode;
	}

	public function setAgreementId($agreementId)
	{
		$this->agreementId = $agreementId;
		$this->apiParas["agreement_id"] = $agreementId;
	}

	public function getAgreementId()
	{
		return $this->agreementId;
	}

	public function setAlipayUserId($alipayUserId)
	{
		$this->alipayUserId = $alipayUserId;
		$this->apiParas["alipay_user_id"] = $alipayUserId;
	}

	public function getAlipayUserId()
	{
		return $this->alipayUserId;
	}

	public function setCardName($cardName)
	{
		$this->cardName = $cardName;
		$this->apiParas["card_name"] = $cardName;
	}

	public function getCardName()
	{
		return $this->cardName;
	}

	public function setCardNo($cardNo)
	{
		$this->cardNo = $cardNo;
		$this->apiParas["card_no"] = $cardNo;
	}

	public function getCardNo()
	{
		return $this->cardNo;
	}

	public function setPublicId($publicId)
	{
		$this->publicId = $publicId;
		$this->apiParas["public_id"] = $publicId;
	}

	public function getPublicId()
	{
		return $this->publicId;
	}

	public function getApiMethodName()
	{
		return "alipay.ecard.edu.public.bind";
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
