<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    Shop,
    Bought
};
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class BoughtLogController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function bought($request, $response, $args): ResponseInterface
    {
        $id = $args['id'];
        $user = User::find($id);
        $table_config['total_column'] = array(
            'op'         => '操作',
            'id'         => 'ID',
            'name'       => '商品名称',
            'valid'      => '是否有效期内',
            'renew'      => '自动续费时间',
            'reset_time' => '流量重置时间',
            'datetime'   => '套餐购买时间',
            'exp_time'   => '套餐过期时间',
            'content'    => '商品详细内容',
        );
        $table_config['default_show_column'] = array('op', 'name', 'valid', 'reset_time');
        $table_config['ajax_url'] = 'bought/ajax';
        $shops = Shop::where('status', 1)->orderBy('name')->get();

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('shops', $shops)
                ->assign('user', $user)
                ->display('admin/user/bought.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function bought_ajax($request, $response, $args): ResponseInterface
    {
        $user  = User::find($args['id']);
        $query = Bought::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['op', 'reset_time', 'valid', 'exp_time'])) {
                    $order_field = 'id';
                }
                if (in_array($order_field, ['content', 'name'])) {
                    $order_field = 'shopid';
                }
            },
            static function ($query) use ($user) {
                $query->where('userid', $user->id);
            }
        );

        $data  = [];
        foreach ($query['datas'] as $value) {
            /** @var Bought $value */

            if ($value->shop() == null) {
                Bought::shop_is_null($value);
                continue;
            }
            $tempdata                = [];
            $tempdata['op']          = '<a class="btn btn-brand-accent" id="delete" href="javascript:void(0);" onClick="delete_modal_show(\'' . $value->id . '\')">删除</a>';
            $tempdata['id']          = $value->id;
            $tempdata['name']        = $value->shop()->name;
            $tempdata['content']     = $value->content();
            $tempdata['renew']       = $value->renew();
            $tempdata['datetime']    = $value->datetime();
            if ($value->shop()->use_loop()) {
                $tempdata['valid'] = ($value->valid() ? '有效' : '已过期');
            } else {
                $tempdata['valid'] = '-';
            }
            $tempdata['reset_time']  = $value->reset_time();
            $tempdata['exp_time']    = $value->exp_time();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => Bought::where('userid', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function bought_delete($request, $response, $args): ResponseInterface
    {
        $id = $request->getParam('id');
        $Bought = Bought::find($id);
        if (!$Bought->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败'
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function bought_add($request, $response, $args): ResponseInterface
    {
        $id = $args['id'];
        $user = User::find($id);
        $shop_id  = (int) $request->getParam('buy_shop');
        $buy_type = (int) $request->getParam('buy_type');
        if ($shop_id == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请选择套餐'
            ]);
        }
        $shop = Shop::find($shop_id);
        if ($shop == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '套餐不存在'
            ]);
        }
        if ($buy_type != 0) {
            if (bccomp($user->money, $shop->price, 2) == -1) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '喵喵喵~ 该用户余额不足。'
                ]);
            }
            $user->money = bcsub($user->money, $shop->price, 2);
            $user->save();
        }
        $boughts = Bought::where('userid', $user->id)->get();
        foreach ($boughts as $disable_bought) {
            $disable_bought->renew = 0;
            $disable_bought->save();
        }
        $bought           = new Bought();
        $bought->userid   = $user->id;
        $bought->shopid   = $shop->id;
        $bought->datetime = time();
        $bought->renew    = 0;
        $bought->coupon   = '';
        $bought->price    = $shop->price;
        $bought->save();
        $shop->buy($user);

        return $response->withJson([
            'ret' => 1,
            'msg' => ($buy_type != 0 ? '套餐购买成功' : '套餐添加成功')
        ]);
    }
}
