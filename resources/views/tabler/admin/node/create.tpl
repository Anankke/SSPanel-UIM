{include file='admin/header.tpl'}

<script src="//{$config['jsdelivr_url']}/npm/jsoneditor@latest/dist/jsoneditor.min.js"></script>
<link href="//{$config['jsdelivr_url']}/npm/jsoneditor@latest/dist/jsoneditor.min.css" rel="stylesheet" type="text/css">

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">创建节点</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">创建各类节点</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a id="create-node" href="#" class="btn btn-primary">
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
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">基础信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">名称</label>
                                <div class="col">
                                    <input id="name" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">连接地址</label>
                                <div class="col">
                                    <input id="server" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">流量倍率</label>
                                <div class="col">
                                    <input id="traffic_rate" type="text" class="form-control"
                                           value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">接入类型</label>
                                <div class="col">
                                    <select id="sort" class="col form-select">
                                        <option value="14">Trojan</option>
                                        <option value="11">Vmess</option>
                                        <option value="2">TUIC</option>
                                        <option value="1">Shadowsocks2022</option>
                                        <option value="0">Shadowsocks</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">自定义配置</label>
                                <div id="custom_config"></div>
                                <label class="form-label col-form-label">
                                    请参考
                                    <a href="https://docs.sspanel.io/docs/configuration/nodes" target="_blank">
                                        节点自定义配置文档
                                    </a>
                                    修改节点自定义配置
                                </label>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">显示此节点</span>
                                <span class="col-auto">
                                    <label class="form-check form-check-single form-switch">
                                        <input id="type" class="form-check-input" type="checkbox" checked="">
                                    </label>
                                </span>
                            </div>
                            <div class="hr-text">
                                <span>动态倍率</span>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">启用动态流量倍率</span>
                                <span class="col-auto">
                                    <label class="form-check form-check-single form-switch">
                                        <input id="is_dynamic_rate" class="form-check-input" type="checkbox" checked="">
                                    </label>
                                </span>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">动态流量倍率计算方式</label>
                                <div class="col">
                                    <select id="dynamic_rate_type" class="col form-select">
                                        <option value="0">Logistic</option>
                                        <option value="1">Linear</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最大倍率</label>
                                <div class="col">
                                    <input id="max_rate" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最大倍率时间（时）</label>
                                <div class="col">
                                    <input id="max_rate_time" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最小倍率</label>
                                <div class="col">
                                    <input id="min_rate" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最小倍率时间（时）</label>
                                <div class="col">
                                    <input id="min_rate_time" type="text" class="form-control" value="">
                                </div>
                                <label class="form-label col-form-label">
                                    最大倍率时间必须大于最小倍率时间，否则将不会生效
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">其他信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">等级</label>
                                <div class="col">
                                    <input id="node_class" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">组别</label>
                                <div class="col">
                                    <input id="node_group" type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>流量设置</span>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">可用流量 (GB)</label>
                                <div class="col">
                                    <input id="node_bandwidth_limit" type="text" class="form-control"
                                           value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">流量重置日</label>
                                <div class="col">
                                    <input id="bandwidthlimit_resetday" type="text" class="form-control"
                                           value="">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label required">速率限制 (Mbps)</label>
                                <div class="col">
                                    <input id="node_speedlimit" type="text" class="form-control"
                                           value="">
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
    const container = document.getElementById('custom_config');
    let options = {
        modes: ['code', 'tree'],
    };
    const editor = new JSONEditor(container, options);

    $("#create-node").click(function () {
        $.ajax({
            url: '/admin/node',
            type: 'POST',
            dataType: "json",
            data: {
                {foreach $update_field as $key}
                {$key}: $('#{$key}').val(),
                {/foreach}
                type: $("#type").is(":checked"),
                custom_config: JSON.stringify(editor.get()),
            },
            success: function (data) {
                if (data.ret === 1) {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                    window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });
</script>

{include file='admin/footer.tpl'}
