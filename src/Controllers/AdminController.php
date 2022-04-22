<?php
namespace App\Controllers;

use App\Services\Analytics;
use Slim\Http\Request;
use Slim\Http\Response;

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
}
