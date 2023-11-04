<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function file_get_contents;
use function stream_context_create;
use function version_compare;
use const VERSION;

final class SystemController extends BaseController
{
    /**
     * 后台系统状态页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $last_daily_job_time = Tools::toDateTime(Config::obtain('last_daily_job_time'));
        $db_version = Config::obtain('db_version');

        return $response->write(
            $this->view()
                ->assign('version', VERSION)
                ->assign('last_daily_job_time', $last_daily_job_time)
                ->assign('db_version', $db_version)
                ->fetch('admin/system.tpl')
        );
    }

    /**
     * 检查版本更新
     */
    public function checkUpdate(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $latest_version = file_get_contents('https://ota.sspanel.org/get-latest-version', false, stream_context_create([
            'http' => [
                'timeout' => 3,
            ],
        ]));
        $is_upto_date = version_compare($latest_version, VERSION, '<=');

        return $response->withJson([
            'is_upto_date' => $is_upto_date,
            'latest_version' => $latest_version,
        ]);
    }
}
