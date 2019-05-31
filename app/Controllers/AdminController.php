<?php

namespace App\Controllers;

use App\Models\Node;
use App\Models\Coupon;
use App\Models\User;
use App\Services\Gateway\ChenPay;
use App\Utils\AliPay;
use App\Utils\Tools;
use App\Services\Analytics;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

/**
 *  Admin Controller
 */
class AdminController extends UserController
{
    public function index($request, $response, $args)
    {
        $sts = new Analytics();
        return $this->view()->assign('sts', $sts)->display('admin/index.tpl');
    }

    public function node($request, $response, $args)
    {
        $nodes = Node::all();
        return $this->view()->assign('nodes', $nodes)->display('admin/node.tpl');
    }


    public function editConfig($request, $response, $args)
    {
        return (new ChenPay())->editConfig();
    }

    public function saveConfig($request, $response, $args)
    {
        return (new ChenPay())->saveConfig($request);
    }

    public function sys()
    {
        return $this->view()->display('admin/index.tpl');
    }

    public function invite($request, $response, $args)
    {
        $table_config['total_column'] = array('id' => 'ID',
            'total' => '原始金额', 'event_user_id' => '发起用户ID',
            'event_user_name' => '发起用户名', 'ref_user_id' => '获利用户ID',
            'ref_user_name' => '获利用户名', 'ref_get' => '获利金额',
            'datetime' => '时间');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'payback/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/invite.tpl');
    }

    public function addInvite($request, $response, $args)
    {
        $num = $request->getParam('num');
        $prefix = $request->getParam('prefix');

        if (Tools::isInt($num) == false) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        if ($request->getParam('uid') != '0') {
            if (strpos($request->getParam('uid'), '@') != false) {
                $user = User::where('email', '=', $request->getParam('uid'))->first();
            } else {
                $user = User::Where('id', '=', $request->getParam('uid'))->first();
            }

            if ($user == null) {
                $res['ret'] = 0;
                $res['msg'] = '邀请次数添加失败，检查用户id或者用户邮箱是否输入正确';
                return $response->getBody()->write(json_encode($res));
            }
            $uid = $user->id;
        } else {
            $uid = 0;
        }
        $user->invite_num += $num;
        $user->save();
        $res['ret'] = 1;
        $res['msg'] = '邀请次数添加成功';
        return $response->getBody()->write(json_encode($res));
    }


    public function coupon($request, $response, $args)
    {
        $table_config['total_column'] = array('id' => 'ID', 'code' => '优惠码',
            'expire' => '过期时间', 'shop' => '限定商品ID',
            'credit' => '额度', 'onetime' => '次数');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'coupon/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/coupon.tpl');
    }

    public function addCoupon($request, $response, $args)
    {
        $code = new Coupon();
        $code->onetime = $request->getParam('onetime');
        $generate_type = $request->getParam('generate_type');
        $final_code = $request->getParam('prefix');

        if (empty($final_code) && ($generate_type == 1 || $generate_type == 3)) {
            $res['ret'] = 0;
            $res['msg'] = '优惠码不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        if ($generate_type == 1) {
            if (Coupon::where('code', $final_code)->count() != 0) {
                $res['ret'] = 0;
                $res['msg'] = '优惠码已存在';
                return $response->getBody()->write(json_encode($res));
            }
        } else {
            while (true) {
                if ($generate_type == 2) {
                    $temp_code = Tools::genRandomChar(8);
                } elseif ($generate_type == 3) {
                    $temp_code = $final_code . Tools::genRandomChar(8);
                }

                if (Coupon::where('code', $temp_code)->count() == 0) {
                    $final_code = $temp_code;
                    break;
                }
            }
        }

        $code->code = $final_code;
        $code->expire = time() + $request->getParam('expire') * 3600;
        $code->shop = $request->getParam('shop');
        $code->credit = $request->getParam('credit');

        $code->save();

        $res['ret'] = 1;
        $res['msg'] = '优惠码添加成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function trafficLog($request, $response, $args)
    {
        $table_config['total_column'] = array('id' => 'ID', 'user_id' => '用户ID',
            'user_name' => '用户名', 'node_name' => '使用节点',
            'rate' => '倍率', 'origin_traffic' => '实际使用流量',
            'traffic' => '结算流量',
            'log_time' => '记录时间');
        $table_config['default_show_column'] = array('id', 'user_id',
            'user_name', 'node_name',
            'rate', 'origin_traffic',
            'traffic', 'log_time');
        $table_config['ajax_url'] = 'trafficlog/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/trafficlog.tpl');
    }

    public function ajax_trafficLog($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select log.id,log.user_id,user.user_name,node.name as node_name,log.rate,(log.u + log.d) as origin_traffic,log.traffic,log.log_time from user_traffic_log as log,user,ss_node as node WHERE log.user_id = user.id AND log.node_id = node.id');

        $datatables->edit('log_time', static function ($data) {
            return date('Y-m-d H:i:s', $data['log_time']);
        });

        $datatables->edit('origin_traffic', static function ($data) {
            return Tools::flowAutoShow($data['origin_traffic']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }

    public function ajax_payback($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select payback.id,payback.total,payback.userid as event_user_id,event_user.user_name as event_user_name,payback.ref_by as ref_user_id,ref_user.user_name as ref_user_name,payback.ref_get,payback.datetime from payback,user as event_user,user as ref_user where event_user.id = payback.userid and ref_user.id = payback.ref_by');

        $datatables->edit('datetime', static function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }

    public function ajax_coupon($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select id,code,expire,shop,credit,onetime from coupon');

        $datatables->edit('expire', static function ($data) {
            return date('Y-m-d H:i:s', $data['expire']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
