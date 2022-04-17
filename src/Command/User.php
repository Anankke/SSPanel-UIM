<?php
namespace App\Command;

use Exception;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Tools;
use Ramsey\Uuid\Uuid;
use App\Services\Config;
use App\Models\Setting;
use App\Models\User as ModelsUser;
use App\Controllers\AuthController;

class User extends Command
{
    public $description = ''
        . '├─=: php xcat User [选项]' . PHP_EOL
        . '│ ├─ getCookie               - 获取指定用户的 Cookie' . PHP_EOL
        . '│ ├─ resetPort               - 重置单个用户端口' . PHP_EOL
        . '│ ├─ createAdmin             - 创建管理员帐号' . PHP_EOL
        . '│ ├─ resetAllPort            - 重置所有用户端口' . PHP_EOL
        . '│ ├─ resetTraffic            - 重置所有用户流量' . PHP_EOL
        . '│ ├─ generateUUID            - 为所有用户生成新的 UUID' . PHP_EOL
        . '│ ├─ generateGa              - 为所有用户生成新的 Ga Secret' . PHP_EOL;

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
        $user = ModelsUser::find(trim(fgets(STDIN)));
        if ($user !== null) {
            $user->port = Tools::getAvPort();
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
     * 为所有用户生成新的UUID
     *
     * @return void
     */
    public function generateUUID()
    {
        $users = ModelsUser::all();
        $current_timestamp = time();
        foreach ($users as $user) {
            /** @var ModelsUser $user */
            $user->generateUUID($current_timestamp);
        }
        echo 'generate UUID successful' . PHP_EOL;
    }

    /**
     * 二次验证
     *
     * @return void
     */
    public function generateGa()
    {
        $users = ModelsUser::all();
        foreach ($users as $user) {
            $ga = new GA();
            $secret = $ga->createSecret();

            $user->ga_token = $secret;
            $user->save();
        }
        echo 'generate Ga Secret successful' . PHP_EOL;
    }

    /**
     * 创建 Admin 账户
     *
     * @return void
     */
    public function createAdmin()
    {
        if (count($this->argv) === 3) {
            // ask for input
            fwrite(STDOUT, '(1/3) 请输入管理员邮箱：') . PHP_EOL;
            // get input
            $email = trim(fgets(STDIN));
            if ($email == null) {
                die("必须输入管理员邮箱.\r\n");
            }

            // write input back
            fwrite(STDOUT, "(2/3) 请输入管理员账户密码：") . PHP_EOL;
            $passwd = trim(fgets(STDIN));
            if ($passwd == null) {
                die("必须输入管理员密码.\r\n");
            }

            fwrite(STDOUT, "(3/3) 按 Y 或 y 确认创建：");
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 5) {
            [,,, $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) == 'y') {
            try {
                AuthController::register_helper('admin', $email, $passwd, '', '1', '', 0, false);
                $last_user = User::orderBy('id', 'desc')->first();
                $last_user->is_admin = 1;
                $last_user->save();
            } catch (\Exception $e) {
                $error_msg = $e->getMessage();
            }

            if (!empty($error_msg)) {
                echo PHP_EOL . '创建失败，以下是错误信息：' . PHP_EOL;
                die($error_msg);
            }

            echo PHP_EOL . '创建成功，请在主页登录' . PHP_EOL;
        } else {
            echo PHP_EOL . '已取消创建' . PHP_EOL;
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
