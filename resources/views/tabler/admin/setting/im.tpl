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
                                    <a href="#notification" class="nav-link active" data-bs-toggle="tab">Notification</a>
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
                                            <label class="form-label col-3 col-form-label">
                                                Node Addition
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_add_node" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_add_node']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_add_node']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_add_node']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node Update
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_update_node" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_update_node']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_update_node']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_update_node']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node Deletion
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_delete_node" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_delete_node']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_delete_node']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_delete_node']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node GFWed
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_node_gfwed" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_node_gfwed']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_node_gfwed']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_node_gfwed']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node UnGFWed
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_node_ungfwed" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_node_ungfwed']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_node_ungfwed']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_node_ungfwed']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node Online
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_node_online" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_node_online']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_node_online']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_node_online']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Node Offline
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_node_offline" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_node_offline']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_node_offline']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_node_offline']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Daily Job
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_daily_job" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_daily_job']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_daily_job']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_daily_job']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                System Dairy
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_diary" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_diary']}">
                                                    <option value="0" {if ! $settings['im_bot_group_notify_diary']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['im_bot_group_notify_diary']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Announcement Creation
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_ann_create" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_ann_create']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_ann_create']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_ann_create']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Announcement Update
                                            </label>
                                            <div class="col">
                                                <select id="im_bot_group_notify_ann_update" class="col form-select"
                                                        value="{$settings['im_bot_group_notify_ann_update']}">
                                                    <option value="0"
                                                            {if ! $settings['im_bot_group_notify_ann_update']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['im_bot_group_notify_ann_update']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="telegram">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Bot Token</label>
                                            <div class="col">
                                                <input id="telegram_token" type="text" class="form-control"
                                                       value="{$settings['telegram_token']}">
                                            </div>
                                            <div class="col-auto">
                                                <button class="btn btn-primary"
                                                        hx-post="/admin/setting/im/set_webhook/telegram" hx-swap="none"
                                                        hx-vals='js:{
                                                            bot_token: document.getElementById("telegram_token").value
                                                        }'>
                                                    Set Webhook
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Webhook Token</label>
                                            <div class="col">
                                                <input id="telegram_webhook_token" type="text" class="form-control"
                                                       value="{$settings['telegram_webhook_token']}" disabled>
                                            </div>
                                            <div class="col-auto">
                                                <button class="btn btn-primary"
                                                        hx-post="/admin/setting/im/reset_webhook_token/telegram" hx-swap="none">
                                                    Reset Webhook Token
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Bot Account Username</label>
                                            <div class="col">
                                                <input id="telegram_bot" type="text" class="form-control"
                                                       value="{$settings['telegram_bot']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Group ID</label>
                                            <div class="col">
                                                <input id="telegram_chatid" type="text" class="form-control"
                                                       value="{$settings['telegram_chatid']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Telegram group notify
                                            </label>
                                            <div class="col">
                                                <select id="enable_telegram_group_notify" class="col form-select"
                                                        value="{$settings['enable_telegram_group_notify']}">
                                                    <option value="0" {if ! $settings['enable_telegram_group_notify']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_telegram_group_notify']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
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
                                            <label class="form-label col-3 col-form-label">Telegram Chat ID(Group/DM)</label>
                                            <input type="text" class="form-control" id="telegram_chat_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button class="btn btn-primary"
                                                        hx-post="/admin/setting/test/telegram" hx-swap="none"
                                                        hx-vals='js:{ telegram_chat_id: document.getElementById("telegram_chat_id").value }'>
                                                        Send Test Message
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
                                            <label class="form-label col-3 col-form-label">Discord Channel ID</label>
                                            <div class="col">
                                                <input id="discord_channel_id" type="text" class="form-control"
                                                       value="{$settings['discord_channel_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Discord channel notify
                                            </label>
                                            <div class="col">
                                                <select id="enable_discord_channel_notify" class="col form-select"
                                                        value="{$settings['enable_discord_channel_notify']}">
                                                    <option value="0" {if ! $settings['enable_discord_channel_notify']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_discord_channel_notify']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Discord User ID/Channel ID</label>
                                            <input type="text" class="form-control" id="discord_channel_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button class="btn btn-primary"
                                                        hx-post="/admin/setting/test/discord" hx-swap="none"
                                                        hx-vals='js:{ discord_channel_id: document.getElementById("discord_channel_id").value }'>
                                                        Send Test Message
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
                                            <label class="form-label col-3 col-form-label">Slack Channel ID</label>
                                            <div class="col">
                                                <input id="slack_channel_id" type="text" class="form-control"
                                                       value="{$settings['slack_channel_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Slack channel notify
                                            </label>
                                            <div class="col">
                                                <select id="enable_slack_channel_notify" class="col form-select"
                                                        value="{$settings['enable_slack_channel_notify']}">
                                                    <option value="0" {if ! $settings['enable_slack_channel_notify']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_slack_channel_notify']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Slack User ID/Channel ID</label>
                                            <input type="text" class="form-control" id="slack_channel_id" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button class="btn btn-primary"
                                                        hx-post="/admin/setting/test/slack" hx-swap="none"
                                                        hx-vals='js:{ slack_channel_id: document.getElementById("slack_channel_id").value }'>
                                                        Send Test Message
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
        </script>

        {include file='admin/footer.tpl'}
