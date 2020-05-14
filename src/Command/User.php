<?php

namespace App\Command;

use App\Models\Relay;
use App\Models\User as ModelsUser;
use App\Services\Config;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;

class User extends Command
{
    public $description = ''
        . '├─=: php xcat User [选项]' . PHP_EOL
        . '│ ├─ getCookie               - 获取指定用户的 Cookie' . PHP_EOL
        . '│ ├─ resetPort               - 重置单个用户端口' . PHP_EOL
        . '│ ├─ createAdmin             - 创建管理员帐号' . PHP_EOL
        . '│ ├─ resetAllPort            - 重置所有用户端口' . PHP_EOL
        . '│ ├─ resetTraffic            - 重置所有用户流量' . PHP_EOL
        . '│ ├─ cleanRelayRule          - 清除所有中转规则' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    /**
     * 重置用户端口
     *
     * @return void
     */
    public function resetPort()
    {
        fwrite(STDOUT, '请输入用户id: ');
        $user        = ModelsUser::Where('id', '=', trim(fgets(STDIN)))->first();
        if ($user !== null) {
            $origin_port = $user->port;
            $user->port  = Tools::getAvPort();
            $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
            foreach ($relay_rules as $rule) {
                $rule->port = $user->port;
                $rule->save();
            }
            if ($user->save()) {
                echo '重置成功!' . PHP_EOL;
            }
        } else {
            echo 'not found user.' . PHP_EOL;
        }
    }

    /**
     * 重置所有用户端口
     *
     * @return void
     */
    public function resetAllPort()
    {
        $users = ModelsUser::all();
        foreach ($users as $user) {
            $origin_port = $user->port;
            $user->port  = Tools::getAvPort();
            echo '$origin_port=' . $origin_port . '&$user->port=' . $user->port . PHP_EOL;
            $user->save();
        }
    }

    /**
     * 重置所有用户流量
     *
     * @return void
     */
    public function resetTraffic()
    {
        try {
            ModelsUser::where('enable', 1)->update([
                'd'          => 0,
                'u'          => 0,
                'last_day_t' => 0,
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            return;
        }
        echo 'reset traffic successful' . PHP_EOL;
    }

    /**
     * 清理所有中转规则
     *
     * @return void
     */
    public function cleanRelayRule()
    {
        $rules = Relay::all();
        foreach ($rules as $rule) {
            echo ($rule->id . "\n");
            if ($rule->source_node_id == 0) {
                echo ($rule->id . "被删除！\n");
                $rule->delete();
                continue;
            }
            $ruleset = Relay::where('user_id', $rule->user_id)->orwhere('user_id', 0)->get();
            $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $rule->id);
            if ($maybe_rule_id != 0) {
                echo ($rule->id . "被删除！\n");
                $rule->delete();
            }
        }
    }

    /**
     * 创建 Admin 账户
     *
     * @return void
     */
    public function createAdmin()
    {
        if (count($this->argv) === 3) {
            echo 'add admin/ 创建管理员帐号.....';
            // ask for input
            fwrite(STDOUT, 'Enter your email/输入管理员邮箱: ');
            // get input
            $email = trim(fgets(STDIN));
            // write input back
            fwrite(STDOUT, "Enter password for: $email / 为 $email 添加密码: ");
            $passwd = trim(fgets(STDIN));
            echo "Email: $email, Password: $passwd! ";
            fwrite(STDOUT, "Press [y] to create admin..... 按下[Y]确认来确认创建管理员账户..... \n");
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 5) {
            [,,, $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) == 'y') {
            echo 'start create admin account';
            // create admin user
            // do reg user
            $user                   = new ModelsUser();
            $user->user_name        = 'admin';
            $user->email            = $email;
            $user->pass             = Hash::passwordHash($passwd);
            $user->passwd           = Tools::genRandomChar(6);
            $user->port             = Tools::getLastPort() + 1;
            $user->t                = 0;
            $user->u                = 0;
            $user->d                = 0;
            $user->transfer_enable  = Tools::toGB((int) Config::getconfig('Register.string.defaultTraffic'));
            $user->invite_num       = (int) Config::getconfig('Register.string.defaultInviteNum');
            $user->ref_by           = 0;
            $user->is_admin         = 1;
            $user->expire_in        = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultExpire_in') * 86400);
            $user->reg_date         = date('Y-m-d H:i:s');
            $user->money            = 0;
            $user->im_type          = 1;
            $user->im_value         = '';
            $user->class            = 0;
            $user->plan             = 'A';
            $user->node_speedlimit  = 0;
            $user->theme            = $_ENV['theme'];

            $ga                     = new GA();
            $secret                 = $ga->createSecret();
            $user->ga_token         = $secret;
            $user->ga_enable        = 0;

            if ($user->save()) {
                echo 'Successful/添加成功!' . PHP_EOL;
            } else {
                echo '添加失败' . PHP_EOL;
            }
        } else {
            echo 'cancel' . PHP_EOL;
        }
    }

    /**
     * 获取 USERID 的 Cookie
     *
     * @return void
     */
    public function getCookie()
    {
        if (count($this->argv) === 4) {
            $user = ModelsUser::find($this->argv[3]);
            $expire_in = 86400 + time();
            echo Hash::cookieHash($user->pass, $expire_in) . ' ' . $expire_in;
        }
    }
}
