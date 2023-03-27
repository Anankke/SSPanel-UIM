<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Paylist;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class PaylistController extends BaseController
{
    public static array $details =
    [
        'field' => [
            'id' => '事件ID',
            'userid' => '用户ID',
            'total' => '金额',
            'status' => '状态',
            'gateway' => '支付网关',
            'tradeno' => '网关单号',
            'datetime' => '支付时间',
            'invoice_id' => '关联账单ID',
        ],
    ];

    /**
     * 后台网关记录页面
     *
     * @throws Exception
     */
    public function gateway(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/gateway.tpl')
        );
    }

    /**
     * 后台网关记录页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $paylists = Paylist::orderBy('id', 'desc')->get();

        foreach ($paylists as $paylist) {
            $paylist->status = $paylist->status();
            $paylist->datetime = Tools::toDateTime((int) $paylist->datetime);
        }

        return $response->withJson([
            'paylists' => $paylists,
        ]);
    }
}
