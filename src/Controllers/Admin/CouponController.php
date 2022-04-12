<?php
namespace App\Controllers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Coupon;
use App\Controllers\AdminController;

class CouponController extends AdminController
{
    public function index($request, $response, $args)
    {
        $coupons = Coupon::all();

        return $response->write(
            $this->view()
                ->assign('coupons', $coupons)
                ->display('admin/coupon.tpl')
        );
    }

    public function get($request, $response, $args)
    {
        $coupon_id = $args['id'];
        $coupon = Coupon::find($coupon_id);

        return $response->withJson([
            'ret' => 1,
            'data' => $coupon
        ]);
    }

    public function save($request, $response, $args)
    {
        $coupon = $request->getParam('coupon');
        $discount = $request->getParam('discount');
        $time_limit = $request->getParam('time_limit');
        $user_limit = $request->getParam('user_limit');
        $total_limit = $request->getParam('total_limit');
        $product_limit = $request->getParam('product_limit');
        $count = Coupon::where('coupon', $coupon)->count();

        try {
            if ($coupon == '') {
                throw new \Exception('请填写优惠码');
            }
            if ($count != '0') {
                throw new \Exception('存在同名优惠码');
            }
            if (trim($coupon) != $coupon) {
                throw new \Exception('优惠码首尾不能有空格');
            }
            if ($discount == '') {
                throw new \Exception('请填写优惠额度');
            }
            if ($discount > '1' || $discount < '0') {
                throw new \Exception('优惠额度需要介于0与1之间');
            }
            if ($time_limit == '') {
                throw new \Exception('请填写时间限制');
            }
            if ($product_limit == '') {
                throw new \Exception('请填写商品限制');
            }
            if ($user_limit == '') {
                throw new \Exception('请填写单用户使用次数限制');
            }
            if ($total_limit == '') {
                throw new \Exception('请填写所有用户使用次数限制');
            }
            if ($user_limit > $total_limit) {
                throw new \Exception('单用户使用次数限制不能大于所有用户使用次数限制');
            }

            $new_coupon = new Coupon;
            $new_coupon->coupon = $coupon;
            $new_coupon->discount = $discount;
            $new_coupon->product_limit = $product_limit;
            $new_coupon->time_limit = $time_limit;
            $new_coupon->user_limit = (int) $user_limit;
            $new_coupon->total_limit = (int) $total_limit;
            $new_coupon->use_count = 0;
            $new_coupon->amount_count = 0;
            $new_coupon->created_at = time();
            $new_coupon->updated_at = time();
            $new_coupon->expired_at = time() + ($time_limit * 3600);
            $new_coupon->save();
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

    public function update($request, $response, $args)
    {
        $coupon_id = $args['id'];
        $coupon = $request->getParam('coupon');
        $discount = $request->getParam('discount');
        $user_limit = $request->getParam('user_limit');
        $total_limit = $request->getParam('total_limit');
        $product_limit = $request->getParam('product_limit');

        try {
            if ($coupon == '') {
                throw new \Exception('请填写优惠码');
            }
            if (trim($coupon) != $coupon) {
                throw new \Exception('优惠码首尾不能有空格');
            }
            if ($discount == '') {
                throw new \Exception('请填写优惠额度');
            }
            if ($discount > '1' || $discount < '0') {
                throw new \Exception('优惠额度需要介于0与1之间');
            }
            if ($product_limit == '') {
                throw new \Exception('请填写商品限制');
            }
            if ($user_limit == '') {
                throw new \Exception('请填写单用户使用次数限制');
            }
            if ($total_limit == '') {
                throw new \Exception('请填写所有用户使用次数限制');
            }
            if ($user_limit > $total_limit) {
                throw new \Exception('单用户使用次数限制不能大于所有用户使用次数限制');
            }

            $this_coupon = Coupon::find($coupon_id);
            $this_coupon->coupon = $coupon;
            $this_coupon->discount = $discount;
            $this_coupon->product_limit = $product_limit;
            $this_coupon->user_limit = (int) $user_limit;
            $this_coupon->total_limit = (int) $total_limit;
            $this_coupon->updated_at = time();
            $this_coupon->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '更新成功'
        ]);
    }

    public function delete($request, $response, $args)
    {
        $coupon_id = $args['id'];
        Coupon::find($coupon_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }
}
