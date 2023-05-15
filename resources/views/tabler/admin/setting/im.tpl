{include file='admin/header.tpl'}

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
                                            <option value="0" {if $settings['telegram_add_node'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_add_node']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_update_node'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_update_node']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_delete_node'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_delete_node']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_node_gfwed'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_gfwed']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_node_ungfwed'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_ungfwed']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_node_offline'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_offline']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_node_online'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_node_online']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_daily_job'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_daily_job']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_diary'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_diary']}selected{/if}>开启</option>
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
                                            <option value="0" {if $settings['telegram_unbind_kick_member'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_unbind_kick_member']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">仅允许已绑定 Telegram 账户的用户加入群组</label>
                                    <div class="col">
                                        <select id="telegram_group_bound_user" class="col form-select" value="{$settings['telegram_group_bound_user']}">
                                            <option value="0" {if $settings['telegram_group_bound_user'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_group_bound_user']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">启用 Telegram 机器人显示用户群组链接</label>
                                    <div class="col">
                                        <select id="telegram_show_group_link" class="col form-select" value="{$settings['telegram_show_group_link']}">
                                            <option value="0" {if $settings['telegram_show_group_link'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_show_group_link']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">用户群组链接</label>
                                    <div class="col">
                                        <input id="telegram_group_link" type="text" class="form-control" value="{$settings['telegram_group_link']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Telegram 机器人发送欢迎消息</label>
                                    <div class="col">
                                        <select id="enable_welcome_message" class="col form-select" value="{$settings['enable_welcome_message']}">
                                            <option value="0" {if $settings['enable_welcome_message'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['enable_welcome_message']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Telegram 机器人在群组中不回应</label>
                                    <div class="col">
                                        <select id="telegram_group_quiet" class="col form-select" value="{$settings['telegram_group_quiet']}">
                                            <option value="0" {if $settings['telegram_group_quiet'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['telegram_group_quiet']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">允许 Bot 加入下方配置之外的群组</label>
                                    <div class="col">
                                        <select id="allow_to_join_new_groups" class="col form-select" value="{$settings['allow_to_join_new_groups']}">
                                            <option value="0" {if $settings['allow_to_join_new_groups'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['allow_to_join_new_groups']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">允许加入的群组 ID</label>
                                    <div class="col">
                                        <input id="group_id_allowed_to_join" type="text" class="form-control" value="{$settings['group_id_allowed_to_join']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">额外的 Telegram 管理员 ID</label>
                                    <div class="col">
                                        <input id="telegram_admins" type="text" class="form-control" value="{$settings['telegram_admins']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">非管理员操作管理员功能是否回复</label>
                                    <div class="col">
                                        <select id="enable_not_admin_reply" class="col form-select" value="{$settings['enable_not_admin_reply']}">
                                            <option value="0" {if $settings['enable_not_admin_reply'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['enable_not_admin_reply']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">非管理员操作管理员功能的回复内容</label>
                                    <div class="col">
                                        <input id="not_admin_reply_msg" type="text" class="form-control" value="{$settings['not_admin_reply_msg']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">管理员操作时，找不到用户的回复</label>
                                    <div class="col">
                                        <input id="no_user_found" type="text" class="form-control" value="{$settings['no_user_found']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">管理员操作时，修改数据的字段没有找到的回复</label>
                                    <div class="col">
                                        <input id="data_method_not_found" type="text" class="form-control" value="{$settings['data_method_not_found']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">允许任意未知的命令触发 /help 的回复</label>
                                    <div class="col">
                                        <select id="help_any_command" class="col form-select" value="{$settings['help_any_command']}">
                                            <option value="0" {if $settings['help_any_command'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['help_any_command']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">开启在群组搜寻用户信息时显示用户完整邮箱，关闭则会对邮箱中间内容打码</label>
                                    <div class="col">
                                        <select id="enable_user_email_group_show" class="col form-select" value="{$settings['enable_user_email_group_show']}">
                                            <option value="0" {if $settings['enable_user_email_group_show'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['enable_user_email_group_show']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">未绑定账户的回复</label>
                                    <div class="col">
                                        <input id="user_not_bind_reply" type="text" class="form-control" value="{$settings['user_not_bind_reply']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">面向游客的产品介绍</label>
                                    <div class="col">
                                        <input id="telegram_general_pricing" type="text" class="form-control" value="{$settings['telegram_general_pricing']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">面向游客的服务条款</label>
                                    <div class="col">
                                        <input id="telegram_general_terms" type="text" class="form-control" value="{$settings['telegram_general_terms']}">
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
                if (data.ret === 1) {
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

{include file='admin/footer.tpl'}