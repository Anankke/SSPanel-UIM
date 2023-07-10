<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Product;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;
use function json_encode;
use function time;

final class ProductController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '商品ID',
            'type' => '类型',
            'name' => '名称',
            'price' => '售价',
            'status' => '销售状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'sale_count' => '累计销售',
            'stock' => '库存',
        ],
    ];

    public static array $update_field = [
        'type',
        'name',
        'price',
        'status',
        'stock',
        'time',
        'bandwidth',
        'class',
        'class_time',
        'node_group',
        'speed_limit',
        'ip_limit',
        'class_required',
        'node_group_required',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/product/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function create(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->fetch('admin/product/create.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = $args['id'];
        $product = Product::find($id);
        $content = json_decode($product->content);
        $limit = json_decode($product->limit);

        return $response->write(
            $this->view()
                ->assign('product', $product)
                ->assign('content', $content)
                ->assign('limit', $limit)
                ->assign('update_field', self::$update_field)
                ->fetch('admin/product/edit.tpl')
        );
    }

    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        // base product
        $type = $request->getParam('type') ?? '';
        $name = $request->getParam('name') ?? '';
        $price = $request->getParam('price') ?? 0;
        $status = $request->getParam('status') ?? 1;
        $stock = $request->getParam('stock') ?? -1;
        // content
        $time = $request->getParam('time') ?? 0;
        $bandwidth = $request->getParam('bandwidth') ?? 0;
        $class = $request->getParam('class') ?? 0;
        $class_time = $request->getParam('class_time') ?? 0;
        $node_group = $request->getParam('node_group') ?? 0;
        $speed_limit = $request->getParam('speed_limit') ?? 0;
        $ip_limit = $request->getParam('ip_limit') ?? 0;
        // limit
        $class_required = $request->getParam('class_required') ?? '';
        $node_group_required = $request->getParam('node_group_required') ?? '';
        $new_user_required = $request->getParam('new_user_required') === 'true' ? 1 : 0;

        $product = new Product();

        if ($price < 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '无效的商品价格',
            ]);
        }

        if ($type === 'tabp') {
            if ($time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的商品时长',
                ]);
            }

            if ($class_time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的等级时长',
                ]);
            }

            if ($bandwidth <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的套餐流量',
                ]);
            }

            $content = [
                'time' => $time,
                'bandwidth' => $bandwidth,
                'class' => $class,
                'class_time' => $class_time,
                'node_group' => $node_group,
                'speed_limit' => $speed_limit,
                'ip_limit' => $ip_limit,
            ];
        } elseif ($type === 'time') {
            if ($time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的商品时长',
                ]);
            }

            if ($class_time === '' || $class_time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的等级时长',
                ]);
            }

            $content = [
                'time' => $time,
                'class' => $class,
                'class_time' => $class_time,
                'node_group' => $node_group,
                'speed_limit' => $speed_limit,
                'ip_limit' => $ip_limit,
            ];
        } elseif ($type === 'bandwidth') {
            if ($bandwidth <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的套餐流量',
                ]);
            }

            $content = [
                'bandwidth' => $bandwidth,
            ];
        } else {
            return $response->withJson([
                'ret' => 0,
                'msg' => '商品类型错误',
            ]);
        }

        $limit = [
            'class_required' => $class_required,
            'node_group_required' => $node_group_required,
            'new_user_required' => $new_user_required,
        ];

        $product->type = $type;
        $product->name = $name;
        $product->price = $price;
        $product->content = json_encode($content);
        $product->limit = json_encode($limit);
        $product->status = $status;
        $product->create_time = time();
        $product->update_time = time();
        $product->sale_count = 0;
        $product->stock = $stock;
        $product->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功',
        ]);
    }

    public function update(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $product_id = $args['id'];
        // base product
        $type = $request->getParam('type') ?? '';
        $name = $request->getParam('name') ?? '';
        $price = $request->getParam('price') ?? 0;
        $status = $request->getParam('status') ?? 1;
        $stock = $request->getParam('stock') ?? -1;
        // content
        $time = $request->getParam('time') ?? 0;
        $bandwidth = $request->getParam('bandwidth') ?? 0;
        $class = $request->getParam('class') ?? 0;
        $class_time = $request->getParam('class_time') ?? 0;
        $node_group = $request->getParam('node_group') ?? 0;
        $speed_limit = $request->getParam('speed_limit') ?? 0;
        $ip_limit = $request->getParam('ip_limit') ?? 0;
        // limit
        $class_required = $request->getParam('class_required') ?? '';
        $node_group_required = $request->getParam('node_group_required') ?? '';
        $new_user_required = $request->getParam('new_user_required') === 'true' ? 1 : 0;

        $product = Product::find($product_id);

        if ($price < 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '无效的商品价格',
            ]);
        }

        if ($type === 'tabp') {
            if ($time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的商品时长',
                ]);
            }

            if ($class_time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的等级时长',
                ]);
            }

            if ($bandwidth <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的套餐流量',
                ]);
            }

            $content = [
                'time' => $time,
                'bandwidth' => $bandwidth,
                'class' => $class,
                'class_time' => $class_time,
                'node_group' => $node_group,
                'speed_limit' => $speed_limit,
                'ip_limit' => $ip_limit,
            ];
        } elseif ($type === 'time') {
            if ($time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的商品时长',
                ]);
            }

            if ($class_time <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的等级时长',
                ]);
            }

            $content = [
                'time' => $time,
                'class' => $class,
                'class_time' => $class_time,
                'node_group' => $node_group,
                'speed_limit' => $speed_limit,
                'ip_limit' => $ip_limit,
            ];
        } elseif ($type === 'bandwidth') {
            if ($bandwidth <= 0) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无效的套餐流量',
                ]);
            }

            $content = [
                'bandwidth' => $bandwidth,
            ];
        } else {
            return $response->withJson([
                'ret' => 0,
                'msg' => '商品类型错误',
            ]);
        }

        $limit = [
            'class_required' => $class_required,
            'node_group_required' => $node_group_required,
            'new_user_required' => $new_user_required,
        ];

        $product->type = $type;
        $product->name = $name;
        $product->price = $price;
        $product->content = json_encode($content);
        $product->limit = json_encode($limit);
        $product->stock = $stock;
        $product->status = $status;
        $product->update_time = time();
        $product->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '更新成功',
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $product_id = $args['id'];
        Product::find($product_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function copy(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $old_product_id = $args['id'];
        $old_product = Product::find($old_product_id);

        $new_product = $old_product->replicate([
            'create_time',
            'update_time',
        ]);
        $new_product->name .= ' (副本)';
        $new_product->create_time = time();
        $new_product->update_time = time();
        $new_product->sale_count = 0;
        $new_product->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '复制成功',
        ]);
    }

    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $products = Product::orderBy('id', 'desc')->get();

        foreach ($products as $product) {
            $product->op = '<button type="button" class="btn btn-red" id="delete-product-' . $product->id . '"
             onclick="deleteProduct(' . $product->id . ')">删除</button>
            <button type="button" class="btn btn-orange" id="copy-product-' . $product->id . '"
             onclick="copyProduct(' . $product->id . ')">复制</button>
            <a class="btn btn-blue" href="/admin/product/' . $product->id . '/edit">编辑</a>';
            $product->type = $product->type();
            $product->status = $product->status();
            $product->create_time = Tools::toDateTime($product->create_time);
            $product->update_time = Tools::toDateTime($product->update_time);
            $product->stock = $product->stock();
        }

        return $response->withJson([
            'products' => $products,
        ]);
    }
}
