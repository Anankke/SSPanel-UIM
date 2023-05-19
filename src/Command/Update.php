<?php

declare(strict_types=1);

namespace App\Command;

use tronovav\GeoIP2Update\Client;
use function array_diff;
use function copy;
use function file_get_contents;
use function file_put_contents;
use function preg_match;
use function preg_match_all;
use function str_replace;
use function strpos;
use function substr;
use const BASE_PATH;

final class Update extends Command
{
    public string $description = <<< END
├─=: php xcat Update         - 更新并迁移配置
END;

    public function boot(): void
    {
        // 迁移配置
        global $_ENV;
        $copy_result = copy(BASE_PATH . '/config/.config.php', BASE_PATH . '/config/.config.php.bak');

        if ($copy_result) {
            echo ".config.php 文件备份成功。\n";
        } else {
            echo ".config.php 文件备份失败，迁移终止。\n";
        }

        $config_old = file_get_contents(BASE_PATH . '/config/.config.php');
        $config_new = file_get_contents(BASE_PATH . '/config/.config.example.php');
        //将旧config迁移到新config上
        $migrated = [];

        foreach ($_ENV as $key => $value_reserve) {
            $regex = '/_ENV\[\'' . $key . '\'\].*?;/s';
            $matches_new = [];
            preg_match($regex, $config_new, $matches_new);
            if (isset($matches_new[0]) === false) {
                echo "未找到配置项：" . $key . " 未能在新版本 .config.php 文件中找到，可能已被更名或废弃。\n";
                continue;
            }

            $matches_old = [];
            preg_match($regex, $config_old, $matches_old);

            $config_new = str_replace($matches_new[0], $matches_old[0], $config_new);
            $migrated[] = '_ENV[\'' . $key . '\']';
        }
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

            echo "新增 .config.php 配置项：" . $difference . ":" . $comment . " \n";
        }

        echo "没有任何新 .config.php 配置项需要添加。\n";
        file_put_contents(BASE_PATH . '/config/.config.php', $config_new);
        echo "迁移完成。\n";

        // 更新 GeoLite2 数据库
        if ($_ENV['maxmind_license_key'] !== '') {
            echo "正在更新 GeoLite2 数据库...\n";
            $client = new Client([
                'license_key' => $_ENV['maxmind_license_key'],
                'dir' => BASE_PATH . '/storage/',
                'editions' => ['GeoLite2-City', "GeoLite2-Country"],
            ]);
            $client->run();
            echo "成功更新 GeoLite2 数据库。\n";
        }
    }
}
