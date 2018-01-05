<?php

namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Ip;
use App\Models\RadiusBan;
use App\Models\Relay;
use App\Controllers\AdminController;
use App\Utils\Hash;
use App\Utils\Radius;
use App\Utils\QQWry;
use App\Utils\Wecenter;
use App\Utils\Tools;

class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array("op" => "操作", "id" => "ID", "user_name" => "用户名",
                            "remark" => "备注", "email" => "邮箱", "money" => "金钱",
                            "im_type" => "联络方式类型", "im_value" => "联络方式详情",
                            "node_group" => "群组", "account_expire_in" => "账户过期时间",
                            "class" => "等级", "class_expire" => "等级过期时间",
                            "passwd" => "连接密码","port" => "连接端口", "method" => "加密方式",
                            "protocol" => "连接协议", "obfs" => "连接混淆方式",
                            "online_ip_count" => "在线IP数", "last_ss_time" => "上次使用时间",
                            "used_traffic" => "已用流量/GB", "enable_traffic" => "总流量/GB",
                            "last_checkin_time" => "上次签到时间", "today_traffic" => "今日流量/MB",
                            "is_enable" => "是否启用", "reg_date" => "注册时间",
                            "reg_location" => "注册IP", "auto_reset_day" => "自动重置流量日",
                            "auto_reset_bandwidth" => "自动重置流量/GB", "ref_by" => "邀请人ID", "ref_by_user_name" => "邀请人用户名");
        $table_config['default_show_column'] = array("op", "id", "user_name", "remark", "email");
        $table_config['ajax_url'] = 'user/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/user/index.tpl');
    }

    public function search($request, $response, $args)
    {
        $pageNum = 1;
        $text=$args["text"];
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }

        $users = User::where("email", "LIKE", "%".$text."%")->orWhere("user_name", "LIKE", "%".$text."%")->orWhere("im_value", "LIKE", "%".$text."%")->orWhere("port", "LIKE", "%".$text."%")->orWhere("remark", "LIKE", "%".$text."%")->paginate(20, ['*'], 'page', $pageNum);
        $users->setPath('/admin/user/search/'.$text);



        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where("datetime", ">=", time()-90)->orderBy('userid', 'desc')->get();


        $userip=array();
        $useripcount=array();
        $regloc=array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id]=0;
            $userip[$user->id]=array();

            $location=$iplocation->getlocation($user->reg_ip);
            $regloc[$user->id]=iconv('gbk', 'utf-8//IGNORE', $location['country'].$location['area']);
        }



        foreach ($total as $single) {
            if (isset($useripcount[$single->userid])) {
                if (!isset($userip[$single->userid][$single->ip])) {
                    $useripcount[$single->userid]=$useripcount[$single->userid]+1;
                    $location=$iplocation->getlocation($single->ip);
                    $userip[$single->userid][$single->ip]=iconv('gbk', 'utf-8//IGNORE', $location['country'].$location['area']);
                }
            }
        }


        return $this->view()->assign('users', $users)->assign("regloc", $regloc)->assign("useripcount", $useripcount)->assign("userip", $userip)->display('admin/user/index.tpl');
    }

    public function sort($request, $response, $args)
    {
        $pageNum = 1;
        $text=$args["text"];
        $asc=$args["asc"];
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }


        $users->setPath('/admin/user/sort/'.$text."/".$asc);



        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where("datetime", ">=", time()-90)->orderBy('userid', 'desc')->get();


        $userip=array();
        $useripcount=array();
        $regloc=array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id]=0;
            $userip[$user->id]=array();

            $location=$iplocation->getlocation($user->reg_ip);
            $regloc[$user->id]=iconv('gbk', 'utf-8//IGNORE', $location['country'].$location['area']);
        }



        foreach ($total as $single) {
            if (isset($useripcount[$single->userid])) {
                if (!isset($userip[$single->userid][$single->ip])) {
                    $useripcount[$single->userid]=$useripcount[$single->userid]+1;
                    $location=$iplocation->getlocation($single->ip);
                    $userip[$single->userid][$single->ip]=iconv('gbk', 'utf-8//IGNORE', $location['country'].$location['area']);
                }
            }
        }


        return $this->view()->assign('users', $users)->assign("regloc", $regloc)->assign("useripcount", $useripcount)->assign("userip", $userip)->display('admin/user/index.tpl');
    }


    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if ($user == null) {
        }
        return $this->view()->assign('edit_user', $user)->display('admin/user/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $email1=$user->email;

        $user->email =  $request->getParam('email');

        $email2=$request->getParam('email');

        $passwd=$request->getParam('passwd');

        Radius::ChangeUserName($email1, $email2, $passwd);


        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            Wecenter::ChangeUserName($email1, $email2, $request->getParam('pass'), $user->user_name);
            $user->clean_link();
        }

        $user->auto_reset_day =  $request->getParam('auto_reset_day');
        $user->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $origin_port = $user->port;
        $user->port =  $request->getParam('port');

        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        $user->passwd = $request->getParam('passwd');
        $user->protocol = $request->getParam('protocol');
        $user->protocol_param = $request->getParam('protocol_param');
        $user->obfs = $request->getParam('obfs');
        $user->obfs_param = $request->getParam('obfs_param');
        $user->is_multi_user = $request->getParam('is_multi_user');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->node_speedlimit = $request->getParam('node_speedlimit');
        $user->node_connector = $request->getParam('node_connector');
        $user->enable = $request->getParam('enable');
        $user->is_admin = $request->getParam('is_admin');
        $user->node_group = $request->getParam('group');
        $user->ref_by = $request->getParam('ref_by');
        $user->remark = $request->getParam('remark');
        $user->money = $request->getParam('money');
        $user->class = $request->getParam('class');
        $user->class_expire = $request->getParam('class_expire');
        $user->expire_in = $request->getParam('expire_in');

        $user->forbidden_ip = str_replace(PHP_EOL, ",", $request->getParam('forbidden_ip'));
        $user->forbidden_port = str_replace(PHP_EOL, ",", $request->getParam('forbidden_port'));

        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $user = User::find($id);

        $email1=$user->email;

        if (!$user->kill_user()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }

        $users = User::skip(($pageNum - 1) * 100)->limit(100)->get();
        $total_conut = User::count();
        if($total_conut < $pageNum * 100) {
            $res['next'] = 0;
        } else {
            $res['next'] = 1;
        }

        $res['data'] = array();
        foreach ($users as $user) {
            array_push($res['data'], $user->get_table_json_array());
        }

        return $this->echoJson($response, $res);
    }
}
