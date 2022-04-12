<?php
namespace App\Controllers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Product;
use App\Controllers\AdminController;

class ProductController extends AdminController
{
    public static function translate($array, $type)
    {
        if ($type == 'tatp') {
            $text = '时长' . $array['product_time'] . '天';
            $text .= ($array['product_reset_time'] == '0') ? '（叠加上此值）' : '（重置为此值）';
            $text .= '，流量' . $array['product_traffic'] . 'GB';
            $text .= ($array['product_reset_traffic'] == '0') ? '（叠加上此值）' : '（重置为此值）';
            $text .= ($array['product_device'] == '0') ? '，不限制设备' : '，限制' . $array['product_device'] . '个设备使用';
            $text .= ($array['product_speed'] == '0') ? '，不限速' : '，限速' . $array['product_speed'] . 'Mbps';
            $text .= '，设置等级为' . $array['product_class'];
            $text .= '，等级时效' . $array['product_class_time'] . '天';
            if ($array['product_reset_class_time'] == '1') {
                $text .= '（直接叠加等级时长）';
            } elseif ($array['product_reset_class_time'] == '2') {
                $text .= '（直接重置为套餐等级时长）';
            } elseif ($array['product_reset_class_time'] == '3') {
                $text .= '（用户等级与套餐等级不同时，重置为套餐等级时长；相同时叠加）';
            } elseif ($array['product_reset_class_time'] == '4') {
                $text .= '（用户等级与套餐等级不同时，重置为套餐等级时长；相同时重置）';
            }
        }
        if ($type == 'time') {
            $text = '添加账户时长' . $array['product_time'] . '天';
        }
        if ($type == 'traffic') {
            $text = '添加账户流量' . $array['product_traffic'] . 'GB';
        }

        return $text;
    }

    public function index($request, $response, $args)
    {
        $products = Product::all();

        return $response->write(
            $this->view()
                ->assign('products', $products)
                ->display('admin/product/index.tpl')
        );
    }

    public function get($request, $response, $args)
    {
        $product_id = $args['id'];
        $product = Product::find($product_id);

        return $response->withJson([
            'ret' => 1,
            'data' => $product,
            'content' => json_decode($product->content, true)
        ]);
    }

    public function save($request, $response, $args)
    {
        $product_type = $request->getParam('product_type');
        $product_name = $request->getParam('product_name');
        $product_price = $request->getParam('product_price');
        $product_time = $request->getParam('product_time');
        $product_traffic = $request->getParam('product_traffic');
        $product_class = $request->getParam('product_class');
        $product_class_time = $request->getParam('product_class_time');
        $product_status = $request->getParam('product_status');
        $product_reset_time = $request->getParam('product_reset_time');
        $product_reset_traffic = $request->getParam('product_reset_traffic');
        $product_reset_class_time = $request->getParam('product_reset_class_time');
        $product_speed = $request->getParam('product_speed');
        $product_device = $request->getParam('product_device');
        $product_stock = $request->getParam('product_stock');
        $product_html = $request->getParam('product_html');

        try {
            $product = new Product;

            if ($product_name == '') {
                throw new \Exception('请填写商品名称');
            }
            if ($product_price == '') {
                throw new \Exception('请填写商品售价');
            }
            if ($product_stock == '') {
                throw new \Exception('请填写商品库存');
            }

            if ($product_type == 'tatp') {
                if ($product_time == '') {
                    throw new \Exception('请填写套餐时长');
                }
                if ($product_traffic == '') {
                    throw new \Exception('请填写套餐流量');
                }

                ($product_class == '') && $product_class = '0';
                ($product_class_time == '') && $product_class_time = $product_time;
                ($product_speed == '') && $product_speed = '0';
                ($product_device == '') && $product_device = '0';

                $content = [
                    'product_time' => $product_time,
                    'product_traffic' => $product_traffic,
                    'product_class' => $product_class,
                    'product_class_time' => $product_class_time,
                    'product_reset_time' => $product_reset_time,
                    'product_reset_traffic' => $product_reset_traffic,
                    'product_reset_class_time' => $product_reset_class_time,
                    'product_speed' => $product_speed,
                    'product_device' => $product_device,
                ];

                $product->translate = self::translate($content, 'tatp');
            } elseif ($product_type == 'time') {
                if ($product_time == '') {
                    throw new \Exception('请填写套餐时长');
                }

                $content = [
                    'product_time' => $product_time,
                ];

                $product->translate = self::translate($content, 'time');
            } elseif ($product_type == 'traffic') {
                if ($product_traffic == '') {
                    throw new \Exception('请填写套餐流量');
                }

                $content = [
                    'product_traffic' => $product_traffic,
                ];

                $product->translate = self::translate($content, 'traffic');
            }

            $product->type = $product_type;
            $product->name = $product_name;
            $product->price = $product_price * 100;
            $product->content = json_encode($content);
            $product->stock = $product_stock;
            $product->sales = 0;
            $product->html = $product_html;
            $product->limit = null;
            $product->status = $product_status;
            $product->created_at = time();
            $product->updated_at = time();
            $product->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功'
        ]);
    }

    public function update($request, $response, $args)
    {
        $product_id = $args['id'];
        $product_type = $request->getParam('product_type');
        $product_name = $request->getParam('product_name');
        $product_price = $request->getParam('product_price');
        $product_time = $request->getParam('product_time');
        $product_traffic = $request->getParam('product_traffic');
        $product_class = $request->getParam('product_class');
        $product_class_time = $request->getParam('product_class_time');
        $product_status = $request->getParam('product_status');
        $product_reset_time = $request->getParam('product_reset_time');
        $product_reset_traffic = $request->getParam('product_reset_traffic');
        $product_reset_class_time = $request->getParam('product_reset_class_time');
        $product_speed = $request->getParam('product_speed');
        $product_device = $request->getParam('product_device');
        $product_stock = $request->getParam('product_stock');
        $product_html = $request->getParam('product_html');

        try {
            $product = Product::find($product_id);

            if ($product_name == '') {
                throw new \Exception('请填写商品名称');
            }
            if ($product_price == '') {
                throw new \Exception('请填写商品售价');
            }
            if ($product_stock == '') {
                throw new \Exception('请填写商品库存');
            }

            if ($product_type == 'tatp') {
                if ($product_time == '') {
                    throw new \Exception('请填写套餐时长');
                }
                if ($product_traffic == '') {
                    throw new \Exception('请填写套餐流量');
                }

                ($product_class == '') && $product_class = '0';
                ($product_class_time == '') && $product_class_time = $product_time;
                ($product_speed == '') && $product_speed = '0';
                ($product_device == '') && $product_device = '0';

                $content = [
                    'product_time' => $product_time,
                    'product_traffic' => $product_traffic,
                    'product_class' => $product_class,
                    'product_class_time' => $product_class_time,
                    'product_reset_time' => $product_reset_time,
                    'product_reset_traffic' => $product_reset_traffic,
                    'product_reset_class_time' => $product_reset_class_time,
                    'product_speed' => $product_speed,
                    'product_device' => $product_device,
                ];

                $product->translate = self::translate($content, 'tatp');
            } elseif ($product_type == 'time') {
                if ($product_time == '') {
                    throw new \Exception('请填写套餐时长');
                }

                $content = [
                    'product_time' => $product_time,
                ];

                $product->translate = self::translate($content, 'time');
            } elseif ($product_type == 'traffic') {
                if ($product_traffic == '') {
                    throw new \Exception('请填写套餐流量');
                }

                $content = [
                    'product_traffic' => $product_traffic,
                ];

                $product->translate = self::translate($content, 'traffic');
            }

            $product->type = $product_type;
            $product->name = $product_name;
            $product->price = $product_price * 100;
            $product->content = json_encode($content);
            $product->stock = $product_stock;
            $product->html = $product_html;
            $product->status = $product_status;
            $product->updated_at = time();
            $product->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '更新成功'
        ]);
    }

    public function delete($request, $response, $args)
    {
        $product_id = $args['id'];
        Product::find($product_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }
}
