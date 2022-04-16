<?php
namespace App\Models;

class WorkOrder extends Model
{
    protected $connection = 'default';
    protected $table = 'work_order';

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getClosedAtAttribute($value)
    {
        return ($value == null) ? 'null' : date('Y-m-d H:i:s', $value);
    }

    public function getClosedByAttribute($value)
    {
        return ($value == null) ? '开启中' : '已关闭';
    }
}
