<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Services\DynamicRate;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function array_fill;
use function count;
use function json_decode;
use function json_encode;

final class RateController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $nodes = Subscribe::getUserNodes($this->user);
        $node_list = [];

        foreach ($nodes as $node) {
            $node_list[] = [
                'id' => $node->id,
                'name' => $node->name,
            ];
        }

        if (count($node_list) === 0) {
            $node_list[] = [
                'id' => 0,
                'name' => '暂无节点',
            ];
        }

        return $response->write(
            $this->view()
                ->assign('node_list', $node_list)
                ->fetch('user/rate.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $nodes = Subscribe::getUserNodes($this->user);
        $node = $nodes->find($request->getParam('node_id'));

        if ($node === null) {
            return ResponseHelper::error($response, '节点不存在');
        }

        if ($node->is_dynamic_rate) {
            $dynamic_rate_config = json_decode($node->dynamic_rate_config);

            $dynamic_rate_type = match ($node->dynamic_rate_type) {
                1 => 'linear',
                default => 'logistic',
            };

            $rates = DynamicRate::getFullDayRates(
                (float) $dynamic_rate_config?->max_rate,
                (int) $dynamic_rate_config?->max_rate_time,
                (float) $dynamic_rate_config?->min_rate,
                (int) $dynamic_rate_config?->min_rate_time,
                $dynamic_rate_type
            );
        } else {
            $rates = array_fill(0, 24, $node->traffic_rate);
        }

        $event = json_encode([
            'drawChart' => [
                'msg' => $node->name,
                'data' => $rates,
            ],
        ]);

        return $response->withHeader('HX-Trigger', $event)->withJson([
            'ret' => 1,
        ]);
    }
}
