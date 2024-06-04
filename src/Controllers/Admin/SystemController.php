<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function version_compare;
use const OTA_API_URL;
use const PANEL_VERSION;

final class SystemController extends BaseController
{
    /**
     * 后台系统状态页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('version', PANEL_VERSION)
                ->assign('last_daily_job_time', Tools::toDateTime(Config::obtain('last_daily_job_time')))
                ->assign('db_version', Config::obtain('db_version'))
                ->fetch('admin/system.tpl')
        );
    }

    /**
     * 检查版本更新
     */
    public function checkUpdate(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $client = new Client();

        $headers = [
            'User-Agent' => 'NeXT-Panel/' . PANEL_VERSION,
            'Panel-Type' => 'NeXT',
            'Accept' => 'application/json',
        ];

        try {
            $latest_version = $client->get(OTA_API_URL, [
                'headers' => $headers,
                'timeout' => 3,
            ])->getBody()->getContents();
        } catch (GuzzleException $e) {
            return ResponseHelper::error($response, '检查更新失败：' . $e->getMessage());
        }

        return $response->withJson([
            'ret' => 1,
            'is_up_to_date' => version_compare($latest_version, PANEL_VERSION, '<='),
            'latest_version' => $latest_version,
        ]);
    }
}
