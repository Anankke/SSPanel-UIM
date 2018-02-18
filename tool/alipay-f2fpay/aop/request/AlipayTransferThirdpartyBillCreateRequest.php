<?php
/**
 * ALIPAY API: alipay.transfer.thirdparty.bill.create request
 *
 * @author auto create
 * @since 1.0, 2014-06-25 17:00:56
 */
class AlipayTransferThirdpartyBillCreateRequest
{
	/** 
	 * 收款金额，单位：分
	 **/
	private $amount;
	
	/** 
	 * 收款币种，默认为156（人民币）目前只允许转账人民币
	 **/
	private $currency;
	
	/** 
	 * 扩展参数
	 **/
	private $extParam;
	
	/** 
	 * 转账备注
	 **/
	private $memo;
	
	/** 
	 * 合作方的支付宝帐号UID
	 **/
	private $partnerId;
	
	/** 
	 * 外部系统收款方UID，付款人和收款人不能是同一个帐户
	 **/
	private $payeeAccount;
	
	/** 
	 * （同payer_type所列举的）
目前限制payer_type和payee_type必须一致
	 **/
	private $payeeType;
	
	/** 
	 * 外部系统付款方的UID
	 **/
	private $payerAccount;
	
	/** 
	 * 1-支付宝帐户
2-淘宝帐户
10001-新浪微博帐户
10002-阿里云帐户
（1、2目前对外不可见、不可用）
	 **/
	private $payerType;
	
	/** 
	 * 发起支付交易来源方定义的交易ID，用于将支付回执通知给来源方。不同来源方给出的ID可以重复，同一个来源方给出的ID唯一性由来源方保证。
	 **/
	private $paymentId;
	
	/** 
	 * 支付来源
10001-新浪微博
10002-阿里云
	 **/
	private $paymentSource;
	
	/** 
	 * 支付款项的标题
	 **/
	private $title;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setAmount($amount)
	{
		$this->amount = $amount;
		$this->apiParas["amount"] = $amount;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
		$this->apiParas["currency"] = $currency;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function setExtParam($extParam)
	{
		$this->extParam = $extParam;
		$this->apiParas["ext_param"] = $extParam;
	}

	public function getExtParam()
	{
		return $this->extParam;
	}

	public function setMemo($memo)
	{
		$this->memo = $memo;
		$this->apiParas["memo"] = $memo;
	}

	public function getMemo()
	{
		return $this->memo;
	}

	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
		$this->apiParas["partner_id"] = $partnerId;
	}

	public function getPartnerId()
	{
		return $this->partnerId;
	}

	public function setPayeeAccount($payeeAccount)
	{
		$this->payeeAccount = $payeeAccount;
		$this->apiParas["payee_account"] = $payeeAccount;
	}

	public function getPayeeAccount()
	{
		return $this->payeeAccount;
	}

	public function setPayeeType($payeeType)
	{
		$this->payeeType = $payeeType;
		$this->apiParas["payee_type"] = $payeeType;
	}

	public function getPayeeType()
	{
		return $this->payeeType;
	}

	public function setPayerAccount($payerAccount)
	{
		$this->payerAccount = $payerAccount;
		$this->apiParas["payer_account"] = $payerAccount;
	}

	public function getPayerAccount()
	{
		return $this->payerAccount;
	}

	public function setPayerType($payerType)
	{
		$this->payerType = $payerType;
		$this->apiParas["payer_type"] = $payerType;
	}

	public function getPayerType()
	{
		return $this->payerType;
	}

	public function setPaymentId($paymentId)
	{
		$this->paymentId = $paymentId;
		$this->apiParas["payment_id"] = $paymentId;
	}

	public function getPaymentId()
	{
		return $this->paymentId;
	}

	public function setPaymentSource($paymentSource)
	{
		$this->paymentSource = $paymentSource;
		$this->apiParas["payment_source"] = $paymentSource;
	}

	public function getPaymentSource()
	{
		return $this->paymentSource;
	}

	public function setTitle($title)
	{
		$this->title = $title;
		$this->apiParas["title"] = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getApiMethodName()
	{
		return "alipay.transfer.thirdparty.bill.create";
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
