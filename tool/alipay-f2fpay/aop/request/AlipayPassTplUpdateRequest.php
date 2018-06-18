<?php
/**
 * ALIPAY API: alipay.pass.tpl.update request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:03
 */
class AlipayPassTplUpdateRequest
{
	/** 
	 * 模版内容
	 **/
	private $tplContent;
	
	/** 
	 * 模版ID
	 **/
	private $tplId;

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

	public function setTplId($tplId)
	{
		$this->tplId = $tplId;
		$this->apiParas["tpl_id"] = $tplId;
	}

	public function getTplId()
	{
		return $this->tplId;
	}

	public function getApiMethodName()
	{
		return "alipay.pass.tpl.update";
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
