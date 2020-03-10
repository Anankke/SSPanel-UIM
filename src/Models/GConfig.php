<?php

namespace App\Models;

use App\Services\DefaultConfig;

class GConfig extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gconfig';

    /**
     * 恢复默认配置
     *
     * @param User $user
     *
     * @return void
     */
    public function recover($user)
    {
        $this->oldvalue       = $this->value;
        $this->value          = DefaultConfig::default_value($this->key)['value'];
        $this->operator_id    = $user->id;
        $this->operator_name  = ('[恢复默认] - ' . $user->user_name);
        $this->operator_email = $user->email;
        $this->last_update    = time();
        $this->save();
    }

    /**
     * 获取配置值
     *
     * @return mixed
     */
    public function getValue()
    {
        switch ($this->type) {
            case 'bool':
                return (bool)      $this->value;
            case 'array':
                return json_decode($this->value, true);
            case 'string':
                return (string)    $this->value;
            default:
                return (string)    $this->value;
        }
    }

    /**
     * 设定配置值
     *
     * @param mixed $value
     * @param User  $user
     *
     * @return bool
     */
    public function setValue($value, $user = null)
    {
        $this->oldvalue = $this->value;
        $this->value    = $this->typeConversion($value);
        if ($user === null) {
            $this->operator_id    = 0;
            $this->operator_name  = '系统修改';
            $this->operator_email = 'admin@admin.com';
        } else {
            $this->operator_id    = $user->id;
            $this->operator_name  = $user->user_name;
            $this->operator_email = $user->email;
        }
        $this->last_update = time();
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 配置值得类型转换
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function typeConversion($value)
    {
        switch ($this->type) {
            case 'bool':
                return (string) $value;
            case 'array':
                return json_encode($value, 320);
            case 'string':
                return (string) $value;
            default:
                return (string) $value;
        }
    }
}
