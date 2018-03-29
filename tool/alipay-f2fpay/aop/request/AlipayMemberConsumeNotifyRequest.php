<?php
/**
 * ALIPAY API: alipay.member.consume.notify request
 *
 * @author auto create
 * @since 1.0, 2014-12-09 14:01:31
 */
class AlipayMemberConsumeNotifyRequest
{
	/** 
	 * 实付金额
	 **/
	private $actPayAmount;
	
	/** 
	 * 会员卡卡号
	 **/
	private $bizCardNo;
	
	/** 
	 * point:整数
balance:金额格式
level:String
	 **/
	private $cardInfo;
	
	/** 
	 * 商户给会员开设的卡号，最大长度不超过32
	 **/
	private $externalCardNo;
	
	/** 
	 * 获取权益列表，是指由于发生当前交易，而使用户最终获取到的特权列表信息，
实际消耗的权益，这是个json字段
&#61550;	卡面额权益说明（元为单位）
benefitType：PRE_FUND（卡面额）
amount：80.00
&#61550;	券权益说明（张数为单位）
benefitType：COUPON
count：5
description：2元抵用券
amount：10.00
	 **/
	private $gainBenefitList;
	
	/** 
	 * 备注信息，现有直接填写门店信息
	 **/
	private $memo;
	
	/** 
	 * 门店编号
	 **/
	private $shopCode;
	
	/** 
	 * ALIPAY：支付宝电子卡
ENTITY：实体卡
OTHER：其他
	 **/
	private $swipeCertType;
	
	/** 
	 * 交易金额：本次交易的实际总金额（可认为标价金额）
	 **/
	private $tradeAmount;
	
	/** 
	 * 交易名称
	 **/
	private $tradeName;
	
	/** 
	 * 商户端对当前消费交易的单据号
	 **/
	private $tradeNo;
	
	/** 
	 * 交易事件
	 **/
	private $tradeTime;
	
	/** 
	 * 交易类型
消费：TRADE
充值：DEPOSIT
	 **/
	private $tradeType;
	
	/** 
	 * 实际消耗的权益，这是个json字段
&#61550;	卡面额权益说明（元为单位）
benefitType：PRE_FUND（卡面额）
amount：80.00

&#61550;	折扣权益说明（元为单位）
benefitType：DISCOUNT
amount：10.00
description：折扣10元

&#61550;	券权益说明（张数为单位）
benefitType：COUPON
count：5
description：2元抵用券
	 **/
	private $useBenefitList;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setActPayAmount($actPayAmount)
	{
		$this->actPayAmount = $actPayAmount;
		$this->apiParas["act_pay_amount"] = $actPayAmount;
	}

	public function getActPayAmount()
	{
		return $this->actPayAmount;
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

	public function setCardInfo($cardInfo)
	{
		$this->cardInfo = $cardInfo;
		$this->apiParas["card_info"] = $cardInfo;
	}

	public function getCardInfo()
	{
		return $this->cardInfo;
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

	public function setGainBenefitList($gainBenefitList)
	{
		$this->gainBenefitList = $gainBenefitList;
		$this->apiParas["gain_benefit_list"] = $gainBenefitList;
	}

	public function getGainBenefitList()
	{
		return $this->gainBenefitList;
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

	public function setShopCode($shopCode)
	{
		$this->shopCode = $shopCode;
		$this->apiParas["shop_code"] = $shopCode;
	}

	public function getShopCode()
	{
		return $this->shopCode;
	}

	public function setSwipeCertType($swipeCertType)
	{
		$this->swipeCertType = $swipeCertType;
		$this->apiParas["swipe_cert_type"] = $swipeCertType;
	}

	public function getSwipeCertType()
	{
		return $this->swipeCertType;
	}

	public function setTradeAmount($tradeAmount)
	{
		$this->tradeAmount = $tradeAmount;
		$this->apiParas["trade_amount"] = $tradeAmount;
	}

	public function getTradeAmount()
	{
		return $this->tradeAmount;
	}

	public function setTradeName($tradeName)
	{
		$this->tradeName = $tradeName;
		$this->apiParas["trade_name"] = $tradeName;
	}

	public function getTradeName()
	{
		return $this->tradeName;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->apiParas["trade_no"] = $tradeNo;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function setTradeTime($tradeTime)
	{
		$this->tradeTime = $tradeTime;
		$this->apiParas["trade_time"] = $tradeTime;
	}

	public function getTradeTime()
	{
		return $this->tradeTime;
	}

	public function setTradeType($tradeType)
	{
		$this->tradeType = $tradeType;
		$this->apiParas["trade_type"] = $tradeType;
	}

	public function getTradeType()
	{
		return $this->tradeType;
	}

	public function setUseBenefitList($useBenefitList)
	{
		$this->useBenefitList = $useBenefitList;
		$this->apiParas["use_benefit_list"] = $useBenefitList;
	}

	public function getUseBenefitList()
	{
		return $this->useBenefitList;
	}

	public function getApiMethodName()
	{
		return "alipay.member.consume.notify";
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
