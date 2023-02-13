{include file='admin/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">IM 设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">管理站点的 IM 集成设置</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a id="save-setting" href="#" class="btn btn-primary">
                            <i class="icon ti ti-device-floppy"></i>
                            保存
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-md-12">
                    <div class="card">
                    <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#telegram_notification" class="nav-link active" data-bs-toggle="tab">Telegram 通知设定</a>
                        </li>
                        <li class="nav-item">
                            <a href="#telegram_bot" class="nav-link" data-bs-toggle="tab">Telegram Bot 设定</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="telegram_notification">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">添加节点通知</label>
                                    <div class="col">
                                        <select id="telegram_add_node" class="col form-select" value="{$settings['telegram_add_node']}">
                                            <option value="0" {if $settings['telegram_add_node'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_add_node'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">添加节点通知文本</label>
                                    <div class="col">
                                        <input id="telegram_add_node_text" type="text" class="form-control" value="{$settings['telegram_add_node_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">修改节点通知</label>
                                    <div class="col">
                                        <select id="telegram_update_node" class="col form-select" value="{$settings['telegram_update_node']}">
                                            <option value="0" {if $settings['telegram_update_node'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_update_node'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">修改节点通知文本</label>
                                    <div class="col">
                                        <input id="telegram_update_node_text" type="text" class="form-control" value="{$settings['telegram_update_node_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">删除节点通知</label>
                                    <div class="col">
                                        <select id="telegram_delete_node" class="col form-select" value="{$settings['telegram_delete_node']}">
                                            <option value="0" {if $settings['telegram_delete_node'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_delete_node'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">删除节点通知文本</label>
                                    <div class="col">
                                        <input id="telegram_delete_node_text" type="text" class="form-control" value="{$settings['telegram_delete_node_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点被墙通知</label>
                                    <div class="col">
                                        <select id="telegram_node_gfwed" class="col form-select" value="{$settings['telegram_node_gfwed']}">
                                            <option value="0" {if $settings['telegram_node_gfwed'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_gfwed'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点被墙通知文本</label>
                                    <div class="col">
                                        <input id="telegram_node_gfwed_text" type="text" class="form-control" value="{$settings['telegram_node_gfwed_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点被墙恢复通知</label>
                                    <div class="col">
                                        <select id="telegram_node_ungfwed" class="col form-select" value="{$settings['telegram_node_ungfwed']}">
                                            <option value="0" {if $settings['telegram_node_ungfwed'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_ungfwed'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点被墙恢复通知文本</label>
                                    <div class="col">
                                        <input id="telegram_node_ungfwed_text" type="text" class="form-control" value="{$settings['telegram_node_ungfwed_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点离线通知</label>
                                    <div class="col">
                                        <select id="telegram_node_offline" class="col form-select" value="{$settings['telegram_node_offline']}">
                                            <option value="0" {if $settings['telegram_node_offline'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_offline'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点离线通知文本</label>
                                    <div class="col">
                                        <input id="telegram_node_offline_text" type="text" class="form-control" value="{$settings['telegram_node_offline_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点上线通知</label>
                                    <div class="col">
                                        <select id="telegram_node_online" class="col form-select" value="{$settings['telegram_node_online']}">
                                            <option value="0" {if $settings['telegram_node_online'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_online'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">节点上线通知文本</label>
                                    <div class="col">
                                        <input id="telegram_node_online_text" type="text" class="form-control" value="{$settings['telegram_node_online_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">每日任务通知</label>
                                    <div class="col">
                                        <select id="telegram_daily_job" class="col form-select" value="{$settings['telegram_daily_job']}">
                                            <option value="0" {if $settings['telegram_daily_job'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_daily_job'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">每日任务通知文本</label>
                                    <div class="col">
                                        <input id="telegram_daily_job_text" type="text" class="form-control" value="{$settings['telegram_daily_job_text']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">系统运行状况通知</label>
                                    <div class="col">
                                        <select id="telegram_diary" class="col form-select" value="{$settings['telegram_diary']}">
                                            <option value="0" {if $settings['telegram_diary'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_diary'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">系统运行状况通知文本</label>
                                    <div class="col">
                                        <input id="telegram_diary_text" type="text" class="form-control" value="{$settings['telegram_diary_text']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="telegram_bot">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">解绑 Telegram 账户后自动踢出群组</label>
                                    <div class="col">
                                        <select id="telegram_unbind_kick_member" class="col form-select" value="{$settings['telegram_unbind_kick_member']}">
                                            <option value="0" {if $settings['telegram_unbind_kick_member'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_unbind_kick_member'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">仅允许已绑定 Telegram 账户的用户加入群组</label>
                                    <div class="col">
                                        <select id="telegram_group_bound_user" class="col form-select" value="{$settings['telegram_group_bound_user']}">
                                            <option value="0" {if $settings['telegram_group_bound_user'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_group_bound_user'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">启用 Telegram 机器人显示用户群组链接</label>
                                    <div class="col">
                                        <select id="telegram_show_group_link" class="col form-select" value="{$settings['telegram_show_group_link']}">
                                            <option value="0" {if $settings['telegram_show_group_link'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_show_group_link'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">用户群组链接</label>
                                    <div class="col">
                                        <input id="telegram_group_link" type="text" class="form-control" value="{$settings['telegram_group_link']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#save-setting").click(function() {
        $.ajax({
            url: '/admin/setting/im',
            type: 'POST',
            dataType: "json",
            data: {
                {foreach $update_field as $key}
                {$key}: $('#{$key}').val(),
                {/foreach}
            },
            success: function(data) {
                if (data.ret == 1) {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });
</script>

{include file='admin/tabler_footer.tpl'}