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
                                    <a href="#mailgun" class="nav-link" data-bs-toggle="tab">Mailgun</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sendgrid" class="nav-link" data-bs-toggle="tab">Sendgrid</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#postal" class="nav-link" data-bs-toggle="tab">Postal</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#ses" class="nav-link" data-bs-toggle="tab">AWS SES</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#mailchimp" class="nav-link" data-bs-toggle="tab">Mailchimp</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#alibabacloud" class="nav-link" data-bs-toggle="tab">AlibabaCloud DM</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#postmark" class="nav-link" data-bs-toggle="tab">Postmark</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#resend" class="nav-link" data-bs-toggle="tab">Resend</a>
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
                                                <select id="email_driver" class="col form-select"
                                                        value="{$settings['email_driver']}">
                                                    <option value="none"
                                                            {if $settings['email_driver'] === "none"}selected{/if}>
                                                        None
                                                    </option>
                                                    <option value="smtp"
                                                            {if $settings['email_driver'] === "smtp"}selected{/if}>
                                                        SMTP
                                                    </option>
                                                    <option value="mailgun"
                                                            {if $settings['email_driver'] === "mailgun"}selected{/if}>
                                                        Mailgun
                                                    </option>
                                                    <option value="sendgrid"
                                                            {if $settings['email_driver'] === "sendgrid"}selected{/if}>
                                                        Sendgrid
                                                    </option>
                                                    <option value="postal"
                                                            {if $settings['email_driver'] === "postal"}selected{/if}>
                                                        Postal
                                                    </option>
                                                    <option value="ses"
                                                            {if $settings['email_driver'] === "ses"}selected{/if}>
                                                        AWS SES
                                                    </option>
                                                    <option value="mailchimp"
                                                            {if $settings['email_driver'] === "mailchimp"}selected{/if}>
                                                        Mailchimp
                                                    </option>
                                                    <option value="alibabacloud"
                                                            {if $settings['email_driver'] === "alibabacloud"}selected{/if}>
                                                        AlibabaCloud DM
                                                    </option>
                                                    <option value="resend"
                                                            {if $settings['email_driver'] === "resend"}selected{/if}>
                                                        Resend
                                                    </option>
                                                    <option value="postmark"
                                                            {if $settings['email_driver'] === "postmark"}selected{/if}>
                                                        postmark
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">测试邮件接收地址</label>
                                            <input type="text" class="form-control" id="recipient" value="">
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button id="test-email" class="btn btn-primary">发送测试邮件
                                                    </button>
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
                                            <label class="form-label col-3 col-form-label">Host</label>
                                            <div class="col">
                                                <input id="smtp_host" type="text" class="form-control"
                                                       value="{$settings['smtp_host']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Username</label>
                                            <div class="col">
                                                <input id="smtp_username" type="text" class="form-control"
                                                       value="{$settings['smtp_username']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Password</label>
                                            <div class="col">
                                                <input id="smtp_password" type="text" class="form-control"
                                                       value="{$settings['smtp_password']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Port</label>
                                            <div class="col">
                                                <select id="smtp_port" class="col form-select"
                                                        value="{$settings['smtp_port']}">
                                                    <option value="465"
                                                            {if $settings['smtp_port'] === "465"}selected{/if}>465
                                                    </option>
                                                    <option value="587"
                                                            {if $settings['smtp_port'] === "587"}selected{/if}>587
                                                    </option>
                                                    <option value="443"
                                                            {if $settings['smtp_port'] === "443"}selected{/if}>443
                                                    </option>
                                                    <option value="80"
                                                            {if $settings['smtp_port'] === "80"}selected{/if}>80
                                                    </option>
                                                    <option value="2525"
                                                            {if $settings['smtp_port'] === "2525"}selected{/if}>2525
                                                    </option>
                                                    <option value="25"
                                                            {if $settings['smtp_port'] === "25"}selected{/if}>25
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Name</label>
                                            <div class="col">
                                                <input id="smtp_name" type="text" class="form-control"
                                                       value="{$settings['smtp_name']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sener</label>
                                            <div class="col">
                                                <input id="smtp_sender" type="text" class="form-control"
                                                       value="{$settings['smtp_sender']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Enable TLS/SSL</label>
                                            <div class="col">
                                                <select id="smtp_ssl" class="col form-select"
                                                        value="{$settings['smtp_ssl']}">
                                                    <option value="0" {if ! $settings['smtp_ssl']}selected{/if}>False
                                                    </option>
                                                    <option value="1" {if $settings['smtp_ssl']}selected{/if}>True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">BBC</label>
                                            <div class="col">
                                                <input id="smtp_bbc" type="text" class="form-control"
                                                       value="{$settings['smtp_bbc']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="mailgun">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="mailgun_key" type="text" class="form-control"
                                                       value="{$settings['mailgun_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Domain</label>
                                            <div class="col">
                                                <input id="mailgun_domain" type="text" class="form-control"
                                                       value="{$settings['mailgun_domain']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sender</label>
                                            <div class="col">
                                                <input id="mailgun_sender" type="text" class="form-control"
                                                       value="{$settings['mailgun_sender']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sender Name</label>
                                            <div class="col">
                                                <input id="mailgun_sender_name" type="text" class="form-control"
                                                       value="{$settings['mailgun_sender_name']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="sendgrid">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="sendgrid_key" type="text" class="form-control"
                                                       value="{$settings['sendgrid_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sender</label>
                                            <div class="col">
                                                <input id="sendgrid_sender" type="text" class="form-control"
                                                       value="{$settings['sendgrid_sender']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Name</label>
                                            <div class="col">
                                                <input id="sendgrid_name" type="text" class="form-control"
                                                       value="{$settings['sendgrid_name']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="postal">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Host</label>
                                            <div class="col">
                                                <input id="postal_host" type="text" class="form-control"
                                                       value="{$settings['postal_host']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="postal_key" type="text" class="form-control"
                                                       value="{$settings['postal_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sender</label>
                                            <div class="col">
                                                <input id="postal_sender" type="text" class="form-control"
                                                       value="{$settings['postal_sender']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Name</label>
                                            <div class="col">
                                                <input id="postal_name" type="text" class="form-control"
                                                       value="{$settings['postal_name']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="ses">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key ID</label>
                                            <div class="col">
                                                <input id="aws_ses_access_key_id" type="text" class="form-control"
                                                       value="{$settings['aws_ses_access_key_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key Secret</label>
                                            <div class="col">
                                                <input id="aws_ses_access_key_secret" type="text" class="form-control"
                                                       value="{$settings['aws_ses_access_key_secret']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Region</label>
                                            <div class="col">
                                                <input id="aws_ses_region" type="text" class="form-control"
                                                       value="{$settings['aws_ses_region']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Sender</label>
                                            <div class="col">
                                                <input id="aws_ses_sender" type="text" class="form-control"
                                                       value="{$settings['aws_ses_sender']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="mailchimp">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="mailchimp_key" type="text" class="form-control"
                                                       value="{$settings['mailchimp_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">From Email</label>
                                            <div class="col">
                                                <input id="mailchimp_from_email" type="text" class="form-control"
                                                       value="{$settings['mailchimp_from_email']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">From Name</label>
                                            <div class="col">
                                                <input id="mailchimp_from_name" type="text" class="form-control"
                                                       value="{$settings['mailchimp_from_name']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="alibabacloud">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key ID</label>
                                            <div class="col">
                                                <input id="alibabacloud_dm_access_key_id" type="text" class="form-control"
                                                       value="{$settings['alibabacloud_dm_access_key_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key Secret</label>
                                            <div class="col">
                                                <input id="alibabacloud_dm_access_key_secret" type="text" class="form-control"
                                                       value="{$settings['alibabacloud_dm_access_key_secret']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Endpoint</label>
                                            <div class="col">
                                                <input id="alibabacloud_dm_endpoint" type="text" class="form-control"
                                                       value="{$settings['alibabacloud_dm_endpoint']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Account Name</label>
                                            <div class="col">
                                                <input id="alibabacloud_dm_account_name" type="text" class="form-control"
                                                       value="{$settings['alibabacloud_dm_account_name']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">From Alias</label>
                                            <div class="col">
                                                <input id="alibabacloud_dm_from_alias" type="text" class="form-control"
                                                       value="{$settings['alibabacloud_dm_from_alias']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="postmark">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="postmark_key" type="text" class="form-control"
                                                       value="{$settings['postmark_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">发件人</label>
                                            <div class="col">
                                                <input id="postmark_sender" type="text" class="form-control"
                                                       value="{$settings['postmark_sender']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Stream</label>
                                            <div class="col">
                                                <input id="postmark_stream" type="text" class="form-control"
                                                       value="{$settings['postmark_stream']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="resend">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Api Key</label>
                                            <div class="col">
                                                <input id="resend_api_key" type="text" class="form-control"
                                                       value="{$settings['resend_api_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">From</label>
                                            <div class="col">
                                                <input id="resend_from" type="text" class="form-control"
                                                       value="{$settings['resend_from']}">
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
</div>

<script>
    $("#save-setting").click(function () {
        $.ajax({
            url: '/admin/setting/email',
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

    $("#test-email").click(function () {
        $.ajax({
            url: '/admin/setting/test/email',
            type: 'POST',
            dataType: "json",
            data: {
                recipient: $('#recipient').val(),
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