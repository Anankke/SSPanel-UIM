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
                                    <label class="form-label col-3 col-form-label">使用邀请链接注册所能获得的余额奖励（元）</label>
                                    <div class="col">
                                        <input id="invitation_to_register_balance_reward" type="text" class="form-control" value="{$settings['invitation_to_register_balance_reward']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">使用邀请链接注册所能获得的流量奖励（GB）</label>
                                    <div class="col">
                                        <input id="invitation_to_register_traffic_reward" type="text" class="form-control" value="{$settings['invitation_to_register_traffic_reward']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="rebate">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">邀请模式</label>
                                    <div class="col">
                                        <select id="invitation_mode" class="col form-select" value="{$settings['invitation_mode']}">
                                            <option value="reg_only" {if $settings['invitation_mode'] === 'reg_only'}selected{/if}>
                                            不返利</option>
                                            <option value="after_paid" {if $settings['invitation_mode'] === 'after_paid'}selected{/if}>
                                            被邀请用户支付账单时返利</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">返利模式</label>
                                    <div class="col">
                                        <select id="invite_rebate_mode" class="col form-select" value="{$settings['invite_rebate_mode']}">
                                            <option value="continued" {if $settings['invite_rebate_mode'] === 'continued'}selected{/if}>
                                            持续返利</option>
                                            <option value="limit_frequency" {if $settings['invite_rebate_mode'] === 'limit_frequency'}selected{/if}>
                                            限制邀请人能从被邀请人身上获得的返利次数</option>
                                            <option value="limit_amount" {if $settings['invite_rebate_mode'] === 'limit_amount'}selected{/if}>
                                            限制邀请人能从被邀请人身上获得的返利金额</option>
                                            <option value="limit_time_range" {if $settings['invite_rebate_mode'] === 'limit_time_range'}selected{/if}>
                                            限制邀请人能从被邀请人身上获得返利的时间范围</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">返利比例，10% 填 0.1</label>
                                    <div class="col">
                                        <input id="rebate_ratio" type="text" class="form-control" value="{$settings['rebate_ratio']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">返利时间范围（天）</label>
                                    <div class="col">
                                        <input id="rebate_time_range_limit" type="text" class="form-control" value="{$settings['rebate_time_range_limit']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">返利次数</label>
                                    <div class="col">
                                        <input id="rebate_frequency_limit" type="text" class="form-control" value="{$settings['rebate_frequency_limit']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">返利金额限制</label>
                                    <div class="col">
                                        <input id="rebate_amount_limit" type="text" class="form-control" value="{$settings['rebate_amount_limit']}">
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
            url: '/admin/setting/ref',
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