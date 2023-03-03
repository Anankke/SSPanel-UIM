<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\Product;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class OrderController extends BaseController
{
    public function order(ServerRequest $request, Response $response, array $args)
    {
        $orders = Order::where('user_id', $this->user->id)->get();

        return $response->write(
            $this->view()
                ->assign('orders', $orders)
                ->fetch('user/order/index.tpl')
        );
    }

    public function create(ServerRequest $request, Response $response, array $args)
    {
        $product_id = $args['product_id'];
        $product = Product::where('id', $product_id)
            ->first();

        $product->content = \json_decode($product->content);

        return $response->write(
            $this->view()
                ->assign('product', $product)
                ->fetch('user/order/create.tpl')
        );
    }

    public function detail(ServerRequest $request, Response $response, array $args)
    {
        $order_id = $args['id'];
        $order = Order::where('user_id', $this->user->id)
            ->where('id', $order_id)
            ->first();

        return $response->write(
            $this->view()
                ->assign('order', $order)
                ->fetch('user/order/view.tpl')
        );
    }
}
