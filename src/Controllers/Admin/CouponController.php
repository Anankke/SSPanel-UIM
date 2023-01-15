<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserCoupon;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/*
 *  Coupon Controller
 */
final class CouponController extends BaseController
{
    public static $details = [
        'field' => [
            'op' => '操作',
            'id' => '优惠码ID',
            'code' => '优惠码',
            'type' => '优惠码类型',
            'value' => '优惠码额度',
            'product_id' => '可用商品ID',
            'use_time' => '每个用户可使用次数',
            'new_user' => '仅限新用户使用',
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
                'info' => '可用商品ID',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'use_time',
                'info' => '每个用户可使用次数',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'new_user',
                'info' => '仅限新用户使用',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'generate_method',
                'info' => '生成方式',
                'type' => 'select',
                'select' => [
                    'char' => '指定字符',
                    'random' => '随机字符（无视优惠码参数）',
                    'char_ramdom' => '指定字符+随机字符',
                ],
            ],
        ],
    ];

    /**
     * 后台优惠码页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->display('admin/coupon.tpl')
        );
    }

    /**
     * 添加优惠码
     *
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args): ResponseInterface
    {
        $code = $request->getParam('code');
        $type = $request->getParam('type');
        $value = $request->getParam('value');
        $product_id = $request->getParam('product_id');
        $use_time = $request->getParam('use_time');
        $new_user = $request->getParam('new_user');
        $generate_method = $request->getParam('generate_method');
        $expire_time = $request->getParam('expire_time');

        if ($code === '' && \in_array($generate_method, ['char', 'char_ramdom'])) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '优惠码不能为空',
            ]);
        }

        if ($type === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '优惠码类型不能为空',
            ]);
        }

        if ($value === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '优惠码额度不能为空',
            ]);
        }

        if ($expire_time < \time()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '到期时间不能小于当前时间',
            ]);
        }

        if ($generate_method === 'char') {
            if (UserCoupon::where('code', $code)->count() !== 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '优惠码已存在',
                ]);
            }
        }

        if ($generate_method === 'char_ramdom') {
            $code .= Tools::genRandomChar(8);

            if (UserCoupon::where('code', $code)->count() === 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '出现了一些问题，请稍后重试',
                ]);
            }
        }

        if ($generate_method === 'ramdom') {
            $code = Tools::genRandomChar(8);

            if (UserCoupon::where('code', $code)->count() === 0) {
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
            'new_user' => $new_user,
        ];

        $coupon = new UserCoupon();
        $coupon->code = $code;
        $coupon->content = \json_encode($content);
        $coupon->limit = \json_encode($limit);
        $coupon->create_time = \time();
        $coupon->expire_time = $expire_time;
        $coupon->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '优惠码 <code>' . $code . '</code> 添加成功',
        ]);
    }

    public function delete(Request $request, Response $response, array $args): ResponseInterface
    {
        $coupon_id = $args['id'];
        UserCoupon::find($coupon_id)->delete();
        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    /**
     * 后台商品优惠码页面 AJAX
     *
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $coupons = UserCoupon::orderBy('id', 'desc')->get();
        foreach ($coupons as $coupon) {
            $content = \json_decode($coupon->content);
            $limit = \json_decode($coupon->limit);
            $coupon->op = '<button type="button" class="btn btn-red" id="delete-coupon-' . $coupon->id . '" 
        onclick="deleteCoupons(' . $coupon->id . ')">删除</button>';
            $coupon->type = Tools::getCouponType($content);
            $coupon->value = $content->value;
            $coupon->product_id = $limit->product_id;
            $coupon->use_time = $limit->use_time;
            $coupon->new_user = $limit->new_user;
            $coupon->create_time = Tools::toDateTime((int) $coupon->create_time);
            $coupon->expire_time = Tools::toDateTime((int) $coupon->expire_time);
        }
        return $response->withJson([
            'coupons' => $coupons,
        ]);
    }
}
