<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectBanLog;
use App\Utils\DataTable;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_keys;

final class DetectBanLogController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'id' => '事件ID',
                'user_id' => '用户ID',
                'detect_number' => '违规次数',
                'ban_time' => '封禁时长(分钟)',
                'start_time' => '统计开始时间',
                'end_time' => '统计结束&封禁开始时间',
                'ban_end_time' => '封禁结束时间',
                'all_detect_number' => '累计违规次数',
            ],
        ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/detect_ban.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $detect_ban_log = DetectBanLog::query();

        $search = $request->getParam('search')['value'];

        if ($search !== '') {
            $detect_ban_log->where('user_id', '=', $search);
        }

        DataTable::applyOrder(
            $detect_ban_log,
            $request,
            array_keys(self::$details['field']),
            ['id', 'user_id', 'detect_number', 'ban_time', 'start_time', 'end_time', 'all_detect_number'],
            'id'
        );

        $filtered = $detect_ban_log->count();
        $total = (new DetectBanLog())->count();

        $bans = $detect_ban_log->paginate($length, '*', '', $page);

        foreach ($bans as $ban) {
            $ban->ban_end_time = $ban->banEndTime();
            $ban->start_time = Tools::toDateTime((int) $ban->start_time);
            $ban->end_time = Tools::toDateTime((int) $ban->end_time);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'bans' => $bans,
        ]);
    }
}
