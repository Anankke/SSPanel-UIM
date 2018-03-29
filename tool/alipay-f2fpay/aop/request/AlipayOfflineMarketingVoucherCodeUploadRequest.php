<?php
/**
 * ALIPAY API: alipay.offline.marketing.voucher.code.upload request
 *
 * @author auto create
 * @since 1.0, 2016-04-25 11:38:38
 */
class AlipayOfflineMarketingVoucherCodeUploadRequest
{
	/** 
	 * 约定的扩展参数
	 **/
	private $extendParams;
	
	/** 
	 * 文件编码
	 **/
	private $fileCharset;
	
	/** 
	 * 文件二进制内容
	 **/
	private $fileContent;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setExtendParams($extendParams)
	{
		$this->extendParams = $extendParams;
		$this->apiParas["extend_params"] = $extendParams;
	}

	public function getExtendParams()
	{
		return $this->extendParams;
	}

	public function setFileCharset($fileCharset)
	{
		$this->fileCharset = $fileCharset;
		$this->apiParas["file_charset"] = $fileCharset;
	}

	public function getFileCharset()
	{
		return $this->fileCharset;
	}

	public function setFileContent($fileContent)
	{
		$this->fileContent = $fileContent;
		$this->apiParas["file_content"] = $fileContent;
	}

	public function getFileContent()
	{
		return $this->fileContent;
	}

	public function getApiMethodName()
	{
		return "alipay.offline.marketing.voucher.code.upload";
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
