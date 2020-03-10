<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Utils\{
    QQWry,
    DatatablesHelper
};
use Ozdemir\Datatables\Datatables;
use Psr\Http\Message\ResponseInterface;

class SubscribeLogController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $table_config['total_column'] = array(
            'id'                  => 'ID',
            'user_name'           => '用户名',
            'user_id'             => '用户ID',
            'email'               => '用户邮箱',
            'subscribe_type'      => '类型',
            'request_ip'          => 'IP',
            'location'            => '归属地',
            'request_time'        => '时间',
            'request_user_agent'  => 'User-Agent'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'subscribe/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/subscribe.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax_subscribe_log($request, $response, $args): ResponseInterface
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select user_subscribe_log.id,user_subscribe_log.user_name,user_subscribe_log.user_id,user_subscribe_log.email,user_subscribe_log.subscribe_type,user_subscribe_log.request_ip,user_subscribe_log.request_ip as location,user_subscribe_log.request_time,user_subscribe_log.request_user_agent from user_subscribe_log');

        $iplocation = new QQWry();
        $datatables->edit('location', static function ($data) use ($iplocation) {
            $location = $iplocation->getlocation($data['location']);
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        return $response->write(
            $datatables->generate()
        );
    }
}
