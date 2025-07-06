{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">订阅设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">设置站点的订阅系统</span>
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
                                    <a href="#sub" class="nav-link active" data-bs-toggle="tab">订阅设置</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="sub">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Shadowsocks Subscription
                                            </label>
                                            <div class="col">
                                                <select id="enable_ss_sub" class="col form-select"
                                                        value="{$settings['enable_ss_sub']}">
                                                    <option value="0" {if ! $settings['enable_ss_sub']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_ss_sub']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Vmess Subscription
                                            </label>
                                            <div class="col">
                                                <select id="enable_v2_sub" class="col form-select"
                                                        value="{$settings['enable_v2_sub']}">
                                                    <option value="0" {if ! $settings['enable_v2_sub']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_v2_sub']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                Enable Trojan Subscription
                                            </label>
                                            <div class="col">
                                                <select id="enable_trojan_sub" class="col form-select"
                                                        value="{$settings['enable_trojan_sub']}">
                                                    <option value="0" {if ! $settings['enable_trojan_sub']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1" {if $settings['enable_trojan_sub']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">
                                                修改账户登录密码时重置订阅地址
                                            </label>
                                            <div class="col">
                                                <select id="enable_forced_replacement" class="col form-select"
                                                        value="{$settings['enable_forced_replacement']}">
                                                    <option value="0"
                                                            {if ! $settings['enable_forced_replacement']}selected{/if}>
                                                        False
                                                    </option>
                                                    <option value="1"
                                                            {if $settings['enable_forced_replacement']}selected{/if}>
                                                        True
                                                    </option>
                                                </select>
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
                    url: '/admin/setting/sub',
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
