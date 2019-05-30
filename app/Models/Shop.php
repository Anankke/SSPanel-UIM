<?php

namespace App\Models;

use App\Services\Config;

class Shop extends Model
{
    protected $connection = 'default';
    protected $table = 'shop';

    public function content()
    {
        $content = json_decode($this->attributes['content'], true);
        $content_text = '';
        $i = 0;
        foreach ($content as $key => $value) {
            switch ($key) {
                case 'bandwidth':
                    $content_text .= '添加流量 ' . $value . ' G ';
                    break;
                case 'expire':
                    $content_text .= ', 为账号的有效期添加 ' . $value . ' 天 ';
                    break;
                case 'class':
                    $content_text .= ', 为账号升级为等级 ' . $value . ' , 有效期 ' . $content['class_expire'] . ' 天 ';
                    break;
                case 'reset':
                    $content_text .= ', 在 ' . $content['reset_exp'] . ' 天内 ，每 ' . $value . ' 天重置流量为 ' . $content['reset_value'] . ' G ';
                    break;
                case 'speedlimit':
                    if ($value == 0) {
                        $content_text .= ', 用户端口不限速 ';
                    } else {
                        $content_text .= ', 用户端口限速变为' . $value . ' Mbps ';
                    }
                    break;
                case 'connector':
                    if ($value == 0) {
                        $content_text .= ', 用户IP不限制';
                    } else {
                        $content_text .= ', 用户IP限制变为 ' . $value . ' 个';
                    }
                    break;
                default:
            }

            //if ($i<count($content)&&$key!="connector") {
            //$content_text .= ",";
            //}

            $i++;
        }

        if ($content_text[strlen($content_text) - 1] == ',') {
            $content_text = substr($content_text, 0, -1);
        }

        return $content_text;
    }

    public function bandwidth()
    {
        $content = json_decode($this->attributes['content']);
        return $content->bandwidth ?? 0;
    }

    public function expire()
    {
        $content = json_decode($this->attributes['content']);
        return $content->expire ?? 0;
    }

    public function reset()
    {
        $content = json_decode($this->attributes['content']);
        return $content->reset ?? 0;
    }

    public function reset_value()
    {
        $content = json_decode($this->attributes['content']);
        return $content->reset_value ?? 0;
    }

    public function reset_exp()
    {
        $content = json_decode($this->attributes['content']);
        return $content->reset_exp ?? 0;
    }

    public function content_extra()
    {
        $content = json_decode($this->attributes['content']);
        if (isset($content->content_extra)) {
            $content_extra = $content->content_extra;
            $content_extra = explode(';', $content_extra);
            $content_extra_new = array();
            foreach ($content_extra as $innerContent) {
                if (false === strpos($innerContent, '-')) {
                    $innerContent = 'check-' . $innerContent;
                }
                $innerContent = explode('-', $innerContent);
                $content_extra_new[] = $innerContent;
            }
            $content_extra = $content_extra_new;
            return $content_extra;
        }

        return 0;
    }

    public function user_class()
    {
        $content = json_decode($this->attributes['content']);
        return $content->class ?? 0;
    }

    public function class_expire()
    {
        $content = json_decode($this->attributes['content']);
        return $content->class_expire ?? 0;
    }

    public function speedlimit()
    {
        $content = json_decode($this->attributes['content']);
        return $content->speedlimit ?? 0;
    }

    public function connector()
    {
        $content = json_decode($this->attributes['content']);
        return $content->connector ?? 0;
    }

    public function buy($user, $is_renew = 0)
    {
        $content = json_decode($this->attributes['content'], true);
        $content_text = '';

        foreach ($content as $key => $value) {
            switch ($key) {
                case 'bandwidth':
                    if ($is_renew == 0) {
                        if (Config::get('enable_bought_reset') == 'true') {
                            $user->transfer_enable = $value * 1024 * 1024 * 1024;
                            $user->u = 0;
                            $user->d = 0;
                            $user->last_day_t = 0;
                        } else {
                            $user->transfer_enable += $value * 1024 * 1024 * 1024;
                        }
                    } elseif ($this->attributes['auto_reset_bandwidth'] == 1) {
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
                    if (Config::get('enable_bought_extend') == 'true') {
                        if ($user->class == $value) {
                            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $content['class_expire'] * 86400);
                        } else {
                            $user->class_expire = date('Y-m-d H:i:s', time() + $content['class_expire'] * 86400);
                        }
                        $user->class = $value;
                    } else {
                        $user->class = $value;
                        $user->class_expire = date('Y-m-d H:i:s', time() + $content['class_expire'] * 86400);
                        break;
                    }
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
}
