<?php

declare(strict_types=1);

namespace App\Models;

use function time;

/**
 * @property-read   int     $id
 *
 * @property        string  $name
 * @property        float   $price
 * @property        array   $content
 * @property        int     $auto_renew
 * @property        int     $auto_reset_bandwidth
 * @property        int     $status
 */
final class Shop extends Model
{
    protected $connection = 'default';
    protected $table = 'shop';

    protected $casts = [
        'content' => 'array',
    ];

    public function reset()
    {
        return $this->content['reset'] ?? 0;
    }

    public function resetValue()
    {
        return $this->content['reset_value'] ?? 0;
    }

    public function resetExp()
    {
        return $this->content['reset_exp'] ?? 0;
    }

    public function buy($user, $is_renew = 0): void
    {
        if (isset($this->content['traffic_package'])) {
            $user->transfer_enable += $this->content['bandwidth'] * 1024 * 1024 * 1024;
            $user->save();
            return;
        }

        foreach ($this->content as $key => $value) {
            switch ($key) {
                case 'bandwidth':
                    if ($is_renew === 0) {
                        if ($_ENV['enable_bought_reset'] === true) {
                            $user->transfer_enable = $value * 1024 * 1024 * 1024;
                            $user->u = 0;
                            $user->d = 0;
                            $user->last_day_t = 0;
                        } else {
                            $user->transfer_enable += $value * 1024 * 1024 * 1024;
                        }
                    } elseif ($this->auto_reset_bandwidth === 1) {
                        $user->transfer_enable = $value * 1024 * 1024 * 1024;
                        $user->u = 0;
                        $user->d = 0;
                        $user->last_day_t = 0;
                    } else {
                        $user->transfer_enable += $value * 1024 * 1024 * 1024;
                    }
                    break;
                case 'expire':
                    if (time() > strtotime($user->expire_in)) {
                        $user->expire_in = date('Y-m-d H:i:s', time() + (int) $value * 86400);
                    } else {
                        $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + (int) $value * 86400);
                    }
                    break;
                case 'class':
                    if ($_ENV['enable_bought_extend'] === true) {
                        if ($user->class === $value) {
                            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + (int) $this->content['class_expire'] * 86400);
                        } else {
                            $user->class_expire = date('Y-m-d H:i:s', time() + (int) $this->content['class_expire'] * 86400);
                        }
                        $user->class = $value;
                    } else {
                        $user->class = $value;
                        $user->class_expire = date('Y-m-d H:i:s', time() + (int) $this->content['class_expire'] * 86400);
                        break;
                    }
                    // no break
                case 'speedlimit':
                    $user->node_speedlimit = $value;
                    break;
                case 'connector':
                    $user->node_connector = $value;
                    break;
                default:
            }
        }

        $user->save();
    }

    /*
     * 是否周期内循环重置性商品
     */
    public function useLoop(): bool
    {
        return $this->reset() !== 0 && $this->resetValue() !== 0 && $this->resetExp() !== 0;
    }
}
