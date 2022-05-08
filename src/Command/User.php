<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Setting;
use App\Models\User as ModelsUser;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;
use Ramsey\Uuid\Uuid;

final class User extends Command
{
    public $description = <<<EOL
├─=: php xcat User [选项]
│ ├─ getCookie               - 获取指定用户的 Cookie
│ ├─ resetPort               - 重置单个用户端口
│ ├─ createAdmin             - 创建管理员帐号
│ ├─ resetAllPort            - 重置所有用户端口
│ ├─ resetTraffic            - 重置所有用户流量
│ ├─ generateUUID            - 为所有用户生成新的 UUID
│ ├─ generateGa              - 为所有用户生成新的 Ga Secret;
EOL;
    public function boot(): void
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.';
            }
        }
    }

    /**
     * 重置用户端口
     */
    public function resetPort(): void
    {
        fwrite(STDOUT, '请输入用户id: ');
        $user = ModelsUser::find(trim(fgets(STDIN)));
        if ($user !== null) {
            $user->port = Tools::getAvPort();
            if ($user->save()) {
                echo '重置成功!';
            }
        } else {
            echo 'not found user.';
        }
    }

    /**
     * 重置所有用户端口
     */
    public function resetAllPort(): void
    {
        $users = ModelsUser::all();
        foreach ($users as $user) {
            $origin_port = $user->port;
            $user->port = Tools::getAvPort();
            echo '$origin_port=' . $origin_port . '&$user->port=' . $user->port . PHP_EOL;
            $user->save();
        }
    }

    /**
     * 重置所有用户流量
     */
    public function resetTraffic(): void
    {
        try {
            ModelsUser::where('enable', 1)->update([
                'd' => 0,
                'u' => 0,
                'last_day_t' => 0,
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            return;
        }
        echo 'reset traffic successful';
    }

    /**
     * 为所有用户生成新的UUID
     */
    public function generateUUID(): void
    {
        $users = ModelsUser::all();
        $current_timestamp = time();
        foreach ($users as $user) {
            /** @var ModelsUser $user */
            $user->generateUUID($current_timestamp);
        }
        echo 'generate UUID successful';
    }

    /**
     * 二次验证
     */
    public function generateGa(): void
    {
        $users = ModelsUser::all();
        foreach ($users as $user) {
            $ga = new GA();
            $secret = $ga->createSecret();

            $user->ga_token = $secret;
            $user->save();
        }
        echo 'generate Ga Secret successful';
    }

    /**
     * 创建 Admin 账户
     */
    public function createAdmin(): void
    {
        if (count($this->argv) === 3) {
            // ask for input
            fwrite(STDOUT, '(1/3) 请输入管理员邮箱：') . PHP_EOL;
            // get input
            $email = trim(fgets(STDIN));
            if ($email === null) {
                die("必须输入管理员邮箱.\r\n");
            }

            // write input back
            fwrite(STDOUT, '(2/3) 请输入管理员账户密码：') . PHP_EOL;
            $passwd = trim(fgets(STDIN));
            if ($passwd === null) {
                die("必须输入管理员密码.\r\n");
            }

            fwrite(STDOUT, '(3/3) 按 Y 或 y 确认创建：');
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 5) {
            [,,, $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) === 'y') {
            $current_timestamp = time();
            // create admin user
            $configs = Setting::getClass('register');
            // do reg user
            $user = new ModelsUser();
            $user->user_name = 'admin';
            $user->email = $email;
            $user->remark = 'admin';
            $user->pass = Hash::passwordHash($passwd);
            $user->passwd = Tools::genRandomChar(16);
            $user->uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $email . '|' . $current_timestamp);
            $user->port = Tools::getLastPort() + 1;
            $user->t = 0;
            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = Tools::toGB($configs['sign_up_for_free_traffic']);
            $user->invite_num = $configs['sign_up_for_invitation_codes'];
            $user->ref_by = 0;
            $user->is_admin = 1;
            $user->expire_in = date('Y-m-d H:i:s', time() + $configs['sign_up_for_free_time'] * 86400);
            $user->reg_date = date('Y-m-d H:i:s');
            $user->money = 0;
            $user->im_type = 1;
            $user->im_value = '';
            $user->class = 0;
            $user->node_speedlimit = 0;
            $user->theme = $_ENV['theme'];

            $ga = new GA();
            $secret = $ga->createSecret();
            $user->ga_token = $secret;
            $user->ga_enable = 0;

            if ($user->save()) {
                echo '创建成功，请在主页登录' . PHP_EOL;
            } else {
                echo '创建失败，请检查数据库配置' . PHP_EOL;
            }
        } else {
            echo '已取消创建' . PHP_EOL;
        }
    }

    /**
     * 获取 USERID 的 Cookie
     */
    public function getCookie(): void
    {
        if (count($this->argv) === 4) {
            $user = ModelsUser::find($this->argv[3]);
            $expire_in = 86400 + time();
            echo Hash::cookieHash($user->pass, $expire_in) . ' ' . $expire_in;
        }
    }
}
