<?php
namespace App\Controllers\Admin;

use App\Services\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Utils\Tools;
use App\Services\Mail;
use App\Models\Setting;
use App\Models\GiftCard;
use App\Controllers\AdminController;

class GiftCardController extends AdminController
{
    public static function page(){
        $details = [
            'route' => 'giftcard',
            'title' => [
                'title' => '礼品卡',
                'subtitle' => '生成和管理礼品卡。表格仅展示最近 500 条记录',
            ],
            'field' => [
                'id' => '#',
                'card' => '礼品卡',
                'balance' => '面值',
                'created_at' => '创建时间',
                'status' => '使用状态',
                'used_at' => '使用时间',
                'use_user' => '使用用户'
            ],
            'search_dialog' => [
                [
                    'id' => 'card',
                    'info' => '礼品卡',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'balance',
                    'info' => '面值',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'use_user',
                    'info' => '使用用户',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'status',
                    'info' => '使用状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '未使用',
                        '1' => '已使用',
                    ],
                    'exact' => true,
                ],
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
                    ]
                ]
            ]
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = GiftCard::orderBy('id', 'desc')
        ->limit(500)
        ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/giftcard.tpl')
        );
    }

    public function add($request, $response, $args)
    {
        $cards = [];
        $user = Auth::getUser();
        $card_number = $request->getParam('card_number');
        $card_value = $request->getParam('card_value');
        $card_length = $request->getParam('card_length');

        try {
            if (empty($card_number) || $card_number < 0) {
                throw new \Exception('生成数量应该是一个正整数');
            }
            if (empty($card_value) || $card_value < 0) {
                throw new \Exception('礼品卡面值应该大于零');
            }

            for ($i = 0; $i < $card_number; $i++) {
                $card = strtolower(Tools::genRandomChar($card_length));
                array_push($cards, $card);
                // save to database
                $giftcard = new GiftCard;
                $giftcard->card = $card;
                $giftcard->balance = $card_value * 100;
                $giftcard->created_at = time();
                $giftcard->status = 0;
                $giftcard->used_at = 0;
                $giftcard->use_user = 0;
                $giftcard->save();
            }

            if (Setting::obtain('mail_driver') != 'none') {
                Mail::send($user->email, $_ENV['appName'] . '- 充值码', 'giftcard.tpl',
                    [
                        'text' => implode('<br/>', $cards)
                    ], []
                );
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功'
        ]);
    }

    public function ajaxQuery($request, $response, $args)
    {
        $condition = [];
        $details = self::page();
        foreach ($details['search_dialog'] as $from)
        {
            $field = $from['id'];
            $keyword = $request->getParam($field);
            if (!empty($keyword) && $field == 'balance') {
                $keyword = $keyword * 100;
            }
            if ($from['type'] == 'input') {
                if ($from['exact']) {
                    ($keyword != '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword != '') && array_push($condition, [$field, 'like', '%'.$keyword.'%']);
                }
            }
            if ($from['type'] == 'select') {
                ($keyword != 'all') && array_push($condition, [$field, '=', $keyword]);
            }
        }

        $results = GiftCard::orderBy('id', 'desc')
        ->where($condition)
        ->limit(500)
        ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        GiftCard::find($item_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }
}
