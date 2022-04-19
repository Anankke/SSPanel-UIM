<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Indexyz
 * Date: 11/9/2018
 * Time: 9:15 PM
 */

namespace App\Utils;

use App\Services\Config;
use Smarty;

final class ConfRender
{
    public static function getTemplateRender()
    {
        $smarty = new Smarty();

        $smarty->settemplatedir(BASE_PATH . '/resources/conf/');
        $smarty->setcompiledir(BASE_PATH . '/storage/framework/smarty/compile/');
        $smarty->setcachedir(BASE_PATH . '/storage/framework/smarty/cache/');
        $smarty->registerClass('config', Config::class);
        return $smarty;
    }
}
