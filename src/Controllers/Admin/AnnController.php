<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ann;
use App\Models\User;
use App\Utils\Telegram;
use Slim\Http\Request;
use Slim\Http\Response;

final class AnnController extends BaseController
{
    public static $details =
    [
        'field' => [
            'op' => '操作',
            'id' => '公告ID',
            'date' => '日期',
            'content' => '公告内容',
        ],
    ];

    public static $update_field = [
        'email_notify_class',
    ];

    /**
     * 后台公告页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->display('admin/announcement/index.tpl')
        );
    }

    /**
     * 后台公告页面 AJAX
     *
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args)
    {
        $anns = Ann::orderBy('id', 'asc')->get();

        foreach ($anns as $ann) {
            $ann->op = '<button type="button" class="btn btn-red" id="delete-announcement" 
            onclick="deleteAnn(' . $ann->id . ')">删除</button>
            <a class="btn btn-blue" href="/admin/announcement/' . $ann->id . '/edit">编辑</a>';
        }

        return $response->withJson([
            'anns' => $anns,
        ]);
    }

    /**
     * 后台公告创建页面
     *
     * @param array     $args
     */
    public function create(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->display('admin/announcement/create.tpl')
        );
    }

    /**
     * 后台添加公告
     *
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args)
    {
        $email_notify_class = (int) $request->getParam('email_notify_class');
        $email_notify = (int) $request->getParam('email_notify');
        $content = (string) $request->getParam('content');
        $subject = $_ENV['appName'] . ' - 公告';

        if ($content !== '') {
            $ann = new Ann();
            $ann->date = date('Y-m-d H:i:s');
            $ann->content = $content;

            if (! $ann->save()) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '公告保存失败',
                ]);
            }
        }
        if ($email_notify === 1) {
            $users = User::where('class', '>=', $email_notify_class)
                ->get();

            foreach ($users as $user) {
                $user->sendMail(
                    $subject,
                    'news/warn.tpl',
                    [
                        'user' => $user,
                        'text' => $content,
                    ],
                    [],
                    true
                );
            }
        }

        if ($_ENV['enable_telegram']) {
            Telegram::sendMarkdown('新公告：' . PHP_EOL . $request->getParam('markdown'));
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => $email_notify === 1 ? '公告添加成功，邮件发送成功' : '公告添加成功',
        ]);
    }

    /**
     * 后台编辑公告页面
     *
     * @param array     $args
     */
    public function edit(Request $request, Response $response, array $args)
    {
        $ann = Ann::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('ann', $ann)
                ->display('admin/announcement/edit.tpl')
        );
    }

    /**
     * 后台编辑公告提交
     *
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args)
    {
        $ann = Ann::find($args['id']);
        $ann->content = $request->getParam('content');
        $ann->date = date('Y-m-d H:i:s');
        if (! $ann->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }
        Telegram::sendMarkdown('公告更新：' . PHP_EOL . $request->getParam('markdown'));
        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    /**
     * 后台删除公告
     *
     * @param array     $args
     */
    public function delete(Request $request, Response $response, array $args)
    {
        $ann = Ann::find($args['id']);
        if (! $ann->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }
}
