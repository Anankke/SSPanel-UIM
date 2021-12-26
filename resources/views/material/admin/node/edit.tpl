{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">编辑节点 #{$node->id}</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">
                <form id="main_form">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="name">节点名称</label>
                                    <input class="form-control maxwidth-edit" id="name" name="name" type="text"
                                           value="{$node->name}">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="server">节点地址</label>
                                    <input class="form-control maxwidth-edit" id="server" name="server" type="text"
                                           value="{$node->server}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>如果填写为域名，“节点IP”会自动设置为解析的IP</p>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="server">节点IP</label>
                                    <input class="form-control maxwidth-edit" id="node_ip" name="node_ip" type="text"
                                           value="{$node->node_ip}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>如果“节点地址”填写为域名，则此处的值会被忽视
                                    </p>
                                </div>
                                <div class="form-group">
                                    <dev id="custom_config"></dev>
                                    <p class="form-control-guide"><i class="material-icons">info</i>请参考 <a href="//wiki.sspanel.org/#/setup-custom-config" target="_blank">wiki.sspanel.org/#/setup-custom-config</a> 进行配置
                                    </p>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="rate">流量比例</label>
                                    <input class="form-control maxwidth-edit" id="rate" name="rate" type="text"
                                           value="{$node->traffic_rate}">
                                </div>
                                <div class="form-group form-group-label">
                                    <label for="mu_only">
                                        <label class="floating-label" for="sort">单端口多用户启用</label>
                                        <select id="mu_only" class="form-control maxwidth-edit" name="is_multi_user">
                                            <option value="-1" {if $node->mu_only==-1}selected{/if}>只启用普通端口</option>
                                            <option value="1" {if $node->mu_only==1}selected{/if}>只启用单端口多用户</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="form-group form-group-label">
                                    <div class="checkbox switch">
                                        <label for="type">
                                            <input {if $node->type==1}checked{/if} class="access-hide" id="type"
                                                   name="type" type="checkbox"><span class="switch-toggle"></span>是否显示
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="status">节点状态</label>
                                    <input class="form-control maxwidth-edit" id="status" name="status" type="text"
                                           value="{$node->status}">
                                </div>
                                <div class="form-group form-group-label">
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="sort">节点类型</label>
                                        <select id="sort" class="form-control maxwidth-edit" name="sort">
                                            <option value="0" {if $node->sort==0}selected{/if}>Shadowsocks</option>
                                            <option value="1" {if $node->sort==1}selected{/if}>Shadowsocksr</option>
                                            <option value="9" {if $node->sort==9}selected{/if}>Shadowsocksr 单端口多用户（旧）</option>
                                            <option value="11" {if $node->sort==11}selected{/if}>V2Ray</option>
                                            <option value="14" {if $node->sort==14}selected{/if}>Trojan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="info">节点描述</label>
                                    <input class="form-control maxwidth-edit" id="info" name="info" type="text"
                                           value="{$node->info}">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="class">节点等级</label>
                                    <input class="form-control maxwidth-edit" id="class" name="class" type="text"
                                           value="{$node->node_class}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>不分级请填0，分级填写相应数字</p>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="group">节点群组</label>
                                    <input class="form-control maxwidth-edit" id="group" name="group" type="text"
                                           value="{$node->node_group}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>分组为数字，不分组请填0</p>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="node_bandwidth_limit">节点流量上限（GB）</label>
                                    <input class="form-control maxwidth-edit" id="node_bandwidth_limit"
                                           name="node_bandwidth_limit" type="text"
                                           value="{$node->node_bandwidth_limit/1024/1024/1024}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>不设上限请填0</p>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="bandwidthlimit_resetday">节点流量上限清空日</label>
                                    <input class="form-control maxwidth-edit" id="bandwidthlimit_resetday"
                                           name="bandwidthlimit_resetday" type="text"
                                           value="{$node->bandwidthlimit_resetday}">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="node_speedlimit">节点限速（Mbps）</label>
                                    <input class="form-control maxwidth-edit" id="node_speedlimit"
                                           name="node_speedlimit" type="text" value="{$node->node_speedlimit}">
                                    <p class="form-control-guide"><i class="material-icons">info</i>不限速填0，对于每个用户端口生效</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-10 col-md-push-1">
                                            <button id="submit" type="submit"
                                                    class="btn btn-block btn-brand waves-attach waves-light">修改
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                {include file='dialog.tpl'}
        </div>
    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    const container = document.getElementById('custom_config');
    var options = {
        mode: 'tree'
    };
    const editor = new JSONEditor(container, options);
    editor.set({$node->custom_config})
{literal}
    $('#main_form').validate({
        ignore: ".jsoneditor *",
        rules: {
            name: {required: true},
            server: {required: true},
            rate: {required: true},
            info: {required: true},
            group: {required: true},
            status: {required: true},
            node_speedlimit: {required: true},
            sort: {required: true},
            node_bandwidth_limit: {required: true},
            bandwidthlimit_resetday: {required: true}
        },
        submitHandler: () => {
            if ($$.getElementById('type').checked) {
                var type = 1;
            } else {
                var type = 0;
            }
{/literal}
            $.ajax({
                type: "PUT",
                url: "/admin/node/{$node->id}",
                dataType: "json",
{literal}
                data: {
                    name: $$getValue('name'),
                    server: $$getValue('server'),
                    custom_config: editor.get(),
                    node_ip: $$getValue('node_ip'),
                    rate: $$getValue('rate'),
                    info: $$getValue('info'),
                    type,
                    group: $$getValue('group'),
                    status: $$getValue('status'),
                    sort: $$getValue('sort'),
                    node_speedlimit: $$getValue('node_speedlimit'),
                    class: $$getValue('class'),
                    node_bandwidth_limit: $$getValue('node_bandwidth_limit'),
                    bandwidthlimit_resetday: $$getValue('bandwidthlimit_resetday')
{/literal},
                    mu_only: $$getValue('mu_only')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});

                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
{literal}
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${jqXHR.status}`;
                }
            });
        }
    });
{/literal}
</script>
