<?php
/**
 * ALIPAY API: alipay.acquire.createandpay request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:17:05
 */
class AlipayAcquireCreateandpayRequest
{
	/** 
	 * 证书签名
	 **/
	private $alipayCaRequest;
	
	/** 
	 * 对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
	 **/
	private $body;
	
	/** 
	 * 买家支付宝账号，可以为email或者手机号。
	 **/
	private $buyerEmail;
	
	/** 
	 * 买家支付宝账号对应的支付宝唯一用户号。
以2088开头的纯16位数字。
	 **/
	private $buyerId;
	
	/** 
	 * 描述多渠道收单的渠道明细信息，json格式，具体请参见“4.5 渠道明细说明”。
	 **/
	private $channelParameters;
	
	/** 
	 * 订单金额币种。
目前只支持传入156（人民币）。
如果为空，则默认设置为156。
	 **/
	private $currency;
	
	/** 
	 * 动态ID。
	 **/
	private $dynamicId;
	
	/** 
	 * 动态ID类型：
&#1048698;
soundwave：声波
&#1048698;
qrcode：二维码
&#1048698;
barcode：条码
&#1048698;
wave_code：声波，等同soundwave
&#1048698;
qr_code：二维码，等同qrcode
&#1048698;
bar_code：条码，等同barcode
建议取值wave_code、qr_code、bar_code。
	 **/
	private $dynamicIdType;
	
	/** 
	 * 用于商户的特定业务信息的传递，只有商户与支付宝约定了传递此参数且约定了参数含义，此参数才有效。
比如可传递声波支付场景下的门店ID等信息，以json格式传输，具体请参见“4.7 业务扩展参数说明”。
	 **/
	private $extendParams;
	
	/** 
	 * xml或json
	 **/
	private $formatType;
	
	/** 
	 * 描述商品明细信息，json格式，具体请参见“4.3 商品明细说明”。
	 **/
	private $goodsDetail;
	
	/** 
	 * 设置未付款交易的超时时间，一旦超时，该笔交易就会自动被关闭。
取值范围：1m～15d。
m-分钟，h-小时，d-天，1c-当天（无论交易何时创建，都在0点关闭）。
该参数数值不接受小数点，如1.5h，可转换为90m。
该功能需要联系支付宝配置关闭时间。
	 **/
	private $itBPay;
	
	/** 
	 * 描述预付卡相关的明细信息，json格式，具体请参见“4.8 预付卡明细参数说明”。
	 **/
	private $mcardParameters;
	
	/** 
	 * 卖家的操作员ID。
	 **/
	private $operatorId;
	
	/** 
	 * 操作员的类型：
&#1048698;
0：支付宝操作员
&#1048698;
1：商户的操作员
如果传入其它值或者为空，则默认设置为1。
	 **/
	private $operatorType;
	
	/** 
	 * 支付宝合作商户网站唯一订单号。
	 **/
	private $outTradeNo;
	
	/** 
	 * 订单中商品的单价。
如果请求时传入本参数，则必须满足total_fee=price×quantity的条件。
	 **/
	private $price;
	
	/** 
	 * 订单中商品的数量。
如果请求时传入本参数，则必须满足total_fee=price×quantity的条件。
	 **/
	private $quantity;
	
	/** 
	 * 业务关联ID集合，用于放置商户的订单号、支付流水号等信息，json格式，具体请参见“4.6 业务关联ID集合说明”。
	 **/
	private $refIds;
	
	/** 
	 * 描述分账明细信息，json格式，具体请参见“4.4 分账明细说明”。
	 **/
	private $royaltyParameters;
	
	/** 
	 * 卖家的分账类型，目前只支持传入ROYALTY（普通分账类型）。
	 **/
	private $royaltyType;
	
	/** 
	 * 卖家支付宝账号，可以为email或者手机号。
如果seller_id不为空，则以seller_id的值作为卖家账号，忽略本参数。
	 **/
	private $sellerEmail;
	
	/** 
	 * 卖家支付宝账号对应的支付宝唯一用户号。
以2088开头的纯16位数字。
如果和seller_email同时为空，则本参数默认填充partner的值。
	 **/
	private $sellerId;
	
	/** 
	 * 收银台页面上，商品展示的超链接。
	 **/
	private $showUrl;
	
	/** 
	 * 商品的标题/交易标题/订单标题/订单关键字等。
该参数最长为128个汉字。
	 **/
	private $subject;
	
	/** 
	 * 该笔订单的资金总额，取值范围[0.01,100000000]，精确到小数点后2位。
	 **/
	private $totalFee;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setAlipayCaRequest($alipayCaRequest)
	{
		$this->alipayCaRequest = $alipayCaRequest;
		$this->apiParas["alipay_ca_request"] = $alipayCaRequest;
	}

	public function getAlipayCaRequest()
	{
		return $this->alipayCaRequest;
	}

	public function setBody($body)
	{
		$this->body = $body;
		$this->apiParas["body"] = $body;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function setBuyerEmail($buyerEmail)
	{
		$this->buyerEmail = $buyerEmail;
		$this->apiParas["buyer_email"] = $buyerEmail;
	}

	public function getBuyerEmail()
	{
		return $this->buyerEmail;
	}

	public function setBuyerId($buyerId)
	{
		$this->buyerId = $buyerId;
		$this->apiParas["buyer_id"] = $buyerId;
	}

	public function getBuyerId()
	{
		return $this->buyerId;
	}

	public function setChannelParameters($channelParameters)
	{
		$this->channelParameters = $channelParameters;
		$this->apiParas["channel_parameters"] = $channelParameters;
	}

	public function getChannelParameters()
	{
		return $this->channelParameters;
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

	public function setDynamicId($dynamicId)
	{
		$this->dynamicId = $dynamicId;
		$this->apiParas["dynamic_id"] = $dynamicId;
	}

	public function getDynamicId()
	{
		return $this->dynamicId;
	}

	public function setDynamicIdType($dynamicIdType)
	{
		$this->dynamicIdType = $dynamicIdType;
		$this->apiParas["dynamic_id_type"] = $dynamicIdType;
	}

	public function getDynamicIdType()
	{
		return $this->dynamicIdType;
	}

	public function setExtendParams($extendParams)
	{
		$this->extendParams = $extendParams;
		$this->apiParas["extend_params"] = $extendParams;
	}

	public function getExtendParams()
	{
		return $this->extendParams;
	}

	public function setFormatType($formatType)
	{
		$this->formatType = $formatType;
		$this->apiParas["format_type"] = $formatType;
	}

	public function getFormatType()
	{
		return $this->formatType;
	}

	public function setGoodsDetail($goodsDetail)
	{
		$this->goodsDetail = $goodsDetail;
		$this->apiParas["goods_detail"] = $goodsDetail;
	}

	public function getGoodsDetail()
	{
		return $this->goodsDetail;
	}

	public function setItBPay($itBPay)
	{
		$this->itBPay = $itBPay;
		$this->apiParas["it_b_pay"] = $itBPay;
	}

	public function getItBPay()
	{
		return $this->itBPay;
	}

	public function setMcardParameters($mcardParameters)
	{
		$this->mcardParameters = $mcardParameters;
		$this->apiParas["mcard_parameters"] = $mcardParameters;
	}

	public function getMcardParameters()
	{
		return $this->mcardParameters;
	}

	public function setOperatorId($operatorId)
	{
		$this->operatorId = $operatorId;
		$this->apiParas["operator_id"] = $operatorId;
	}

	public function getOperatorId()
	{
		return $this->operatorId;
	}

	public function setOperatorType($operatorType)
	{
		$this->operatorType = $operatorType;
		$this->apiParas["operator_type"] = $operatorType;
	}

	public function getOperatorType()
	{
		return $this->operatorType;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->apiParas["out_trade_no"] = $outTradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setPrice($price)
	{
		$this->price = $price;
		$this->apiParas["price"] = $price;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		$this->apiParas["quantity"] = $quantity;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setRefIds($refIds)
	{
		$this->refIds = $refIds;
		$this->apiParas["ref_ids"] = $refIds;
	}

	public function getRefIds()
	{
		return $this->refIds;
	}

	public function setRoyaltyParameters($royaltyParameters)
	{
		$this->royaltyParameters = $royaltyParameters;
		$this->apiParas["royalty_parameters"] = $royaltyParameters;
	}

	public function getRoyaltyParameters()
	{
		return $this->royaltyParameters;
	}

	public function setRoyaltyType($royaltyType)
	{
		$this->royaltyType = $royaltyType;
		$this->apiParas["royalty_type"] = $royaltyType;
	}

	public function getRoyaltyType()
	{
		return $this->royaltyType;
	}

	public function setSellerEmail($sellerEmail)
	{
		$this->sellerEmail = $sellerEmail;
		$this->apiParas["seller_email"] = $sellerEmail;
	}

	public function getSellerEmail()
	{
		return $this->sellerEmail;
	}

	public function setSellerId($sellerId)
	{
		$this->sellerId = $sellerId;
		$this->apiParas["seller_id"] = $sellerId;
	}

	public function getSellerId()
	{
		return $this->sellerId;
	}

	public function setShowUrl($showUrl)
	{
		$this->showUrl = $showUrl;
		$this->apiParas["show_url"] = $showUrl;
	}

	public function getShowUrl()
	{
		return $this->showUrl;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
		$this->apiParas["subject"] = $subject;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function setTotalFee($totalFee)
	{
		$this->totalFee = $totalFee;
		$this->apiParas["total_fee"] = $totalFee;
	}

	public function getTotalFee()
	{
		return $this->totalFee;
	}

	public function getApiMethodName()
	{
		return "alipay.acquire.createandpay";
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
