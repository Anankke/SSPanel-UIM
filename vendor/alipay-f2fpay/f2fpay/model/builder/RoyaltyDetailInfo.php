<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/20
 * Time: 上午11:33
 */

class RoyaltyDetailInfo
{
    //分账序列号，表示分账执行的顺序，必须为正整数
    private $serialNo;

    //接受分账金额的账户类型：默认值为userId。
    //userId：支付宝账号对应的支付宝唯一用户号。
    //bankIndex：分账到银行账户的银行编号。目前暂时只支持分账到一个银行编号。
    //storeId：分账到门店对应的银行卡编号。
    private $transInType;

    //(必填)分账批次号 分账批次号。 目前需要和转入账号类型为bankIndex配合使用
    private $batchNo;

    //商户分账的外部关联号，用于关联到每一笔分账信息，商户需保证其唯一性。
    //如果为空，该值则默认为“商户网站唯一订单号+分账序列号”
    private $outRelationId;

    //(必填)要分账的账户类型,默认值为userId
    //目前只支持userId：支付宝账号对应的支付宝唯一用户号
    private $transOutType;

    //(必填)如果转出账号类型为userId，本参数为要分账的支付宝账号对应的支付宝唯一用户号。
    //以2088开头的纯16位数字。
    private $transOut;

    //(必填)如果转入账号类型为userId，本参数为接受分账金额的支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字。
    //如果转入账号类型为bankIndex，本参数为28位的银行编号（商户和支付宝签约时确定）
    //如果转入账号类型为storeId，本参数为商户的门店ID。
    private $transIn;

    //(必填)分账的金额，单位为元
    private $amount;

    //分账描述信息
    private $desc;

    private $royaltyDetailInfo = array();

    public function __construct()
    {
        $this->setTransInType("userId");
        $this->setTransOutType("userId");
    }

    public function RoyaltyDetailInfo(){
        $this->__construct();
    }

    public function getRoyaltyDetailInfo()
    {
        return $this->royaltyDetailInfo;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getBatchNo()
    {
        return $this->batchNo;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getOutRelationId()
    {
        return $this->outRelationId;
    }

    public function getSerialNo()
    {
        return $this->serialNo;
    }

    public function getTransIn()
    {
        return $this->transIn;
    }

    public function getTransInType()
    {
        return $this->transInType;
    }

    public function getTransOut()
    {
        return $this->transOut;
    }

    public function getTransOutType()
    {
        return $this->transOutType;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        $this->royaltyDetailInfo['amount'] = $amount;
    }

    public function setBatchNo($batchNo)
    {
        $this->batchNo = $batchNo;
        $this->royaltyDetailInfo['batch_no'] = $batchNo;
    }

    public function setDesc($desc)
    {
        $this->desc = $desc;
        $this->royaltyDetailInfo['desc'] = $desc;
    }

    public function setOutRelationId($outRelationId)
    {
        $this->outRelationId = $outRelationId;
        $this->royaltyDetailInfo['out_relation_id'] = $outRelationId;
    }

    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
        $this->royaltyDetailInfo['serial_no'] = $serialNo;
    }

    public function setTransIn($transIn)
    {
        $this->transIn = $transIn;
        $this->royaltyDetailInfo['trans_in'] = $transIn;
    }

    public function setTransInType($transInType)
    {
        $this->transInType = $transInType;
        $this->royaltyDetailInfo['trans_in_type'] = $transInType;
    }

    public function setTransOut($transOut)
    {
        $this->transOut = $transOut;
        $this->royaltyDetailInfo['trans_out'] = $transOut;
    }

    public function setTransOutType($transOutType)
    {
        $this->transOutType = $transOutType;
        $this->royaltyDetailInfo['trans_out_type'] = $transOutType;
    }
}