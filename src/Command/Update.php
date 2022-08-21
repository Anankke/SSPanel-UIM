<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\DatatablesHelper;

final class Update extends Command
{
    public $description = '├─=: php xcat Update         - 更新并迁移配置' . PHP_EOL;

    public function boot(): void
    {
        global $_ENV;
        $copy_result = copy(BASE_PATH . '/config/.config.php', BASE_PATH . '/config/.config.php.bak');
        if ($copy_result === true) {
            echo '备份成功' . PHP_EOL;
        } else {
            echo '备份失败，迁移终止' . PHP_EOL;
        }

        echo PHP_EOL;

        echo '开始升级 QQWry...' . PHP_EOL;
        (new Tool($this->argv))->initQQWry();
        echo '升级 QQWry结束' . PHP_EOL;

        echo PHP_EOL;

        $config_old = file_get_contents(BASE_PATH . '/config/.config.php');
        $config_new = file_get_contents(BASE_PATH . '/config/.config.example.php');

        //将旧config迁移到新config上
        $migrated = [];
        foreach ($_ENV as $key => $value_reserve) {
            $regex = '/_ENV\[\'' . $key . '\'\].*?;/s';
            $matches_new = [];
            preg_match($regex, $config_new, $matches_new);
            if (isset($matches_new[0]) === false) {
                echo '未找到配置项：' . $key . ' 未能在新config文件中找到，可能已被更名或废弃' . PHP_EOL;
                continue;
            }

            $matches_old = [];
            preg_match($regex, $config_old, $matches_old);

            $config_new = str_replace($matches_new[0], $matches_old[0], $config_new);
            $migrated[] = '_ENV[\'' . $key . '\']';
        }
        echo PHP_EOL;

        //检查新增了哪些config
        $regex_new = '/_ENV\[\'.*?\'\]/s';
        $matches_new_all = [];
        preg_match_all($regex_new, $config_new, $matches_new_all);
        $differences = array_diff($matches_new_all[0], $migrated);
        foreach ($differences as $difference) {
            //匹配注释
            $regex_comment = '/' . $difference . '.*?;.*?(?=\n)/s';
            $regex_comment = str_replace(['[', ']'], ['\[', '\]'], $regex_comment);
            $matches_comment = [];
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

            echo '新增配置项：' . $difference . ':' . $comment . PHP_EOL;
        }
        echo '新增配置项通常带有默认值，因此通常即使不作任何改动网站也可以正常运行' . PHP_EOL;

        file_put_contents(BASE_PATH . '/config/.config.php', $config_new);
        echo PHP_EOL . '迁移完成' . PHP_EOL;

        echo PHP_EOL;

        echo '开始升级composer依赖...' . PHP_EOL;
        system('php ' . BASE_PATH . '/composer.phar selfupdate');
        system('php ' . BASE_PATH . '/composer.phar install -d ' . BASE_PATH);
        echo '升级composer依赖结束，请自行根据上方输出确认是否升级成功' . PHP_EOL;
        system('rm -rf ' . BASE_PATH . '/storage/framework/smarty/compile/*');
        system('chown -R ' . $_ENV['php_user_group'] . ' ' . BASE_PATH . '/storage');
    }
}
