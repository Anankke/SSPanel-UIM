<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class LlmController extends BaseController
{
    private static array $update_field = [
        'llm_backend',
        'openai_api_key',
        'openai_model_id',
        'google_ai_api_key',
        'google_ai_model_id',
        'vertex_ai_access_token',
        'vertex_ai_location',
        'vertex_ai_project_id',
        'vertex_ai_model_id',
        'huggingface_api_key',
        'huggingface_endpoint_url',
        'cf_workers_ai_account_id',
        'cf_workers_ai_api_token',
        'cf_workers_ai_model_id',
        'anthropic_api_key',
        'anthropic_model_id',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('llm');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/llm.tpl')
        );
    }

    public function save(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        foreach (self::$update_field as $item) {
            if (! Config::set($item, $request->getParam($item))) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '保存 ' . $item . ' 时出错',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }
}
