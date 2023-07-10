{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">邮件设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">设置站点的邮件系统</span>
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
                            <a href="#email" class="nav-link active" data-bs-toggle="tab">邮件设置</a>
                        </li>
                        <li class="nav-item">
                            <a href="#limit" class="nav-link" data-bs-toggle="tab">发送限制</a>
                        </li>
                        <li class="nav-item">
                            <a href="#smtp" class="nav-link" data-bs-toggle="tab">SMTP</a>
                        </li>
                        <li class="nav-item">
                            <a href="#sendgrid" class="nav-link" data-bs-toggle="tab">Sendgrid</a>
                        </li>
                        <li class="nav-item">
                            <a href="#mailgun" class="nav-link" data-bs-toggle="tab">Mailgun</a>
                        </li>
                        <li class="nav-item">
                            <a href="#postal" class="nav-link" data-bs-toggle="tab">Postal</a>
                        </li>
                        <li class="nav-item">
                            <a href="#ses" class="nav-link" data-bs-toggle="tab">AWS SES</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="email">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邮件服务提供商</label>
                                    <div class="col">
                                        <select id="email_driver" class="col form-select" value="{$settings['email_driver']}">
                                            <option value="none" {if $settings['email_driver'] === "none"}selected{/if}>none</option>
                                            <option value="smtp" {if $settings['email_driver'] === "smtp"}selected{/if}>smtp</option>
                                            <option value="sendgrid" {if $settings['email_driver'] === "sendgrid"}selected{/if}>sendgrid</option>
                                            <option value="mailgun" {if $settings['email_driver'] === "mailgun"}selected{/if}>mailgun</option>
                                            <option value="postal" {if $settings['email_driver'] === "postal"}selected{/if}>postal</option>
                                            <option value="ses" {if $settings['email_driver'] === "ses"}selected{/if}>ses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">测试邮件接收地址</label>
                                    <input type="text" class="form-control" id="recipient" value="">
                                    <div class="row my-3">
                                        <div class="col">
                                            <button id="test-email" class="btn btn-primary">发送测试邮件</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="limit">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邮箱验证码有效期（秒）</label>
                                    <div class="col">
                                        <input id="email_verify_code_ttl" type="text" class="form-control"
                                               value="{$settings['email_verify_code_ttl']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邮箱重设密码链接有效期（秒）</label>
                                    <div class="col">
                                        <input id="email_password_reset_ttl" type="text" class="form-control"
                                               value="{$settings['email_password_reset_ttl']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">单个IP每小时可请求的发信次数</label>
                                    <div class="col">
                                        <input id="email_request_ip_limit" type="text" class="form-control"
                                               value="{$settings['email_request_ip_limit']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">单个邮箱地址每小时可请求的发信次数</label>
                                    <div class="col">
                                        <input id="email_request_address_limit" type="text" class="form-control"
                                               value="{$settings['email_request_address_limit']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="smtp">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 主机地址</label>
                                    <div class="col">
                                        <input id="smtp_host" type="text" class="form-control" value="{$settings['smtp_host']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 用户名</label>
                                    <div class="col">
                                        <input id="smtp_username" type="text" class="form-control" value="{$settings['smtp_username']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 密码</label>
                                    <div class="col">
                                        <input id="smtp_password" type="text" class="form-control" value="{$settings['smtp_password']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 端口</label>
                                    <div class="col">
                                        <select id="smtp_port" class="col form-select" value="{$settings['smtp_port']}">
                                            <option value="465" {if $settings['smtp_port'] === "465"}selected{/if}>465</option>
                                            <option value="587" {if $settings['smtp_port'] === "587"}selected{/if}>587</option>
                                            <option value="443" {if $settings['smtp_port'] === "443"}selected{/if}>443</option>
                                            <option value="80" {if $settings['smtp_port'] === "80"}selected{/if}>80</option>
                                            <option value="2525" {if $settings['smtp_port'] === "2525"}selected{/if}>2525</option>
                                            <option value="25" {if $settings['smtp_port'] === "25"}selected{/if}>25</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 发件人名称</label>
                                    <div class="col">
                                        <input id="smtp_name" type="text" class="form-control" value="{$settings['smtp_name']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">SMTP 发信地址</label>
                                    <div class="col">
                                        <input id="smtp_sender" type="text" class="form-control" value="{$settings['smtp_sender']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">是否使用 TLS/SSL</label>
                                    <div class="col">
                                    <select id="smtp_ssl" class="col form-select" value="{$settings['smtp_ssl']}">
                                        <option value="1" {if $settings['smtp_ssl']}selected{/if}>开启</option>
                                        <option value="0" {if $settings['smtp_ssl'] === false}selected{/if}>关闭</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">客户邮件副本接收邮箱</label>
                                    <div class="col">
                                        <input id="smtp_bbc" type="text" class="form-control" value="{$settings['smtp_bbc']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="sendgrid">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Sendgrid 密钥</label>
                                    <div class="col">
                                        <input id="sendgrid_key" type="text" class="form-control" value="{$settings['sendgrid_key']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Sendgrid 发信地址</label>
                                    <div class="col">
                                        <input id="sendgrid_sender" type="text" class="form-control" value="{$settings['sendgrid_sender']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Sendgrid 发件人名称</label>
                                    <div class="col">
                                        <input id="sendgrid_name" type="text" class="form-control" value="{$settings['sendgrid_name']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="mailgun">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Mailgun 密钥</label>
                                    <div class="col">
                                        <input id="mailgun_key" type="text" class="form-control" value="{$settings['mailgun_key']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Mailgun 域名</label>
                                    <div class="col">
                                        <input id="mailgun_domain" type="text" class="form-control" value="{$settings['mailgun_domain']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Mailgun 发信地址</label>
                                    <div class="col">
                                        <input id="mailgun_sender" type="text" class="form-control" value="{$settings['mailgun_sender']}">
                                    </div>
                                </div>
                                 <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Mailgun 发信人名称</label>
                                    <div class="col">
                                        <input id="mailgun_sender_name" type="text" class="form-control" value="{$settings['mailgun_sender_name']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="postal">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Postal API地址</label>
                                    <div class="col">
                                        <input id="postal_host" type="text" class="form-control" value="{$settings['postal_host']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Postal API密钥</label>
                                    <div class="col">
                                        <input id="postal_key" type="text" class="form-control" value="{$settings['postal_key']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Postal发件邮箱</label>
                                    <div class="col">
                                        <input id="postal_sender" type="text" class="form-control" value="{$settings['postal_sender']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Postal发件人名称</label>
                                    <div class="col">
                                        <input id="postal_name" type="text" class="form-control" value="{$settings['postal_name']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="ses">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">AWS 密钥 ID</label>
                                    <div class="col">
                                        <input id="aws_access_key_id" type="text" class="form-control" value="{$settings['aws_access_key_id']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">AWS 密钥</label>
                                    <div class="col">
                                        <input id="aws_secret_access_key" type="text" class="form-control" value="{$settings['aws_secret_access_key']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">AWS 区域</label>
                                    <div class="col">
                                        <input id="aws_region" type="text" class="form-control" value="{$settings['aws_region']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">AWS SES 发信地址</label>
                                    <div class="col">
                                        <input id="aws_ses_sender" type="text" class="form-control" value="{$settings['aws_ses_sender']}">
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
            url: '/admin/setting/email',
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

    $("#test-email").click(function() {
        $.ajax({
            url: '/admin/setting/test_email',
            type: 'POST',
            dataType: "json",
            data: {
                recipient: $('#recipient').val(),
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
