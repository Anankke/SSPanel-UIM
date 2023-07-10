<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GiftCard;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function time;

final class GiftCardController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '礼品卡ID',
            'card' => '卡号',
            'balance' => '面值',
            'create_time' => '创建时间',
            'status' => '使用状态',
            'use_time' => '使用时间',
            'use_user' => '使用用户',
        ],
        'create_dialog' => [
            [
                'id' => 'card_number',
                'info' => '创建数量',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'card_value',
                'info' => '礼品卡面值',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'card_length',
                'info' => '礼品卡长度',
                'type' => 'select',
                'select' => [
                    '12' => '12位',
                    '18' => '18位',
                    '24' => '24位',
                    '30' => '30位',
                    '36' => '36位',
                ],
            ],
        ],
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/giftcard.tpl')
        );
    }

    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $card_number = $request->getParam('card_number') ?? 0;
        $card_value = $request->getParam('card_value') ?? 0;
        $card_length = $request->getParam('card_length') ?? 0;
        $card_added = '';

        if ($card_number === '' || $card_number <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '生成数量不能为空或小于0',
            ]);
        }

        if ($card_value === '' || $card_value <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡面值不能为空或小于0',
            ]);
        }

        if ($card_length === '' || $card_length <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡长度不能为空或小于0',
            ]);
        }

        for ($i = 0; $i < $card_number; $i++) {
            $card = Tools::genRandomChar($card_length);
            // save to database
            $giftcard = new GiftCard();
            $giftcard->card = $card;
            $giftcard->balance = $card_value;
            $giftcard->create_time = time();
            $giftcard->status = 0;
            $giftcard->use_time = 0;
            $giftcard->use_user = 0;
            $giftcard->save();
            $card_added .= $card . PHP_EOL;
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功' . PHP_EOL . $card_added,
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $card_id = $args['id'];
        GiftCard::find($card_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $giftcards = GiftCard::orderBy('id', 'desc')->get();

        foreach ($giftcards as $giftcard) {
            $giftcard->op = '<button type="button" class="btn btn-red" id="delete-gift-card-' . $giftcard->id . '" 
        onclick="deleteGiftCard(' . $giftcard->id . ')">删除</button>';
            $giftcard->status = $giftcard->status();
            $giftcard->create_time = Tools::toDateTime((int) $giftcard->create_time);
            $giftcard->use_time = Tools::toDateTime((int) $giftcard->use_time);
        }

        return $response->withJson([
            'giftcards' => $giftcards,
        ]);
    }
}
