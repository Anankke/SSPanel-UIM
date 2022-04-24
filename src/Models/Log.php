<?php
namespace App\Models;

class Log extends Model
{
    protected $connection = 'default';
    protected $table = 'log';

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getStatusAttribute($value)
    {
        return ($value == '0') ? '未处理' : '已处理';
    }
}
