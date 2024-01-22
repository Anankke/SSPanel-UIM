{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">邀请设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">管理站点的邀请设置</span>
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
                                    <a href="#invite" class="nav-link active" data-bs-toggle="tab">邀请设置</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#rebate" class="nav-link" data-bs-toggle="tab">返利</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="invite">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">被邀请者初始账户余额（元）</label>
                                            <div class="col">
                                                <input id="invite_reg_money_reward" type="text"
                                                       class="form-control"
                                                       value="{$settings['invite_reg_money_reward']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">邀请者流量奖励（GB）</label>
                                            <div class="col">
                                                <input id="invite_reg_traffic_reward" type="text"
                                                       class="form-control"
                                                       value="{$settings['invite_reg_traffic_reward']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="rebate">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">邀请模式</label>
                                            <div class="col">
                                                <select id="invite_mode" class="col form-select"
                                                        value="{$settings['invite_mode']}">
                                                    <option value="reg_only"
                                                            {if $settings['invite_mode'] === 'reg_only'}selected{/if}>
                                                        不返利
                                                    </option>
                                                    <option value="reward"
                                                            {if $settings['invite_mode'] === 'reward'}selected{/if}>
                                                        被邀请用户支付账单时返利
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">返利模式</label>
                                            <div class="col">
                                                <select id="invite_reward_mode" class="col form-select"
                                                        value="{$settings['invite_reward_mode']}">
                                                    <option value="reward_count"
                                                            {if $settings['invite_reward_mode'] === 'reward_count'}selected{/if}>
                                                        限制返利次数
                                                    </option>
                                                    <option value="reward_total"
                                                            {if $settings['invite_reward_mode'] === 'reward_total'}selected{/if}>
                                                        限制返利金额
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">返利比例，10% 填 0.1</label>
                                            <div class="col">
                                                <input id="invite_reward_rate" type="text" class="form-control"
                                                       value="{$settings['invite_reward_rate']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">返利次数限制</label>
                                            <div class="col">
                                                <input id="invite_reward_count_limit" type="text" class="form-control"
                                                       value="{$settings['invite_reward_count_limit']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">返利金额限制</label>
                                            <div class="col">
                                                <input id="invite_reward_total_limit" type="text" class="form-control"
                                                       value="{$settings['invite_reward_total_limit']}">
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
                    url: '/admin/setting/ref',
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
