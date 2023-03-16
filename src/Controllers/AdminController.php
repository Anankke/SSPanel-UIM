<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Analytics;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

/*
 *  Admin Controller
 */
final class AdminController extends BaseController
{
    /**
     * 后台首页
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $today_income = Tools::getIncome('today');
        $yesterday_income = Tools::getIncome('yesterday');
        $this_month_income = Tools::getIncome('this month');
        $total_income = Tools::getIncome('total');

        return $response->write(
            $this->view()
                ->assign('sts', new Analytics())
                ->assign('today_income', $today_income)
                ->assign('yesterday_income', $yesterday_income)
                ->assign('this_month_income', $this_month_income)
                ->assign('total_income', $total_income)
                ->fetch('admin/index.tpl')
        );
    }
}
