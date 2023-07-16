<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Docs;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class DocsController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $docs = Docs::orderBy('id', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('docs', $docs)
                ->fetch('user/docs/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function detail(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = $args['id'];
        $doc = Docs::find($id);

        return $response->write(
            $this->view()
                ->assign('doc', $doc)
                ->fetch('user/docs/view.tpl')
        );
    }
}
