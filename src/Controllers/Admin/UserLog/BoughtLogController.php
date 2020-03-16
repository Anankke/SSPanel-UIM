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
            'auto_renew' => '自动续费时间',
            'reset_time' => '流量重置时间',
            'buy_time'   => '套餐购买时间',
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
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $id           = $args['id'];
        $user         = User::find($id);
        $boughts      = Bought::where('userid', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = Bought::where('userid', $user->id)->count();
        $data         = [];
        foreach ($boughts as $bought) {
            $shop = $bought->shop();
            if ($shop == null) {
                $bought->delete();
                continue;
            }
            $tempdata = [];
            $tempdata['op']          = '<a class="btn btn-brand-accent" id="delete" href="javascript:void(0);" onClick="delete_modal_show(\'' . $bought->id . '\')">删除</a>';
            $tempdata['id']          = $bought->id;
            $tempdata['name']        = $shop->name;
            $tempdata['content']     = $shop->content();
            $tempdata['auto_renew']  = ($bought->renew == 0 ? '不自动续费' : $bought->renew_date());
            $tempdata['buy_time']    = $bought->datetime();
            if ($bought->use_loop()) {
                $tempdata['valid'] = ($bought->valid() ? '有效' : '已过期');
            } else {
                $tempdata['valid'] = '-';
            }
            $tempdata['reset_time']  = $bought->reset_time();
            $tempdata['exp_time']    = $bought->exp_time();
            $data[] = $tempdata;
        }
        $info = [
            'draw'              => $request->getParam('draw'),
            'recordsTotal'      => $total_conut,
            'recordsFiltered'   => $total_conut,
            'data'              => $data
        ];

        return $response->write(
            json_encode($info)
        );
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
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';

        return $response->write(
            json_encode($rs)
        );
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
            $rs['ret'] = 0;
            $rs['msg'] = '请选择套餐';
            return $response->getBody()->write(json_encode($rs));
        }
        $shop = Shop::find($shop_id);
        if ($shop == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '套餐不存在';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($buy_type != 0) {
            if (bccomp($user->money, $shop->price, 2) == -1) {
                $res['ret'] = 0;
                $res['msg'] = '喵喵喵~ 该用户余额不足。';
                return $response->getBody()->write(json_encode($res));
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
        $rs['msg']        = ($buy_type != 0 ? '套餐购买成功' : '套餐添加成功');
        $rs['ret']        = 1;

        return $response->write(
            json_encode($rs)
        );
    }
}
