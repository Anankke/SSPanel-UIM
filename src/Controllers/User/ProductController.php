<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Product;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;

final class ProductController extends BaseController
{
    public function product(ServerRequest $request, Response $response, array $args)
    {
        $products = Product::where('status', '1')
            ->where('type', 'tabp')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($products as $product) {
            $product->content = json_decode($product->content);
        }

        return $response->write(
            $this->view()
                ->assign('products', $products)
                ->fetch('user/product.tpl')
        );
    }
}
