<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Bought;
use App\Models\Coupon;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\Shop;
use App\Utils\ResponseHelper;
use Slim\Http\Request;
use Slim\Http\Response;

final class ShopController extends BaseController
{
    /**
     * @param array     $args
     */
    public function shop(Request $request, Response $response, array $args)
    {
        $shops = Shop::where('status', 1)->orderBy('name')->get();
        return $this->view()->assign('shops', $shops)->display('user/shop.tpl');
    }

    /**
     * @param array     $args
     */
    public function couponCheck(Request $request, Response $response, array $args)
    {
        $coupon = $request->getParam('coupon');
        $coupon = trim($coupon);

        $user = $this->user;
        $shop = $request->getParam('shop');

        $shop = Shop::where('id', $shop)->where('status', 1)->first();

        if ($shop === null) {
            return ResponseHelper::error($response, '非法请求');
        }

        if ($coupon === '') {
            return $response->withJson([
                'ret' => 1,
                'name' => $shop->name,
                'credit' => '0 %',
                'total' => $shop->price . '元',
            ]);
        }

        $coupon = Coupon::where('code', $coupon)->first();

        if ($coupon === null) {
            return ResponseHelper::error($response, '优惠码无效');
        }

        if ($coupon->order($shop->id) === false) {
            return ResponseHelper::error($response, '此优惠码不可用于此商品');
        }

        $use_limit = $coupon->onetime;
        if ($use_limit > 0) {
            $use_count = Bought::where('userid', $user->id)->where('coupon', $coupon->code)->count();
            if ($use_count >= $use_limit) {
                return ResponseHelper::error($response, '优惠码次数已用完');
            }
        }

        return $response->withJson([
            'ret' => 1,
            'name' => $shop->name,
            'credit' => $coupon->credit . ' %',
            'total' => $shop->price * ((100 - $coupon->credit) / 100) . '元',
        ]);
    }

    /**
     * @param array     $args
     */
    public function buy(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $shop = $request->getParam('shop');
        $coupon = trim($request->getParam('coupon'));
        $autorenew = $request->getParam('autorenew');
        $disableothers = $request->getParam('disableothers');
        $coupon_code = $coupon;

        $shop = Shop::where('id', $shop)
            ->where('status', 1)
            ->first();

        if ($shop === null) {
            return ResponseHelper::error($response, '商品不存在或已下架');
        }

        $orders = Bought::where('userid', $user->id)->get();
        foreach ($orders as $order) {
            if ($order->shop()->useLoop()) {
                if ($order->valid()) {
                    return ResponseHelper::error($response, '您购买的含有自动重置系统的套餐还未过期，无法购买新套餐');
                }
            }
        }

        if ($coupon === '') {
            $credit = 0;
        } else {
            $coupon = Coupon::where('code', $coupon)->first();
            if ($coupon === null) {
                return ResponseHelper::error($response, '此优惠码不存在');
            }
            $credit = $coupon->credit;
            if ($coupon->order($shop->id) === false) {
                return ResponseHelper::error($response, '此优惠码不适用于此商品');
            }
            if ($coupon->expire < time()) {
                return ResponseHelper::error($response, '此优惠码已过期');
            }
            if ($coupon->onetime > 0) {
                $use_count = Bought::where('userid', $user->id)
                    ->where('coupon', $coupon->code)
                    ->count();
                if ($use_count >= $coupon->onetime) {
                    return ResponseHelper::error($response, '此优惠码使用次数已达上限');
                }
            }
        }

        $price = $shop->price * (100 - $credit) / 100;
        if (bccomp((string) $user->money, (string) $price, 2) === -1) {
            return ResponseHelper::error($response, '账户余额不足，请先充值');
        }
        $user->money = bcsub((string) $user->money, (string) $price, 2);
        $user->save();

        if ($disableothers === 1) {
            $boughts = Bought::where('userid', $user->id)->get();
            foreach ($boughts as $disable_bought) {
                $disable_bought->renew = 0;
                $disable_bought->save();
            }
        }

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        if ($autorenew === 0 || $shop->auto_renew === 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }
        $bought->coupon = $coupon_code;
        $bought->price = $price;
        $bought->save();
        $shop->buy($user);

        // 返利
        if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_purchase') {
            Payback::rebate($user->id, $price);
        }

        return ResponseHelper::successfully($response, '购买成功');
    }

    /**
     * @param array     $args
     */
    public function buyTrafficPackage(Request $request, Response $response, array $args)
    {
        $user = $this->user;
        $shop = $request->getParam('shop');
        $shop = Shop::where('id', $shop)->where('status', 1)->first();
        $price = $shop->price;

        if ($shop === null || $shop->trafficPackage() === 0) {
            return ResponseHelper::error($response, '非法请求');
        }

        if ($user->class < $shop->content['traffic_package']['class']['min'] || $user->class > $shop->content['traffic_package']['class']['max']) {
            return ResponseHelper::error($response, '您当前的会员等级无法购买此流量包');
        }

        if (! $user->isLogin) {
            return $response->withJson([ 'ret' => -1 ]);
        }

        if (bccomp((string) $user->money, (string) $price, 2) === -1) {
            return ResponseHelper::error($response, '喵喵喵~ 当前余额不足，总价为'
                . $price . '元。</br><a href="/user/code">点击进入充值界面</a>');
        }

        $user->money = bcsub((string) $user->money, (string) $price, 2);
        $user->save();

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        $bought->renew = 0;
        $bought->coupon = 0;
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);

        // 返利
        if ($user->ref_by > 0 && Setting::obtain('invitation_mode') === 'after_purchase') {
            Payback::rebate($user->id, $price);
        }

        return ResponseHelper::successfully($response, '购买成功');
    }
}
