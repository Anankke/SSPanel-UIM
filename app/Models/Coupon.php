<?php

namespace App\Models;

class Coupon extends Model
{
    protected $connection = 'default';
    protected $table = 'coupon';

    public function expire()
    {
        return date('Y-m-d H:i:s', $this->attributes['expire']);
    }

    public function order($shop)
    {
        if ($this->attributes['shop'] == '') {
            return true;
        }

        $shop_array = explode(',', $this->attributes['shop']);

        foreach ($shop_array as $shopid) {
            if ($shopid == $shop) {
                return true;
            }
        }

        return false;
    }
}
