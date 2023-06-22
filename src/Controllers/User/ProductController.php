<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Product;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;

final class ProductController extends BaseController
{
    /**
     * @throws Exception
     */
    public function product(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $tabps = Product::where('status', '1')
            ->where('type', 'tabp')
            ->orderBy('id', 'asc')
            ->get();

        $bandwidths = Product::where('status', '1')
            ->where('type', 'bandwidth')
            ->orderBy('id', 'asc')
            ->get();

        $times = Product::where('status', '1')
            ->where('type', 'time')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($tabps as $tabp) {
            $tabp->content = json_decode($tabp->content);
        }

        foreach ($bandwidths as $bandwidth) {
            $bandwidth->content = json_decode($bandwidth->content);
        }

        foreach ($times as $time) {
            $time->content = json_decode($time->content);
        }

        return $response->write(
            $this->view()
                ->assign('tabps', $tabps)
                ->assign('bandwidths', $bandwidths)
                ->assign('times', $times)
                ->fetch('user/product.tpl')
        );
    }
}
