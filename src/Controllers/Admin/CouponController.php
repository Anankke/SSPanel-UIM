<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserCoupon;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function in_array;
use function json_decode;
use function json_encode;
use function property_exists;
use function time;

final class CouponController extends BaseController
{
    private static array $details = [
        'field' => [
            'op' => '操作',
            'id' => 'ID',
            'code' => '优惠码',
            'type' => '类型',
            'value' => '额度',
            'product_id' => '可用商品ID',
            'use_time' => '使用次数（每用户）',
            'total_use_time' => '使用次数（累计）',
            'new_user' => '仅限新用户使用',
            'disabled' => '已禁用',
            'use_count' => '总使用次数',
            'create_time' => '创建时间',
            'expire_time' => '过期时间',
        ],
        'create_dialog' => [
            [
                'id' => 'code',
                'info' => '优惠码',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'type',
                'info' => '优惠码类型',
                'type' => 'select',
                'select' => [
                    'percentage' => '百分比',
                    'fixed' => '固定金额',
                ],
            ],
            [
                'id' => 'value',
                'info' => '优惠码额度',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'product_id',
                'info' => '可用商品ID（多个ID以英文半角逗号分隔）',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'use_time',
                'info' => '每个用户可使用次数限制（小于0为不限）',
                'type' => 'input',
                'placeholder' => '-1',
            ],
            [
                'id' => 'total_use_time',
                'info' => '累计可使用次数限制（小于0为不限）',
                'type' => 'input',
                'placeholder' => '-1',
            ],
            [
                'id' => 'new_user',
                'info' => '仅限新用户使用',
                'type' => 'select',
                'select' => [
                    '1' => '启用',
                    '0' => '禁用',
                ],
            ],
            [
                'id' => 'generate_method',
                'info' => '生成方式',
                'type' => 'select',
                'select' => [
                    'char' => '指定字符',
                    'random' => '随机字符（无视优惠码参数）',
                    'char_random' => '指定字符+随机字符',
                ],
            ],
        ],
    ];

    /**
     * 后台优惠码页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/coupon.tpl')
        );
    }

    /**
     * 添加优惠码
     */
    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $code = $request->getParam('code');
        $type = $request->getParam('type');
        $value = $request->getParam('value');
        $product_id = $request->getParam('product_id');
        $use_time = $request->getParam('use_time');
        $total_use_time = $request->getParam('total_use_time');
        $new_user = $request->getParam('new_user');
        $generate_method = $request->getParam('generate_method');
        $expire_time = $request->getParam('expire_time');

        if ($code === '' && in_array($generate_method, ['char', 'char_ramdom'])) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '优惠码不能为空',
            ]);
        }

        if ($type === '' || $value === '' || ($expire_time !== '' && $expire_time < time())) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '无效的优惠码参数',
            ]);
        }

        if ($generate_method === 'char' && (new UserCoupon())->where('code', $code)->count() !== 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '优惠码已存在',
            ]);
        }

        if ($generate_method === 'char_random') {
            $code .= Tools::genRandomChar();

            if ((new UserCoupon())->where('code', $code)->count() !== 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '出现了一些问题，请稍后重试',
                ]);
            }
        }

        if ($generate_method === 'random') {
            $code = Tools::genRandomChar();

            if ((new UserCoupon())->where('code', $code)->count() !== 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '出现了一些问题，请稍后重试',
                ]);
            }
        }

        $content = [
            'type' => $type,
            'value' => $value,
        ];

        $limit = [
            'product_id' => $product_id,
            'use_time' => $use_time,
            'total_use_time' => $total_use_time,
            'new_user' => $new_user,
            'disabled' => 0,
        ];

        $coupon = new UserCoupon();
        $coupon->code = $code;
        $coupon->content = json_encode($content);
        $coupon->limit = json_encode($limit);
        $coupon->create_time = time();

        if ($expire_time !== '') {
            $coupon->expire_time = $expire_time;
        } else {
            $coupon->expire_time = 0;
        }

        $coupon->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '优惠码 ' . $code . ' 添加成功',
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $coupon_id = $args['id'];
        (new UserCoupon())->find($coupon_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function disable(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $coupon_id = $args['id'];
        $coupon = (new UserCoupon())->find($coupon_id)->first();
        $limit = json_decode($coupon->limit);
        $limit->disabled = 1;
        $coupon->limit = json_encode($limit);
        $coupon->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '禁用成功',
        ]);
    }

    /**
     * 后台商品优惠码页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $coupons = (new UserCoupon())->orderBy('id', 'desc')->get();

        foreach ($coupons as $coupon) {
            $content = json_decode($coupon->content);
            $limit = json_decode($coupon->limit);

            $coupon->op = '<button type="button" class="btn btn-red" id="delete-coupon-' . $coupon->id . '"
                onclick="deleteCoupon(' . $coupon->id . ')">删除</button>' .
                ($limit->disabled !== 1 ? '
                <button type="button" class="btn btn-orange" id="disable-coupon-' .
                    $coupon->id . '" onclick="disableCoupon(' . $coupon->id . ')">禁用</button>' : '');

            $coupon->type = $coupon->type();
            $coupon->value = $content->value;
            $coupon->product_id = $limit->product_id;
            $coupon->use_time = (int) $limit->use_time < 0 ? '不限次数' : $limit->use_time;
            $coupon->total_use_time = ! property_exists($limit, 'total_use_time') ||
            (int) $limit->total_use_time < 0 ? '不限次数' : $limit->total_use_time;
            $coupon->new_user = $limit->new_user === 1 ? '是' : '否';
            $coupon->disabled = $limit->disabled === 1 ? '是' : '否';
            $coupon->create_time = Tools::toDateTime((int) $coupon->create_time);
            $coupon->expire_time = $coupon->expire_time === 0 ? '永久有效' : Tools::toDateTime((int) $coupon->expire_time);
        }

        return $response->withJson([
            'coupons' => $coupons,
        ]);
    }
}
