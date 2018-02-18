<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/19
 * Time: 下午2:09
 */
class GoodsDetail
{
    // 商品编号(国标)
    private $goodsId;

    //支付宝定义的统一商品编号
    private $alipayGoodsId;

    // 商品名称
    private $goodsName;

    // 商品数量
    private $quantity;

    // 商品价格，此处单位为元，精确到小数点后2位
    private $price;

    // 商品类别
    private $goodsCategory;

    // 商品详情
    private $body;

    private $goodsDetail = array();

    //单个商品json字符串
    //private $goodsDetailStr = NULL;

    //获取单个商品的json字符串
    public function getGoodsDetail()
    {
        return $this->goodsDetail;
        /*$this->goodsDetailStr = "{";
        foreach ($this->goodsDetail as $k => $v){
            $this->goodsDetailStr.= "\"".$k."\":\"".$v."\",";
        }
        $this->goodsDetailStr = substr($this->goodsDetailStr,0,-1);
        $this->goodsDetailStr.= "}";
        return $this->goodsDetailStr;*/
    }

    public function setGoodsId($goodsId)
    {
        $this->goodsId = $goodsId;
        $this->goodsDetail['goods_id'] = $goodsId;
    }

    public function getGoodsId()
    {
        return $this->goodsId;
    }

    public function setAlipayGoodsId($alipayGoodsId)
    {
        $this->alipayGoodsId = $alipayGoodsId;
        $this->goodsDetail['alipay_goods_id'] = $alipayGoodsId;
    }

    public function getAlipayGoodsId()
    {
        return $this->alipayGoodsId;
    }

    public function setGoodsName($goodsName)
    {
        $this->goodsName = $goodsName;
        $this->goodsDetail['goods_name'] = $goodsName;
    }

    public function getGoodsName()
    {
        return $this->goodsName;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->goodsDetail['quantity'] = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        $this->goodsDetail['price'] = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setGoodsCategory($goodsCategory)
    {
        $this->goodsCategory = $goodsCategory;
        $this->goodsDetail['goods_category'] = $goodsCategory;
    }

    public function getGoodsCategory()
    {
        return $this->goodsCategory;
    }

    public function setBody($body)
    {
        $this->body = $body;
        $this->goodsDetail['body'] = $body;
    }

    public function getBody()
    {
        return $this->body;
    }


}

?>