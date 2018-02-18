<?php
/**
 * ALIPAY API: alipay.pass.tpl.add request
 *
 * @author auto create
 * @since 1.0, 2014-07-23 17:28:53
 */
class AlipayPassTplAddRequest
{
	/** 
	 * 支付宝pass模版内容【JSON格式】
具体格式可参考https://alipass.alipay.com中文档中心-格式说明
	 **/
	private $tplContent;
	
	/** 
	 * 模版外部唯一标识：商户用于控制模版的唯一性。
	 **/
	private $uniqueId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setTplContent($tplContent)
	{
		$this->tplContent = $tplContent;
		$this->apiParas["tpl_content"] = $tplContent;
	}

	public function getTplContent()
	{
		return $this->tplContent;
	}

	public function setUniqueId($uniqueId)
	{
		$this->uniqueId = $uniqueId;
		$this->apiParas["unique_id"] = $uniqueId;
	}

	public function getUniqueId()
	{
		return $this->uniqueId;
	}

	public function getApiMethodName()
	{
		return "alipay.pass.tpl.add";
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
