{include file='admin/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">客服设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">设置站点的客服系统</span>
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
                            <a href="#support" class="nav-link active" data-bs-toggle="tab">网页客服系统</a>
                        </li>
                        <li class="nav-item">
                            <a href="#admin_contact" class="nav-link" data-bs-toggle="tab">管理员联络方式</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="support">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">客服系统提供商</label>
                                    <div class="col">
                                        <select id="live_chat" class="col form-select" value="{$settings['live_chat']}">
                                            <option value="none" {if $settings['live_chat'] == "none"}selected{/if}>无</option>
                                            <option value="tawk" {if $settings['live_chat'] == "tawk"}selected{/if}>Tawk</option>
                                            <option value="crisp" {if $settings['live_chat'] == "crisp"}selected{/if}>Crisp</option>
                                            <option value="livechat" {if $settings['live_chat'] == "livechat"}selected{/if}>LiveChat</option>
                                            <option value="mylivechat" {if $settings['live_chat'] == "mylivechat"}selected{/if}>MyLiveChat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Tawk ID</label>
                                    <div class="col">
                                        <input id="tawk_id" type="text" class="form-control" value="{$settings['tawk_id']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">Crisp ID</label>
                                    <div class="col">
                                        <input id="crisp_id" type="text" class="form-control" value="{$settings['crisp_id']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">LiveChat ID</label>
                                    <div class="col">
                                        <input id="livechat_id" type="text" class="form-control" value="{$settings['livechat_id']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">MyLiveChat ID</label>
                                    <div class="col">
                                        <input id="mylivechat_id" type="text" class="form-control" value="{$settings['mylivechat_id']}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="admin_contact">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">是否显示管理员联系方式</label>
                                    <div class="col">
                                        <select id="enable_admin_contact" class="col form-select" value="{$settings['enable_admin_contact']}">
                                            <option value="0" {if $settings['enable_admin_contact'] == false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['enable_admin_contact'] == true}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">联系方式一</label>
                                    <div class="col">
                                        <input id="admin_contact1" type="text" class="form-control" value="{$settings['admin_contact1']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">联系方式二</label>
                                    <div class="col">
                                        <input id="admin_contact2" type="text" class="form-control" value="{$settings['admin_contact2']}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">联系方式三</label>
                                    <div class="col">
                                        <input id="admin_contact3" type="text" class="form-control" value="{$settings['admin_contact3']}">
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
            url: '/admin/setting/support',
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