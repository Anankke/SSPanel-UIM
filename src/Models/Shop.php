<?php

declare(strict_types=1);

namespace App\Models;

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

    public function content()
    {
        $content_text = '';
        foreach ($this->content as $key => $value) {
            switch ($key) {
                case 'bandwidth':
                    $content_text .= '添加流量 ' . $value . ' G ';
                    break;
                case 'expire':
                    $content_text .= ', 为账号的有效期添加 ' . $value . ' 天 ';
                    break;
                case 'class':
                    $content_text .= ', 为账号升级为等级 ' . $value . ' , 有效期 ' . $this->content['class_expire'] . ' 天 ';
                    break;
                case 'reset':
                    $content_text .= ', 在 ' . $this->content['reset_exp'] . ' 天内 ，每 ' . $value . ' 天重置流量为 ' . $this->content['reset_value'] . ' G ';
                    break;
                case 'speedlimit':
                    if ($value === 0) {
                        $content_text .= ', 用户端口不限速 ';
                    } else {
                        $content_text .= ', 用户端口限速变为' . $value . ' Mbps ';
                    }
                    break;
                case 'connector':
                    if ($value === 0) {
                        $content_text .= ', 用户IP不限制';
                    } else {
                        $content_text .= ', 用户IP限制变为 ' . $value . ' 个';
                    }
                    break;
                default:
            }
        }

        return rtrim($content_text, ',');
    }

    public function bandwidth()
    {
        return $this->content['bandwidth'] ?? 0;
    }

    public function expire()
    {
        return $this->content['expire'] ?? 0;
    }

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

    public function trafficPackage()
    {
        return isset($this->content['traffic_package']);
    }

    public function contentExtra()
    {
        if (isset($this->content['content_extra'])) {
            $content_extra = explode(';', $this->content['content_extra']);
            $content_extra_new = [];
            foreach ($content_extra as $innerContent) {
                if (strpos($innerContent, '-') === false) {
                    $innerContent = 'check-' . $innerContent;
                }
                $innerContent = explode('-', $innerContent);
                $content_extra_new[] = $innerContent;
            }
            return $content_extra_new;
        }

        return 0;
    }

    public function userClass()
    {
        return $this->content['class'] ?? 0;
    }

    public function classExpire()
    {
        return $this->content['class_expire'] ?? 0;
    }

    public function speedlimit()
    {
        return $this->content['speedlimit'] ?? 0;
    }

    public function connector()
    {
        return $this->content['connector'] ?? 0;
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
                        $user->expire_in = date('Y-m-d H:i:s', time() + $value * 86400);
                    } else {
                        $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $value * 86400);
                    }
                    break;
                case 'class':
                    if ($_ENV['enable_bought_extend'] === true) {
                        if ($user->class === $value) {
                            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $this->content['class_expire'] * 86400);
                        } else {
                            $user->class_expire = date('Y-m-d H:i:s', time() + $this->content['class_expire'] * 86400);
                        }
                        $user->class = $value;
                    } else {
                        $user->class = $value;
                        $user->class_expire = date('Y-m-d H:i:s', time() + $this->content['class_expire'] * 86400);
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

    /*
     * 获取周期商品销量
     */
    public function getSales(): int
    {
        $period = $_ENV['sales_period'];
        if ($period === 'expire') {
            $period = $this->content['class_expire'];
        }
        return Bought::where('shopid', $this->id)->where('datetime', '>', time() - $period * 86400)->count();
    }

    /*
     * 自动续费时间
     */
    public function autoRenew(): string
    {
        return $this->auto_renew === 0 ? '不自动续费' : $this->auto_renew . ' 天后续费';
    }

    /*
     * 流量是否自动重置
     */
    public function autoResetBandwidthString(): string
    {
        return $this->auto_reset_bandwidth === 0 ? '不自动重置' : '自动重置';
    }

    /*
     * 商品状态
     */
    public function status(): string
    {
        return $this->status === 1 ? '上架' : '下架';
    }
}
