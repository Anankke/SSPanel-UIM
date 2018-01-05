<?php
/**
 * ALIPAY API: alipay.pass.code.add request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:12
 */
class AlipayPassCodeAddRequest
{
	/** 
	 * alipass文件Base64编码后的内容。
	 **/
	private $fileContent;
	
	/** 
	 * 识别信息
当 recognition_type=1时， recognition_info={“partner_id”:”2088102114633762”,“out_trade_no”:”1234567”}
当recognition_type=2时， recognition_info={“user_id”:”2088102114633761“ }
	 **/
	private $recognitionInfo;
	
	/** 
	 * 发放对象识别类型
1-	订单信息
2-	支付宝userId
	 **/
	private $recognitionType;
	
	/** 
	 * 该pass的核销方式,如果为空，则默认为["wave","qrcode"]
	 **/
	private $verifyType;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setFileContent($fileContent)
	{
		$this->fileContent = $fileContent;
		$this->apiParas["file_content"] = $fileContent;
	}

	public function getFileContent()
	{
		return $this->fileContent;
	}

	public function setRecognitionInfo($recognitionInfo)
	{
		$this->recognitionInfo = $recognitionInfo;
		$this->apiParas["recognition_info"] = $recognitionInfo;
	}

	public function getRecognitionInfo()
	{
		return $this->recognitionInfo;
	}

	public function setRecognitionType($recognitionType)
	{
		$this->recognitionType = $recognitionType;
		$this->apiParas["recognition_type"] = $recognitionType;
	}

	public function getRecognitionType()
	{
		return $this->recognitionType;
	}

	public function setVerifyType($verifyType)
	{
		$this->verifyType = $verifyType;
		$this->apiParas["verify_type"] = $verifyType;
	}

	public function getVerifyType()
	{
		return $this->verifyType;
	}

	public function getApiMethodName()
	{
		return "alipay.pass.code.add";
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
