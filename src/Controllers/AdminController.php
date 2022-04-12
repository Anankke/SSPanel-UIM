<?php
namespace App\Controllers;

use App\Utils\{
    Tools,
    DatatablesHelper
};
use Slim\Http\{
    Request,
    Response
};
use App\Models\User;
use App\Services\Analytics;
use Ozdemir\Datatables\Datatables;

/**
 *  Admin Controller
 */
class AdminController extends UserController
{
    /**
     * 后台首页
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->assign('sts', new Analytics())
                ->display('admin/index.tpl')
        );
    }

    /**
     * 统计信息
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function sys($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/index.tpl')
        );
    }

    /**
     * 后台邀请返利页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function invite($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'id'              => 'ID',
            'total'           => '原始金额',
            'event_user_id'   => '发起用户ID',
            'event_user_name' => '发起用户名',
            'ref_user_id'     => '获利用户ID',
            'ref_user_name'   => '获利用户名',
            'ref_get'         => '获利金额',
            'datetime'        => '时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'payback/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/invite.tpl')
        );
    }

    /**
     * 后台邀请返利页面 AJAX
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax_payback($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select payback.id,payback.total,payback.userid as event_user_id,event_user.user_name as event_user_name,payback.ref_by as ref_user_id,ref_user.user_name as ref_user_name,payback.ref_get,payback.datetime from payback,user as event_user,user as ref_user where event_user.id = payback.userid and ref_user.id = payback.ref_by');
        $datatables->edit('datetime', static function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });
        return $response->write(
            $datatables->generate()
        );
    }

    /**
     * 更改用户邀请者
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function chgInvite($request, $response, $args)
    {
        $userid = $request->getParam('userid');
        if ($userid == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请者更改失败，检查用户id是否输入正确'
            ]);
        }
        if (strpos($userid, '@') != false) {
            $user = User::where('email', '=', $userid)->first();
        } else {
            $user = User::find((int) $userid);
        }
        if ($user == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请者更改失败，检查用户id是否输入正确'
            ]);
        }
        $user->ref_by = $request->getParam('refid', 0);  //如未提供，则删除用户的邀请者
        $user->save();
        return $response->withJson([
            'ret' => 1,
            'msg' => '邀请者更改成功'
        ]);
    }

    /**
     * 为用户添加邀请次数
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function addInvite($request, $response, $args)
    {
        $num = $request->getParam('num');
        if (Tools::isInt($num) == false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法请求'
            ]);
        }
        if ($request->getParam('uid') == '0') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请次数添加失败，检查用户id或者用户邮箱是否输入正确'
            ]);
        }
        if (strpos($request->getParam('uid'), '@') != false) {
            $user = User::where('email', '=', $request->getParam('uid'))->first();
        } else {
            $user = User::find((int) $request->getParam('uid'));
        }
        if ($user == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请次数添加失败，检查用户id或者用户邮箱是否输入正确'
            ]);
        }
        $user->addInviteNum($num);
        return $response->withJson([
            'ret' => 1,
            'msg' => '邀请次数添加成功'
        ]);
    }
}
