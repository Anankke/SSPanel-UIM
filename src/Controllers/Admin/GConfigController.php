<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\GConfig;
use Psr\Http\Message\ResponseInterface;

class GConfigController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function update($request, $response, $args): ResponseInterface
    {
        $key    = trim($args['key']);
        $user   = $this->user;
        $config = GConfig::where('key', '=', $key)->first();
        if ($config != null && $config->setValue($request->getParam('value'), $user) === true) {
            return $response->write(
                json_encode(
                    [
                    'ret' => 1,
                    'msg' => '修改成功'
                    ]
                )
            );
        }
        return $response->write(
            json_encode(
                [
                    'ret' => 0,
                    'msg' => '修改失败'
                ]
            )
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function edit($request, $response, $args): ResponseInterface
    {
        $key    = trim($args['key']);
        $config = GConfig::where('key', '=', $key)->first();
        return $response->write(
            $this->view()
                ->assign('edit_config', $config)
                ->fetch('admin/config/edit.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function register($request, $response, $args): ResponseInterface
    {
        $table_config['total_column'] = array(
            'op'             => '操作',
            'name'           => '配置名称',
            'key'            => '配置名',
            'value'          => '配置值',
            'operator_id'    => '操作员 ID',
            'operator_name'  => '操作员名称',
            'operator_email' => '操作员邮箱',
            'last_update'    => '修改时间'
        );
        $table_config['default_show_column'] = array('op', 'name', 'value', 'last_update');
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'register/ajax';
        $edit_config = GConfig::where('key', '=', 'Register.string.Mode')->first();
        return $response->write(
            $this->view()
                ->assign('edit_config', $edit_config)
                ->assign('table_config', $table_config)
                ->fetch('admin/config/user/register.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function register_ajax($request, $response, $args): ResponseInterface
    {
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $configs      = GConfig::skip($start)->where('key', 'LIKE', "%Register%")->limit($limit_length)->get();
        $total_conut  = GConfig::where('key', 'LIKE', "%Register%")->count();
        $data         = [];
        foreach ($configs as $config) {
            $tempdata = [];
            $tempdata['op']             = '<a class="btn btn-brand" href="/admin/config/update/' . $config->key . '/edit">编辑</a>';
            $tempdata['name']           = $config->name;
            $tempdata['key']            = $config->key;
            $tempdata['value']          = $config->getValue();
            $tempdata['operator_id']    = $config->operator_id;
            $tempdata['operator_name']  = $config->operator_name;
            $tempdata['operator_email'] = $config->operator_email;
            $tempdata['last_update']    = date('Y-m-d H:i:s', $config->last_update);
            if (strpos($config->key, '.bool.')) {
                $tempdata['value'] = ($config->getValue() ? '开启' : '关闭');
            } else {
                $tempdata['value'] = '(请在编辑页面查看)';
            }
            $data[] = $tempdata;
        }
        $info = [
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => $total_conut,
            'recordsFiltered' => $total_conut,
            'data'            => $data
        ];

        return $response->write(
            json_encode($info)
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function telegram($request, $response, $args): ResponseInterface
    {
        $table_config['total_column'] = array(
            'op'             => '操作',
            'name'           => '配置名称',
            'key'            => '配置名',
            'value'          => '配置值',
            'operator_id'    => '操作员 ID',
            'operator_name'  => '操作员名称',
            'operator_email' => '操作员邮箱',
            'last_update'    => '修改时间'
        );
        $table_config['default_show_column'] = array('op', 'name', 'value', 'last_update');
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'telegram/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->fetch('admin/config/telegram/index.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function telegram_ajax($request, $response, $args): ResponseInterface
    {
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $configs      = GConfig::skip($start)->where('key', 'LIKE', "%Telegram%")->limit($limit_length)->get();
        $total_conut  = GConfig::where('key', 'LIKE', "%Telegram%")->count();
        $data         = [];
        foreach ($configs as $config) {
            $tempdata = [];
            $tempdata['op']             = '<a class="btn btn-brand" href="/admin/config/update/' . $config->key . '/edit">编辑</a>';
            $tempdata['name']           = $config->name;
            $tempdata['key']            = $config->key;
            $tempdata['value']          = $config->value;
            $tempdata['operator_id']    = $config->operator_id;
            $tempdata['operator_name']  = $config->operator_name;
            $tempdata['operator_email'] = $config->operator_email;
            $tempdata['last_update']    = date('Y-m-d H:i:s', $config->last_update);
            if (strpos($config->key, '.bool.')) {
                $tempdata['value'] = ($config->getValue() ? '开启' : '关闭');
            } else {
                $tempdata['value'] = '(请在编辑页面查看)';
            }
            $data[] = $tempdata;
        }
        $info = [
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => $total_conut,
            'recordsFiltered' => $total_conut,
            'data'            => $data
        ];

        return $response->write(
            json_encode($info, true)
        );
    }
}
