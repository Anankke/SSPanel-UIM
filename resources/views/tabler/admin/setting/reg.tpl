{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">注册设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">管理站点的注册设置</span>
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
                            <a href="#reg" class="nav-link active" data-bs-toggle="tab">注册设置</a>
                        </li>
                        <li class="nav-item">
                            <a href="#default_value" class="nav-link" data-bs-toggle="tab">默认值</a>
                        </li>
                        <li class="nav-item">
                            <a href="#limit" class="nav-link" data-bs-toggle="tab">账户限制</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="reg">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册模式</label>
                                    <div class="col">
                                        <select id="reg_mode" class="col form-select" value="{$settings['reg_mode']}">
                                            <option value="close" {if $settings['reg_mode'] === 'close'}selected{/if}>关闭注册</option>
                                            <option value="open" {if $settings['reg_mode'] === 'open'}selected{/if}>公开注册</option>
                                            <option value="invite" {if $settings['reg_mode'] === 'invite'}selected{/if}>仅限用户邀请注册</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邮箱验证</label>
                                    <div class="col">
                                        <select id="reg_email_verify" class="col form-select" value="{$settings['reg_email_verify']}">
                                            <option value="0" {if $settings['reg_email_verify'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['reg_email_verify']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">默认接收每日用量邮件推送</label>
                                    <div class="col">
                                        <select id="sign_up_for_daily_report" class="col form-select" value="{$settings['sign_up_for_daily_report']}">
                                            <option value="0" {if $settings['sign_up_for_daily_report'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['sign_up_for_daily_report']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">是否要求用户输入IM联系方式</label>
                                    <div class="col">
                                        <select id="enable_reg_im" class="col form-select" value="{$settings['enable_reg_im']}">
                                            <option value="0" {if $settings['enable_reg_im'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['enable_reg_im']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="default_value">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册时随机分配到的分组，多个分组请用英文半角逗号分隔</label>
                                    <div class="col">
                                        <input id="random_group" type="text" class="form-control" value="{$settings['random_group']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">用户端口池最小值，设为 0 时用户不会被分配端口</label>
                                    <div class="col">
                                        <input id="min_port" type="text" class="form-control" value="{$settings['min_port']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">用户端口池最大值，设为 0 时用户不会被分配端口</label>
                                    <div class="col">
                                        <input id="max_port" type="text" class="form-control" value="{$settings['max_port']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册时赠送的流量（GB）</label>
                                    <div class="col">
                                        <input id="sign_up_for_free_traffic" type="text" class="form-control" value="{$settings['sign_up_for_free_traffic']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">免费用戶的流量重置日，设为 0 时不重置</label>
                                    <div class="col">
                                        <input id="free_user_reset_day" type="text" class="form-control" value="{$settings['free_user_reset_day']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">需要重置的免费流量，设为 0 时不重置</label>
                                    <div class="col">
                                        <input id="free_user_reset_bandwidth" type="text" class="form-control" value="{$settings['free_user_reset_bandwidth']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册时设定的账户有效期（天）</label>
                                    <div class="col">
                                        <input id="sign_up_for_free_time" type="text" class="form-control" value="{$settings['sign_up_for_free_time']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册时设定的等级</label>
                                    <div class="col">
                                        <input id="sign_up_for_class" type="text" class="form-control" value="{$settings['sign_up_for_class']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">注册时设定的等级过期时间（天）</label>
                                    <div class="col">
                                        <input id="sign_up_for_class_time" type="text" class="form-control" value="{$settings['sign_up_for_class_time']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">默认加密</label>
                                    <div class="col">
                                        <input id="sign_up_for_method" type="text" class="form-control" value="{$settings['sign_up_for_method']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="limit">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邀请链接使用次数限制</label>
                                    <div class="col">
                                        <input id="sign_up_for_invitation_codes" type="text" class="form-control" value="{$settings['sign_up_for_invitation_codes']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">连接 IP 限制</label>
                                    <div class="col">
                                        <input id="connection_ip_limit" type="text" class="form-control" value="{$settings['connection_ip_limit']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">使用速率限制</label>
                                    <div class="col">
                                        <input id="connection_rate_limit" type="text" class="form-control" value="{$settings['connection_rate_limit']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">禁止访问的 IP 列表</label>
                                    <div class="col">
                                        <input id="reg_forbidden_ip" type="text" class="form-control" value="{$settings['reg_forbidden_ip']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">禁止访问的端口列表</label>
                                    <div class="col">
                                        <input id="reg_forbidden_port" type="text" class="form-control" value="{$settings['reg_forbidden_port']}">
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
            url: '/admin/setting/reg',
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
