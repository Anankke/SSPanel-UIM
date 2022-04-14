<?php
namespace App\Models;

class GiftCard extends Model
{
    protected $connection = 'default';
    protected $table = 'gift_card';

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getBalanceAttribute($value)
    {
        return sprintf("%.2f", $value / 100);
    }

    public function getStatusAttribute($value)
    {
        return ($value == '0') ? '未使用' : '已用';
    }

    public function getUsedAtAttribute($value)
    {
        return ($value == '0') ? 'null' : date('Y-m-d H:i:s', $value);
    }
}
