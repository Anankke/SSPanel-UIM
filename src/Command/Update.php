<?php

namespace App\Command;

use App\Services\DefaultConfig;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Update
{
    public static function update($xcat)
    {
        global $_ENV;
        $copy_result = copy(BASE_PATH . '/config/.config.php', BASE_PATH . '/config/.config.php.bak');
        if ($copy_result == true) {
            echo ('备份成功' . PHP_EOL);
        } else {
            echo ('备份失败，迁移终止' . PHP_EOL);
            return false;
        }

        echo (PHP_EOL);

        // 检查并创建新增的配置项
        echo DefaultConfig::detectConfigs();

        echo ('开始升级客户端...' . PHP_EOL);
        Job::updatedownload();
        echo ('客户端升级结束' . PHP_EOL);

        echo ('开始升级 QQWry...' . PHP_EOL);
        $xcat->initQQWry();
        echo ('升级 QQWry结束' . PHP_EOL);

        echo (PHP_EOL);

        $config_old = file_get_contents(BASE_PATH . '/config/.config.php');
        $config_new = file_get_contents(BASE_PATH . '/config/.config.example.php');

        //执行版本升级
        $version_old = $_ENV['version'] ?? 0;
        self::old_to_new($version_old);

        //将旧config迁移到新config上
        $migrated = array();
        foreach ($_ENV as $key => $value_reserve) {
            if ($key == 'config_migrate_notice' || $key == 'version') {
                continue;
            }

            $regex = '/_ENV\[\'' . $key . '\'\].*?;/s';
            $matches_new = array();
            preg_match($regex, $config_new, $matches_new);
            if (isset($matches_new[0]) == false) {
                echo ('未找到配置项：' . $key . ' 未能在新config文件中找到，可能已被更名或废弃' . PHP_EOL);
                continue;
            }

            $matches_old = array();
            preg_match($regex, $config_old, $matches_old);

            $config_new = str_replace($matches_new[0], $matches_old[0], $config_new);
            $migrated[] = '_ENV[\'' . $key . '\']';
        }
        echo (PHP_EOL);

        //检查新增了哪些config
        $regex_new = '/_ENV\[\'.*?\'\]/s';
        $matches_new_all = array();
        preg_match_all($regex_new, $config_new, $matches_new_all);
        $differences = array_diff($matches_new_all[0], $migrated);
        foreach ($differences as $difference) {
            if (
                $difference == '_ENV[\'config_migrate_notice\']' ||
                $difference == '_ENV[\'version\']'
            ) {
                continue;
            }
            //匹配注释
            $regex_comment = '/' . $difference . '.*?;.*?(?=\n)/s';
            $regex_comment = str_replace(array('[', ']'), array('\[', '\]'), $regex_comment);
            $matches_comment = array();
            preg_match($regex_comment, $config_new, $matches_comment);
            $comment = '';
            if (isset($matches_comment[0])) {
                $comment = $matches_comment[0];
                $comment = substr(
                    $comment,
                    strpos(
                        $comment,
                        '//',
                        strpos($comment, ';') //查找';'之后的第一个'//'，然后substr其后面的comment
                    ) + 2
                );
            }
            //裁去首尾
            $difference = substr($difference, 15);
            $difference = substr($difference, 0, -2);

            echo ('新增配置项：' . $difference . ':' . $comment . PHP_EOL);
        }
        echo ('新增配置项通常带有默认值，因此通常即使不作任何改动网站也可以正常运行' . PHP_EOL);

        //输出notice
        $regex_notice = '/_ENV\[\'config_migrate_notice\'\].*?(?=\';)/s';
        $matches_notice = array();
        preg_match($regex_notice, $config_new, $matches_notice);
        $notice_new = $matches_notice[0];
        $notice_new = substr(
            $notice_new,
            strpos(
                $notice_new,
                '\'',
                strpos($notice_new, '=') //查找'='之后的第一个'\''，然后substr其后面的notice
            ) + 1
        );
        echo ('以下是迁移附注：');
        if (isset($_ENV['config_migrate_notice'])) {
            if ($_ENV['config_migrate_notice'] != $notice_new) {
                echo ($notice_new);
            }
        } else {
            echo ($notice_new);
        }
        echo (PHP_EOL);

        file_put_contents(BASE_PATH . '/config/.config.php', $config_new);
        echo (PHP_EOL . '迁移完成' . PHP_EOL);

        echo (PHP_EOL);

        echo ('开始升级composer依赖...' . PHP_EOL);
        system('php ' . BASE_PATH . '/composer.phar selfupdate');
        system('php ' . BASE_PATH . '/composer.phar install -d ' . BASE_PATH);
        echo ('升级composer依赖结束，请自行根据上方输出确认是否升级成功' . PHP_EOL);
        system('rm -rf ' . BASE_PATH . '/storage/framework/smarty/compile/*');
        system('chown -R www:www ' . BASE_PATH . '/storage');
    }

    public static function old_to_new($version_old)
    {
        if ($version_old < 2) {
            // 版本 2 开始
            if (!is_file(BASE_PATH . '/config/appprofile.php')) {
                echo ('创建 appprofile 文件.' . PHP_EOL);
                system('cp ' . BASE_PATH . '/config/appprofile.example.php ' . BASE_PATH . '/config/appprofile.php', $ret);
                echo $ret;
            }
            if (!Capsule::schema()->hasTable('gconfig')) {
                echo ('创建 gconfig 表.' . PHP_EOL);
                Capsule::schema()->create(
                    'gconfig',
                    function (Blueprint $table) {
                        $table->engine    = 'InnoDB';
                        $table->charset   = 'utf8mb4';
                        $table->collation = 'utf8mb4_unicode_ci';
                        $table->integer('id', true, true);
                        $table->string('key', 128)->comment('配置键名');
                        $table->string('type', 32)->comment('值类型');
                        $table->text('value')->comment('配置值');
                        $table->text('oldvalue')->comment('之前的配置值');
                        $table->string('name', 128)->comment('配置名称');
                        $table->text('comment')->comment('配置描述');
                        $table->integer('operator_id', false, true)->comment('操作员 ID');
                        $table->string('operator_name', 128)->comment('操作员名称');
                        $table->string('operator_email', 32)->comment('操作员邮箱');
                        $table->bigInteger('last_update')->comment('修改时间');
                    }
                );
            }
            if (!Capsule::schema()->hasTable('user_subscribe_log')) {
                echo ('创建 user_subscribe_log 表.' . PHP_EOL);
                Capsule::schema()->create(
                    'user_subscribe_log',
                    function (Blueprint $table) {
                        $table->engine    = 'InnoDB';
                        $table->charset   = 'utf8mb4';
                        $table->collation = 'utf8mb4_unicode_ci';
                        $table->integer('id', true, true);
                        $table->string('user_name', 128)->comment('用户名');
                        $table->integer('user_id', false, true)->comment('用户 ID');
                        $table->string('email', 32)->comment('用户邮箱');
                        $table->string('subscribe_type', 20)->default(null)->comment('获取的订阅类型');
                        $table->string('request_ip', 128)->default(null)->comment('请求 IP');
                        $table->dateTime('request_time')->default(null)->comment('请求时间');
                        $table->text('request_user_agent')->comment('请求 UA 信息');
                    }
                );
            }
            if (!Capsule::schema()->hasTable('telegram_tasks')) {
                echo ('创建 telegram_tasks 表.' . PHP_EOL);
                Capsule::schema()->create(
                    'telegram_tasks',
                    function (Blueprint $table) {
                        $table->engine    = 'InnoDB';
                        $table->charset   = 'utf8mb4';
                        $table->collation = 'utf8mb4_unicode_ci';
                        $table->integer('id', true, true);
                        $table->integer('type')->comment('任务类型');
                        $table->integer('status')->default(0)->comment('任务状态');
                        $table->string('chatid', 128)->default(0)->comment('Telegram Chat ID');
                        $table->string('messageid', 128)->default(0)->comment('Telegram Message ID');
                        $table->text('content')->default(null)->comment('任务详细内容');
                        $table->string('process', 32)->default(null)->comment('临时任务进度');
                        $table->integer('userid', false, true)->default(0)->comment('网站用户 ID');
                        $table->string('tguserid', 32)->default(0)->comment('Telegram User ID');
                        $table->bigInteger('executetime', false, true)->comment('任务执行时间');
                        $table->bigInteger('datetime', false, true)->comment('任务产生时间');
                    }
                );
            }
            if (!Capsule::schema()->hasTable('detect_ban_log')) {
                echo ('创建 detect_ban_log 表.' . PHP_EOL);
                Capsule::schema()->create(
                    'detect_ban_log',
                    function (Blueprint $table) {
                        $table->engine    = 'InnoDB';
                        $table->charset   = 'utf8mb4';
                        $table->collation = 'utf8mb4_unicode_ci';
                        $table->integer('id', true, true);
                        $table->string('user_name', 128)->comment('用户名');
                        $table->integer('user_id')->comment('用户 ID');
                        $table->string('email', 32)->comment('用户邮箱');
                        $table->integer('detect_number')->comment('本次违规次数');
                        $table->integer('ban_time')->comment('本次封禁时长');
                        $table->bigInteger('start_time')->comment('统计开始时间');
                        $table->bigInteger('end_time')->comment('统计结束时间');
                        $table->integer('all_detect_number')->comment('累计违规次数');
                    }
                );
            }
            // hasColumn 方法在 MySQL 8.0 存在永远返回 false，故不使用
            // if (!Capsule::schema()->hasColumn('user', 'last_detect_ban_time')) {
            //     Capsule::schema()->table(
            //         'user',
            //         function (Blueprint $table) {
            //             $table->dateTime('last_detect_ban_time')->default('1989-06-04 00:05:00')->after('enable');
            //         }
            //     );
            // }
            $UserAttributes = array_keys((new \App\Models\User())->first()->getAttributes());
            if (!in_array('last_detect_ban_time', $UserAttributes)) {
                echo ('添加 last_detect_ban_time 到 user 表.' . PHP_EOL);
                Capsule::schema()->table(
                    'user',
                    function (Blueprint $table) {
                        $table->dateTime('last_detect_ban_time')->default('1989-06-04 00:05:00')->after('enable');
                    }
                );
            }
            if (!in_array('all_detect_number', $UserAttributes)) {
                echo ('添加 all_detect_number 到 user 表.' . PHP_EOL);
                Capsule::schema()->table(
                    'user',
                    function (Blueprint $table) {
                        $table->integer('all_detect_number')->default(0)->after('last_detect_ban_time');
                    }
                );
            }
            /*
             * 避免表中无记录而导致导致添加失败
             */
            $DetectLog = new \App\Models\DetectLog();
            $DetectLog->user_id = 0;
            $DetectLog->list_id = 0;
            $DetectLog->node_id = 0;
            $DetectLog->datetime = 0;
            $DetectLog->save();
            $DetectlogAttributes = array_keys((new \App\Models\DetectLog())->first()->getAttributes());
            if (!in_array('status', $DetectlogAttributes)) {
                echo ('添加 status 到 detect_log 表.' . PHP_EOL);
                Capsule::schema()->table(
                    'detect_log',
                    function (Blueprint $table) {
                        $table->integer('status')->default(0)->after('node_id');
                    }
                );
            }
            /*
             * 删除该记录
             */
            $DetectLog->delete();
            // 版本 2 结束
        }
    }
}
