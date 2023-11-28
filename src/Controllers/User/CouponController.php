<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserCoupon;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function explode;
use function in_array;
use function json_decode;
use function time;

final class CouponController extends BaseController
{
    public function check(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $coupon_raw = $this->antiXss->xss_clean($request->getParam('coupon'));
        $product_id = $this->antiXss->xss_clean($request->getParam('product_id'));
        $invalid_coupon_msg = '优惠码无效';

        if ($coupon_raw === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        $coupon = (new UserCoupon())->where('code', $coupon_raw)->first();

        if ($coupon === null || ($coupon->expire_time !== 0 && $coupon->expire_time < time())) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        $product = (new Product())->where('id', $product_id)->first();

        if ($product === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        $limit = json_decode($coupon->limit);

        if ($limit->disabled) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        if ($limit->product_id !== '' && ! in_array($product_id, explode(',', $limit->product_id))) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        $user = $this->user;
        $use_limit = $limit->use_time;

        if ($use_limit > 0) {
            $user_use_count = (new Order())->where('user_id', $user->id)->where('coupon', $coupon->code)->count();
            if ($user_use_count >= $use_limit) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => $invalid_coupon_msg,
                ]);
            }
        }

        $total_use_limit = $limit->total_use_time;

        if ($total_use_limit > 0 && $coupon->use_count >= $total_use_limit) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $invalid_coupon_msg,
            ]);
        }

        $content = json_decode($coupon->content);

        if ($content->type === 'percentage') {
            $discount = $product->price * $content->value / 100;
        } else {
            $discount = $content->value;
        }

        $buy_price = $product->price - $discount;

        return $response->withJson([
            'ret' => 1,
            'msg' => '优惠码可用',
            'discount' => $discount,
            'buy_price' => $buy_price,
        ]);
    }
}
