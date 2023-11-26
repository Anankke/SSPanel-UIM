<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Docs;
use App\Services\LLM;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function time;

final class DocsController extends BaseController
{
    private static array $details =
        [
            'field' => [
                'op' => '操作',
                'id' => 'ID',
                'date' => '日期',
                'title' => '标题',
            ],
        ];

    /**
     * 后台文档页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/docs/index.tpl')
        );
    }

    /**
     * 后台文档创建页面
     *
     * @throws Exception
     */
    public function create(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->fetch('admin/docs/create.tpl')
        );
    }

    /**
     * 后台添加文档
     */
    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');

        if ($content === '' || $title === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '文档标题或内容不能为空',
            ]);
        }

        $doc = new Docs();
        $doc->date = Tools::toDateTime(time());
        $doc->title = $title;
        $doc->content = $content;

        if (! $doc->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '文档添加失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '文档添加成功',
        ]);
    }

    /**
     * 使用LLM生成文档
     *
     * @throws GuzzleException
     */
    public function generate(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $content = LLM::genTextResponse($request->getParam('question'));

        return $response->withJson([
            'ret' => 1,
            'msg' => '文档生成成功',
            'content' => $content,
        ]);
    }

    /**
     * 文档编辑页面
     *
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $doc = (new Docs())->find($args['id']);

        return $response->write(
            $this->view()
                ->assign('doc', $doc)
                ->fetch('admin/docs/edit.tpl')
        );
    }

    /**
     * 后台编辑文档提交
     *
     * @param ServerRequest $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|ResponseInterface
     */
    public function update(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $doc = (new Docs())->find($args['id']);
        $doc->title = $request->getParam('title');
        $doc->content = $request->getParam('content');
        $doc->date = Tools::toDateTime(time());

        if (! $doc->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '文档更新失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '文档更新成功',
        ]);
    }

    /**
     * 后台删除文档
     */
    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $doc = (new Docs())->find($args['id']);

        if (! $doc->delete()) {
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
     * 后台文档页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $docs = (new Docs())->orderBy('id')->get();

        foreach ($docs as $doc) {
            $doc->op = '<button type="button" class="btn btn-red" id="delete-doc-' . $doc->id . '" 
            onclick="deleteDoc(' . $doc->id . ')">删除</button>
            <a class="btn btn-blue" href="/admin/docs/' . $doc->id . '/edit">编辑</a>';
        }

        return $response->withJson([
            'docs' => $docs,
        ]);
    }
}
