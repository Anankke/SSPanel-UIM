<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Config;
use App\Models\Link;
use App\Models\Node;
use App\Models\User as ModelsUser;
use App\Services\MFA;
use App\Utils\Hash;
use App\Utils\Tools;
use danielsreichenbach\GeoIP2Update\Client;
use Exception;
use Ramsey\Uuid\Uuid;
use function count;
use function date;
use function fgets;
use function file_get_contents;
use function fwrite;
use function in_array;
use function json_decode;
use function method_exists;
use function strtolower;
use function trim;
use const BASE_PATH;
use const PHP_EOL;
use const STDIN;
use const STDOUT;

final class Tool extends Command
{
    public string $description = <<<EOL
├─=: php xcat Tool [选项]
│ ├─ resetSetting        - 使用默认值覆盖数据库配置
│ ├─ importSetting       - 导入数据库配置
│ ├─ resetNodePassword   - 重置所有节点通讯密钥
│ ├─ resetNodeBandwidth  - 重置所有节点流量
│ ├─ resetPort           - 重置所有用户端口
│ ├─ resetBandwidth      - 重置所有用户流量
│ ├─ resetTodayBandwidth - 重置今日流量
│ ├─ resetPassword       - 重置所有用户登录密码
│ ├─ resetPasswd         - 重置所有用户连接密码
│ ├─ clearSubToken       - 清除用户 Sub Token
│ ├─ generateUUID        - 为所有用户生成新的 UUID
│ ├─ generateGa          - 为所有用户生成新的 Ga Secret
│ ├─ generateApiToken    - 为所有用户生成新的 API Token
│ ├─ setTheme            - 为所有用户设置新的主题
│ ├─ setLocale           - 为所有用户设置新的语言
│ ├─ createAdmin         - 创建管理员帐号
│ └─ updateGeoIP2        - 更新 GeoIP2 数据库

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
                echo '方法不存在' . PHP_EOL;
            }
        }
    }

    public function resetSetting(): void
    {
        $settings = Config::all();

        foreach ($settings as $setting) {
            $setting->value = $setting->default;
            $setting->save();
        }

        echo '已使用默认值覆盖所有数据库设置' . PHP_EOL;
    }

    public function importSetting(): void
    {
        $json_settings = file_get_contents('./config/settings.json');
        $settings = json_decode($json_settings, true);
        $config = [];
        $add_counter = 0;
        $update_counter = 0;
        $del_counter = 0;

        // 检查新增
        foreach ($settings as $item) {
            $config[] = $item['item'];
            $item_name = $item['item'];
            $query = (new Config())->where('item', $item['item'])->first();

            if ($query === null) {
                $new_item = new Config();
                $new_item->item = $item['item'];
                $new_item->value = $item['value'];
                $new_item->class = $item['class'];
                $new_item->is_public = $item['is_public'];
                $new_item->type = $item['type'];
                $new_item->default = $item['default'];
                $new_item->mark = $item['mark'];
                $new_item->save();

                echo '添加新数据库设置：' . $item_name . PHP_EOL;
                $add_counter += 1;
                continue;
            }

            if ($query->class !== $item['class']) {
                $query->class = $item['class'];
                $query->save();
                echo '更新数据库设置：' . $item_name . PHP_EOL;
                $update_counter += 1;
            }
        }
        // 检查移除
        $db_settings = Config::all();

        foreach ($db_settings as $db_setting) {
            if (! in_array($db_setting->item, $config)) {
                $db_setting->delete();
                $del_counter += 1;
            }
        }

        if ($add_counter !== 0) {
            echo '添加了 ' . $add_counter . ' 项新数据库设置' . PHP_EOL;
        }

        if ($update_counter !== 0) {
            echo '更新了 ' . $update_counter . ' 项数据库设置' . PHP_EOL;
        }

        if ($del_counter !== 0) {
            echo '移除了 ' . $del_counter . ' 项数据库设置' . PHP_EOL;
        }
    }

    public function resetNodePassword(): void
    {
        $nodes = Node::all();

        foreach ($nodes as $node) {
            $node->password = Tools::genRandomChar(32);
            $node->save();
        }

        echo '已重置所有节点密码' . PHP_EOL;
    }

    public function resetNodeBandwidth(): void
    {
        $nodes = Node::all();

        foreach ($nodes as $node) {
            $node->node_bandwidth = 0;
            $node->save();
        }

        echo '已重置所有节点流量' . PHP_EOL;
    }

    /**
     * 重置所有用户端口
     */
    public function resetPort(): void
    {
        $users = ModelsUser::all();

        if (count($users) === 0 || count($users) >= 65535) {
            echo '无效的用户数量' . PHP_EOL;
            return;
        }

        (new ModelsUser())->update([
            'port' => 0,
        ]);

        foreach ($users as $user) {
            $user->port = Tools::getSsPort();
            $user->save();
        }

        echo '已重置所有用户端口' . PHP_EOL;
    }

    /**
     * 重置所有用户流量
     */
    public function resetBandwidth(): void
    {
        (new ModelsUser())->where('is_banned', 0)->update([
            'd' => 0,
            'u' => 0,
            'transfer_today' => 0,
        ]);

        echo '已重置所有用户流量' . PHP_EOL;
    }

    /**
     * 重置今日流量
     */
    public function resetTodayBandwidth(): void
    {
        (new ModelsUser())->query()->update(['transfer_today' => 0]);

        echo '已重置今日流量' . PHP_EOL;
    }

    /**
     * 重置所有用户登录密码
     */
    public function resetPassword(): void
    {
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->pass = Hash::passwordHash(Tools::genRandomChar(32));
            $user->save();
        }

        echo '已重置所有用户登录密码' . PHP_EOL;
    }

    /**
     * 重置所有用户连接密码
     */
    public function resetPasswd(): void
    {
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->passwd = Tools::genRandomChar(16);
            $user->save();
        }

        echo '已重置所有用户连接密码' . PHP_EOL;
    }

    /**
     * 清除用户 Sub Token
     */
    public function clearSubToken(): void
    {
        Link::query()->truncate();

        echo '已清除所有用户 Sub Token' . PHP_EOL;
    }

    /**
     * 为所有用户生成新的 UUID
     */
    public function generateUUID(): void
    {
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->uuid = Uuid::uuid4();
            $user->save();
        }

        echo '已为所有用户生成新的 UUID' . PHP_EOL;
    }

    /**
     * 二次验证
     */
    public function generateGa(): void
    {
        $users = ModelsUser::all();

        foreach ($users as $user) {
            try {
                $user->ga_token = MFA::generateGaToken();
                $user->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        echo '已为所有用户生成新的 Ga Secret' . PHP_EOL;
    }

    /**
     * 为所有用户生成新的 Api Token
     */
    public function generateApiToken(): void
    {
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->api_token = Tools::genRandomChar(32);
            $user->save();
        }

        echo '已为所有用户生成新的 Api Token' . PHP_EOL;
    }

    /**
     * 为所有用户设置新的主题
     */
    public function setTheme(): void
    {
        fwrite(STDOUT, '请输入要设置的主题名称: ');
        $theme = trim(fgets(STDIN));
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->theme = $theme;
            $user->save();
        }

        echo '已为所有用户设置新的主题: ' . $theme . PHP_EOL;
    }

    /**
     * Set locale for all users
     */
    public function setLocale(): void
    {
        fwrite(STDOUT, 'Please input the new locale: ');
        $locale = trim(fgets(STDIN));
        $users = ModelsUser::all();

        foreach ($users as $user) {
            $user->locale = $locale;
            $user->save();
        }

        echo 'Set locale for all users successfully.' . PHP_EOL;
    }

    /**
     * 创建 Admin 账户
     *
     * @throws Exception
     */
    public function createAdmin(): void
    {
        $y = '';
        $email = '';
        $passwd = '';

        if (count($this->argv) === 3) {
            // ask for input
            echo '(1/3) 请输入管理员邮箱：' . PHP_EOL;
            // get input
            $email = trim(fgets(STDIN));
            // write input back
            echo '(2/3) 请输入管理员账户密码：' . PHP_EOL;
            $passwd = trim(fgets(STDIN));
            echo '(3/3) 按 Y 或 y 确认创建：';
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 5) {
            [,,, $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) === 'y') {
            // do reg user
            $user = new ModelsUser();
            $user->user_name = 'Admin';
            $user->email = $email;
            $user->remark = '';
            $user->pass = Hash::passwordHash($passwd);
            $user->passwd = Tools::genRandomChar(16);
            $user->uuid = Uuid::uuid4();
            $user->api_token = Tools::genRandomChar(32);
            $user->port = Tools::getSsPort();
            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = 0;
            $user->ref_by = 0;
            $user->is_admin = 1;
            $user->reg_date = date('Y-m-d H:i:s');
            $user->money = 0;
            $user->im_type = 0;
            $user->im_value = '';
            $user->class = 0;
            $user->node_iplimit = 0;
            $user->node_speedlimit = 0;
            $user->theme = $_ENV['theme'];
            $user->locale = $_ENV['locale'];

            $user->ga_token = MFA::generateGaToken();
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

    public function updateGeoIP2(): void
    {
        if ($_ENV['maxmind_account_id'] !== '' && $_ENV['maxmind_license_key'] !== '') {
            echo 'Updating GeoIP2 database...' . PHP_EOL;

            $client = new Client([
                'account_id' => $_ENV['maxmind_account_id'],
                'license_key' => $_ENV['maxmind_license_key'],
                'dir' => BASE_PATH . '/storage/',
                'editions' => ['GeoLite2-City', 'GeoLite2-Country'],
            ]);

            try {
                $client->run();
                echo 'Successfully updated GeoIP2 database.' . PHP_EOL;
            } catch (Exception $e) {
                echo 'Update GeoIP2 database failed.' . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
            }
        } else {
            echo 'Please configure maxmind_account_id & maxmind_license_key in config/.config.php' . PHP_EOL;
        }
    }
}
