<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Utils\DatatablesHelper;
use Ozdemir\Datatables\Datatables;
use Psr\Http\Message\ResponseInterface;

class DetectBanLogController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $table_config['total_column'] = array(
            "id"                => "ID",
            "user_id"           => "用户ID",
            "user_name"         => "用户名",
            "email"             => "用户邮箱",
            "detect_number"     => "违规次数",
            "ban_time"          => "封禁时长(分钟)",
            "start_time"        => "统计开始时间",
            "end_time"          => "统计结束以及封禁开始时间",
            "ban_end_time"      => "封禁结束时间",
            "all_detect_number" => "累计违规次数"
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'ban/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/detect/ban.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax_log($request, $response, $args): ResponseInterface
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select detect_ban_log.id,detect_ban_log.user_id,detect_ban_log.user_name,detect_ban_log.email,detect_ban_log.detect_number,detect_ban_log.ban_time,detect_ban_log.start_time,detect_ban_log.end_time,detect_ban_log.all_detect_number,detect_ban_log.end_time as ban_end_time from detect_ban_log');
        $datatables->edit('start_time', function ($data) {
            return date('Y-m-d H:i:s', $data['start_time']);
        });
        $datatables->edit('end_time', function ($data) {
            return date('Y-m-d H:i:s', $data['end_time']);
        });
        $datatables->edit('ban_end_time', function ($data) {
            return date('Y-m-d H:i:s', $data['ban_end_time'] + $data['ban_time'] * 60);
        });
        return $response->write(
            $datatables->generate()
        );
    }
}
