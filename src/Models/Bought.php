<?php

namespace App\Models;

/**
 * Bought Model
 *
 * @property-read   int     $id         id
 * @property        int     $userid     User id
 * @property        int     $shopid     Shop id
 * @property        string  $datetime   Bought complete datetime
 * @property        int     $renew      Time to renew this bought
 * @property        string  $coupon     Coupon applied to this bought
 * @property        float   $price      Price after coupon applied
 * @property        bool    $is_notified If this bought is notified for renew
 */
class Bought extends Model
{
    protected $connection = 'default';

    protected $table = 'bought';

    /**
     * [静态方法] 删除不存在的用户的记录
     *
     * @param Bought $Bought
     */
    public static function user_is_null($Bought): void
    {
        self::where('userid', $Bought->userid)->delete();
    }

    /**
     * [静态方法] 删除不存在的商品的记录
     *
     * @param Bought $Bought
     */
    public static function shop_is_null($Bought): void
    {
        self::where('shopid', $Bought->shopid)->delete();
    }

    /**
     * 自动续费时间
     */
    public function renew(): string
    {
        if ($this->renew == 0) {
            return '不自动续费';
        }
        return date('Y-m-d H:i:s', $this->renew) . ' 时续费';
    }

    /**
     * 购买日期
     */
    public function datetime(): string
    {
        return date('Y-m-d H:i:s', $this->datetime);
    }

    /**
     * 购买用户
     */
    public function user(): ?User
    {
        return User::find($this->userid);
    }

    /**
     * 购买用户名
     */
    public function user_name(): string
    {
        if ($this->user() == null) {
            return '用户已不存在';
        }
        return $this->user()->user_name;
    }

    /**
     * 商品
     */
    public function shop(): ?Shop
    {
        return Shop::find($this->shopid);
    }

    /**
     * 商品内容
     */
    public function content(): string
    {
        if ($this->shop() == null) {
            return '商品已不存在';
        }
        return $this->shop()->content();
    }

    /**
     * 流量是否自动重置
     */
    public function auto_reset_bandwidth(): string
    {
        if ($this->shop() == null) {
            return '商品已不存在';
        }
        return $this->shop()->auto_reset_bandwidth == 0 ? '不自动重置' : '自动重置';
    }

    /*
     * 套餐已使用的天数
     */
    public function used_days(): int
    {
        return (int) ((time() - $this->datetime) / 86400);
    }

    /*
     * 是否有效期内
     */
    public function valid(): bool
    {
        $shop = $this->shop();
        if ($shop->use_loop()) {
            return (time() - $shop->reset_exp() * 86400 < $this->datetime);
        }
        return false;
    }

    /*
     * 下一次流量重置时间
     */
    public function reset_time($unix = false)
    {
        $shop = $this->shop();
        if ($shop->use_loop()) {
            $day = 24 * 60 * 60;
            $resetIndex = 1 +  (int)((time() - $this->datetime - $day) / ($shop->reset() * $day));
            $restTime = $resetIndex * $shop->reset() * $day + $this->datetime;
            $time = time() + ($day * 86400);
            return (!$unix ? date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d', $restTime)))) : $time);
        }
        return (!$unix ? '-' : 0);
    }

    /*
     * 过期时间
     */
    public function exp_time($unix = false)
    {
        $shop = $this->shop();
        if ($shop->use_loop()) {
            $time = $this->datetime + ($shop->reset_exp() * 86400);
            return (!$unix ? date('Y-m-d H:i:s', $time) : $time);
        }
        return (!$unix ? '-' : 0);
    }
}
