<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ann;
use App\Models\User;
use App\Utils\Telegram;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function str_replace;
use function strip_tags;
use const PHP_EOL;

final class AnnController extends BaseController
{
    public static array $details =
        [
            'field' => [
                'op' => '操作',
                'id' => '公告ID',
                'date' => '日期',
                'content' => '公告内容',
            ],
        ];

    public static array $update_field = [
        'email_notify_class',
    ];

    /**
     * 后台公告页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/announcement/index.tpl')
        );
    }

    /**
     * 后台公告创建页面
     *
     * @throws Exception
     */
    public function create(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->fetch('admin/announcement/create.tpl')
        );
    }

    /**
     * 后台添加公告
     *
     * @throws TelegramSDKException
     */
    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $email_notify_class = (int) $request->getParam('email_notify_class');
        $email_notify = $request->getParam('email_notify') === 'true' ? 1 : 0;

        $content = strip_tags(
            str_replace(
                ['<p>','</p>'],
                ['','<br><br>'],
                $request->getParam('content')
            ),
            ['br', 'a', 'strong']
        );
        $subject = $_ENV['appName'] . ' - 新公告发布';

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

        if ($email_notify) {
            $users = User::where('class', '>=', $email_notify_class)
                ->get();

            foreach ($users as $user) {
                $user->sendMail(
                    $subject,
                    'warn.tpl',
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
            try {
                Telegram::sendHtml('新公告：' . PHP_EOL . $content);
            } catch (TelegramSDKException $e) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => $email_notify === 1 ? '公告添加成功，邮件发送成功，Telegram发送失败' : '公告添加成功，Telegram发送失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => $email_notify === 1 ? '公告添加成功，邮件发送成功' : '公告添加成功',
        ]);
    }

    /**
     * 后台编辑公告页面
     *
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $ann = Ann::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('ann', $ann)
                ->fetch('admin/announcement/edit.tpl')
        );
    }

    /**
     * 后台编辑公告提交
     *
     * @throws TelegramSDKException
     */
    public function update(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $ann = Ann::find($args['id']);
        $ann->content = (string) $request->getParam('content');
        $ann->date = date('Y-m-d H:i:s');

        if (! $ann->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '公告更新失败',
            ]);
        }

        if ($_ENV['enable_telegram']) {
            try {
                Telegram::sendHtml('公告更新：' . PHP_EOL . $request->getParam('content'));
            } catch (TelegramSDKException $e) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '公告更新成功，Telegram发送失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '公告更新成功',
        ]);
    }

    /**
     * 后台删除公告
     */
    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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

    /**
     * 后台公告页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $anns = Ann::orderBy('id', 'asc')->get();

        foreach ($anns as $ann) {
            $ann->op = '<button type="button" class="btn btn-red" id="delete-announcement-' . $ann->id . '" 
            onclick="deleteAnn(' . $ann->id . ')">删除</button>
            <a class="btn btn-blue" href="/admin/announcement/' . $ann->id . '/edit">编辑</a>';
        }

        return $response->withJson([
            'anns' => $anns,
        ]);
    }
}
