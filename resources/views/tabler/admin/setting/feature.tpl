{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">其他设置</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">设置站点的其他设置</span>
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
                            <a href="#display" class="nav-link active" data-bs-toggle="tab">功能显示</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="display">
                            <div class="card-body">
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">显示节点流媒体解锁情况</label>
                                    <div class="col">
                                        <select id="display_media" class="col form-select" value="{$settings['display_media']}">
                                            <option value="0" {if $settings['display_media'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['display_media']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">显示用户订阅记录</label>
                                    <div class="col">
                                        <select id="display_subscribe_log" class="col form-select" value="{$settings['display_subscribe_log']}">
                                            <option value="0" {if $settings['display_subscribe_log'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['display_subscribe_log']}selected{/if}>开启</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label class="form-label col-3 col-form-label">显示用户审计记录</label>
                                    <div class="col">
                                        <select id="display_detect_log" class="col form-select" value="{$settings['display_detect_log']}">
                                            <option value="0" {if $settings['display_detect_log'] === false}selected{/if}>关闭</option>
                                            <option value="1" {if $settings['display_detect_log']}selected{/if}>开启</option>
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
    $("#save-setting").click(function() {
        $.ajax({
            url: '/admin/setting/feature',
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
