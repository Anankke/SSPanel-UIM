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
                                    <a href="#notification" class="nav-link active" data-bs-toggle="tab">通知设定</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#telegram" class="nav-link" data-bs-toggle="tab">Telegram Bot</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#discord" class="nav-link" data-bs-toggle="tab">Discord Bot</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#slack" class="nav-link" data-bs-toggle="tab">Slack Bot</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="notification">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">添加节点通知</label>
                                            <div class="col">
                                                <select id="telegram_add_node" class="col form-select"
                                                        value="{$settings['telegram_add_node']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_add_node']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1" {if $settings['telegram_add_node']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">添加节点通知文本</label>
                                            <div class="col">
                                                <input id="telegram_add_node_text" type="text" class="form-control"
                                                       value="{$settings['telegram_add_node_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">修改节点通知</label>
                                            <div class="col">
                                                <select id="telegram_update_node" class="col form-select"
                                                        value="{$settings['telegram_update_node']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_update_node']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_update_node']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">修改节点通知文本</label>
                                            <div class="col">
                                                <input id="telegram_update_node_text" type="text" class="form-control"
                                                       value="{$settings['telegram_update_node_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">删除节点通知</label>
                                            <div class="col">
                                                <select id="telegram_delete_node" class="col form-select"
                                                        value="{$settings['telegram_delete_node']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_delete_node']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_delete_node']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">删除节点通知文本</label>
                                            <div class="col">
                                                <input id="telegram_delete_node_text" type="text" class="form-control"
                                                       value="{$settings['telegram_delete_node_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点被墙通知</label>
                                            <div class="col">
                                                <select id="telegram_node_gfwed" class="col form-select"
                                                        value="{$settings['telegram_node_gfwed']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_node_gfwed']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_node_gfwed']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点被墙通知文本</label>
                                            <div class="col">
                                                <input id="telegram_node_gfwed_text" type="text" class="form-control"
                                                       value="{$settings['telegram_node_gfwed_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点被墙恢复通知</label>
                                            <div class="col">
                                                <select id="telegram_node_ungfwed" class="col form-select"
                                                        value="{$settings['telegram_node_ungfwed']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_node_ungfwed']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_node_ungfwed']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点被墙恢复通知文本</label>
                                            <div class="col">
                                                <input id="telegram_node_ungfwed_text" type="text" class="form-control"
                                                       value="{$settings['telegram_node_ungfwed_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点离线通知</label>
                                            <div class="col">
                                                <select id="telegram_node_offline" class="col form-select"
                                                        value="{$settings['telegram_node_offline']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_node_offline']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_node_offline']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点离线通知文本</label>
                                            <div class="col">
                                                <input id="telegram_node_offline_text" type="text" class="form-control"
                                                       value="{$settings['telegram_node_offline_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点上线通知</label>
                                            <div class="col">
                                                <select id="telegram_node_online" class="col form-select"
                                                        value="{$settings['telegram_node_online']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_node_online']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_node_online']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">节点上线通知文本</label>
                                            <div class="col">
                                                <input id="telegram_node_online_text" type="text" class="form-control"
                                                       value="{$settings['telegram_node_online_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">每日任务通知</label>
                                            <div class="col">
                                                <select id="telegram_daily_job" class="col form-select"
                                                        value="{$settings['telegram_daily_job']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_daily_job']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1" {if $settings['telegram_daily_job']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">每日任务通知文本</label>
                                            <div class="col">
                                                <input id="telegram_daily_job_text" type="text" class="form-control"
                                                       value="{$settings['telegram_daily_job_text']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">系统运行状况通知</label>
                                            <div class="col">
                                                <select id="telegram_diary" class="col form-select"
                                                        value="{$settings['telegram_diary']}">
                                                    <option value="0" {if ! $settings['telegram_diary']}selected{/if}>
                                                        关闭
                                                    </option>
                                                    <option value="1" {if $settings['telegram_diary']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">系统运行状况通知文本</label>
                                            <div class="col">
                                                <input id="telegram_diary_text" type="text" class="form-control"
                                                       value="{$settings['telegram_diary_text']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="telegram">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">是否启用 Telegram
                                                机器人</label>
                                            <div class="col">
                                                <select id="enable_telegram" class="col form-select"
                                                        value="{$settings['enable_telegram']}">
                                                    <option value="0" {if ! $settings['enable_telegram']}selected{/if}>
                                                        关闭
                                                    </option>
                                                    <option value="1" {if $settings['enable_telegram']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Bot Token</label>
                                            <div class="col">
                                                <input id="telegram_token" type="text" class="form-control"
                                                       value="{$settings['telegram_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram 群组会话 ID</label>
                                            <div class="col">
                                                <input id="telegram_chatid" type="text" class="form-control"
                                                       value="{$settings['telegram_chatid']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram 机器人账号</label>
                                            <div class="col">
                                                <input id="telegram_bot" type="text" class="form-control"
                                                       value="{$settings['telegram_bot']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram Webhook 密钥</label>
                                            <div class="col">
                                                <input id="telegram_request_token" type="text" class="form-control"
                                                       value="{$settings['telegram_request_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">解绑 Telegram
                                                账户后自动踢出群组</label>
                                            <div class="col">
                                                <select id="telegram_unbind_kick_member" class="col form-select"
                                                        value="{$settings['telegram_unbind_kick_member']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_unbind_kick_member']}selected{/if}>
                                                        关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_unbind_kick_member']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">仅允许已绑定 Telegram
                                                账户的用户加入群组</label>
                                            <div class="col">
                                                <select id="telegram_group_bound_user" class="col form-select"
                                                        value="{$settings['telegram_group_bound_user']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_group_bound_user']}selected{/if}>
                                                        关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_group_bound_user']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram
                                                机器人发送欢迎消息</label>
                                            <div class="col">
                                                <select id="enable_welcome_message" class="col form-select"
                                                        value="{$settings['enable_welcome_message']}">
                                                    <option value="0"
                                                            {if ! $settings['enable_welcome_message']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['enable_welcome_message']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram
                                                机器人在群组中不回应</label>
                                            <div class="col">
                                                <select id="telegram_group_quiet" class="col form-select"
                                                        value="{$settings['telegram_group_quiet']}">
                                                    <option value="0"
                                                            {if ! $settings['telegram_group_quiet']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['telegram_group_quiet']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">允许 Bot
                                                加入下方配置之外的群组</label>
                                            <div class="col">
                                                <select id="allow_to_join_new_groups" class="col form-select"
                                                        value="{$settings['allow_to_join_new_groups']}">
                                                    <option value="0"
                                                            {if ! $settings['allow_to_join_new_groups']}selected{/if}>关闭
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['allow_to_join_new_groups']}selected{/if}>开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">允许加入的群组 ID</label>
                                            <div class="col">
                                                <input id="group_id_allowed_to_join" type="text" class="form-control"
                                                       value="{$settings['group_id_allowed_to_join']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">允许任意未知的命令触发 /help
                                                的回复</label>
                                            <div class="col">
                                                <select id="help_any_command" class="col form-select"
                                                        value="{$settings['help_any_command']}">
                                                    <option value="0" {if ! $settings['help_any_command']}selected{/if}>
                                                        关闭
                                                    </option>
                                                    <option value="1" {if $settings['help_any_command']}selected{/if}>
                                                        开启
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">未绑定账户的回复</label>
                                            <div class="col">
                                                <input id="user_not_bind_reply" type="text" class="form-control"
                                                       value="{$settings['user_not_bind_reply']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Telegram 用户 ID</label>
                                            <input type="text" class="form-control" id="telegram_user_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button id="test-telegram" class="btn btn-primary">发送测试信息
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="discord">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Bot Token</label>
                                            <div class="col">
                                                <input id="discord_bot_token" type="text" class="form-control"
                                                       value="{$settings['discord_bot_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Client ID</label>
                                            <div class="col">
                                                <input id="discord_client_id" type="text" class="form-control"
                                                       value="{$settings['discord_client_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Client Secret</label>
                                            <div class="col">
                                                <input id="discord_client_secret" type="text" class="form-control"
                                                       value="{$settings['discord_client_secret']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Guild ID</label>
                                            <div class="col">
                                                <input id="discord_guild_id" type="text" class="form-control"
                                                       value="{$settings['discord_guild_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Discord 用户 ID</label>
                                            <input type="text" class="form-control" id="discord_user_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button id="test-discord" class="btn btn-primary">发送测试信息
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="slack">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">App Token</label>
                                            <div class="col">
                                                <input id="slack_token" type="text" class="form-control"
                                                       value="{$settings['slack_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Client ID</label>
                                            <div class="col">
                                                <input id="slack_client_id" type="text" class="form-control"
                                                       value="{$settings['slack_client_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Client Secret</label>
                                            <div class="col">
                                                <input id="slack_client_secret" type="text" class="form-control"
                                                       value="{$settings['slack_client_secret']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Team ID</label>
                                            <div class="col">
                                                <input id="slack_team_id" type="text" class="form-control"
                                                       value="{$settings['slack_team_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Slack 用户 ID</label>
                                            <input type="text" class="form-control" id="slack_user_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button id="test-slack" class="btn btn-primary">发送测试信息
                                                    </button>
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
        </div>

        <script>
            $("#save-setting").click(function () {
                $.ajax({
                    url: '/admin/setting/im',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        {foreach $update_field as $key}
                        {$key}: $('#{$key}').val(),
                        {/foreach}
                    },
                    success: function (data) {
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

            $("#test-telegram").click(function () {
                $.ajax({
                    url: '/admin/setting/test/telegram',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        telegram_user_id: $('#telegram_user_id').val(),
                    },
                    success: function (data) {
                        if (data.ret === 1) {
                            $('#success-noreload-message').text(data.msg);
                            $('#success-noreload-dialog').modal('show');
                        } else {
                            $('#fail-message').text(data.msg);
                            $('#fail-dialog').modal('show');
                        }
                    }
                })
            });

            $("#test-discord").click(function () {
                $.ajax({
                    url: '/admin/setting/test/discord',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        discord_user_id: $('#discord_user_id').val(),
                    },
                    success: function (data) {
                        if (data.ret === 1) {
                            $('#success-noreload-message').text(data.msg);
                            $('#success-noreload-dialog').modal('show');
                        } else {
                            $('#fail-message').text(data.msg);
                            $('#fail-dialog').modal('show');
                        }
                    }
                })
            });

            $("#test-slack").click(function () {
                $.ajax({
                    url: '/admin/setting/test/slack',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        slack_user_id: $('#slack_user_id').val(),
                    },
                    success: function (data) {
                        if (data.ret === 1) {
                            $('#success-noreload-message').text(data.msg);
                            $('#success-noreload-dialog').modal('show');
                        } else {
                            $('#fail-message').text(data.msg);
                            $('#fail-dialog').modal('show');
                        }
                    }
                })
            });
        </script>

        {include file='admin/footer.tpl'}
