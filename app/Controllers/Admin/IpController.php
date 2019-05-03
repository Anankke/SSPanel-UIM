<?php

namespace App\Controllers\Admin;

use App\Models\Ip;
use App\Models\LoginIp;
use App\Models\BlockIp;
use App\Models\UnblockIp;
use App\Models\Node;
use App\Controllers\AdminController;
use App\Utils\QQWry;
use App\Utils\Tools;
use App\Services\Auth;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class IpController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array("id" => "ID", "userid" => "用户ID",
            "user_name" => "用户名", "ip" => "IP",
            "location" => "归属地", "datetime" => "时间", "type" => "类型");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'login/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/ip/login.tpl');
    }

    public function alive($request, $response, $args)
    {
        $table_config['total_column'] = array("id" => "ID", "userid" => "用户ID",
            "user_name" => "用户名", "nodeid" => "节点ID",
            "node_name" => "节点名", "ip" => "IP",
            "location" => "归属地", "datetime" => "时间", "is_node" => "是否为中转连接");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'alive/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/ip/alive.tpl');
    }

    public function block($request, $response, $args)
    {
        $table_config['total_column'] = array("id" => "ID",
            "name" => "节点名称", "ip" => "IP",
            "location" => "归属地", "datetime" => "时间");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'block/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/ip/block.tpl');
    }

    public function unblock($request, $response, $args)
    {
        $table_config['total_column'] = array("id" => "ID", "userid" => "用户ID",
            "user_name" => "用户名", "ip" => "IP",
            "location" => "归属地", "datetime" => "时间");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'unblock/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/ip/unblock.tpl');
    }

    public function doUnblock($request, $response, $args)
    {
        $ip = $request->getParam('ip');

        $user = Auth::getUser();
        $BIP = BlockIp::where("ip", $ip)->get();
        foreach ($BIP as $bi) {
            $bi->delete();
        }

        $UIP = new UnblockIp();
        $UIP->userid = $user->id;
        $UIP->ip = $ip;
        $UIP->datetime = time();
        $UIP->save();


        $res['ret'] = 1;
        $res['msg'] = "发送解封命令解封 " . $ip . " 成功";
        return $this->echoJson($response, $res);
    }

    public function ajax_block($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select blockip.id,node.name,blockip.ip,blockip.ip as location,datetime from blockip,ss_node as node WHERE blockip.nodeid = node.id');

        $datatables->edit('datetime', function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $iplocation = new QQWry();

        $datatables->edit('location', function ($data) use ($iplocation) {
            $location = $iplocation->getlocation($data['location']);
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }

    public function ajax_unblock($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select unblockip.id,userid,user.user_name,unblockip.ip,unblockip.ip as location,datetime from unblockip,user WHERE unblockip.userid = user.id');

        $datatables->edit('datetime', function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $iplocation = new QQWry();

        $datatables->edit('location', function ($data) use ($iplocation) {
            $location = $iplocation->getlocation($data['location']);
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }

    public function ajax_login($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select login_ip.id,login_ip.userid,user.user_name,login_ip.ip,login_ip.ip as location,login_ip.datetime,login_ip.type from login_ip,user WHERE login_ip.userid = user.id');

        $datatables->edit('datetime', function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });


        $iplocation = new QQWry();
        $datatables->edit('location', function ($data) use ($iplocation) {
            $location = $iplocation->getlocation($data['location']);
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        $datatables->edit('type', function ($data) {
            return $data['type'] == 0 ? '成功' : '失败';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    public function ajax_alive($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select alive_ip.id,alive_ip.userid,user.user_name,alive_ip.nodeid,ss_node.name as node_name,alive_ip.ip,alive_ip.ip as location,alive_ip.datetime,alive_ip.id as is_node from alive_ip,user,ss_node WHERE alive_ip.userid = user.id and alive_ip.nodeid = ss_node.id and `datetime` > UNIX_TIMESTAMP() - 60');

        $datatables->edit('datetime', function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $iplocation = new QQWry();

        $datatables->edit('ip', function ($data) {
            return Tools::getRealIp($data['ip']);
        });

        $datatables->edit('is_node', function ($data) {
            $is_node = Node::where("node_ip", Tools::getRealIp($data['ip']))->first();
            if ($is_node) {
                return "是";
            } else {
                return "否";
            }
        });

        $datatables->edit('location', function ($data) use ($iplocation) {
            $location = $iplocation->getlocation(Tools::getRealIp($data['location']));
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
