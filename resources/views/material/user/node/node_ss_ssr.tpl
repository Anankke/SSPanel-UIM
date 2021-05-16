{include file='user/header_info.tpl'}

{$ssr_prefer = URL::SSRCanConnect($user, $mu)}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">节点信息</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner">
            <div class="ui-card-wrap">
                <div class="row">

                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <p class="card-heading" align="center">注意！</p>
                                    <p align="center">配置文件以及二维码请勿泄露！</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <p align="center" class="card-heading">配置信息</p>
                                    <div class="tab-content">
                                        <nav class="tab-nav">
                                            <ul class="nav nav-list">
                                                <li {if $ssr_prefer}class="active"{/if}>
                                                    <a class="" data-toggle="tab" href="#ssr_info"><i class="icon icon-lg">airplanemode_active</i>&nbsp;ShadowsocksR</a>
                                                </li>
                                                <li {if !$ssr_prefer}class="active"{/if}>
                                                    <a class="" data-toggle="tab" href="#ss_info"><i class="icon icon-lg">flight_takeoff</i>&nbsp;Shadowsocks</a>
                                                </li>
                                            </ul>
                                        </nav>
                                        <div class="tab-pane fade {if $ssr_prefer}active in{/if}" id="ssr_info">
                                            {if URL::SSRCanConnect($user, $mu)}
                                                {$ssr_item = $node->getItem($user, $mu, 0)}
                                                <p align="center">
                                                    <a class="btn btn-subscription" type="button" href="{URL::getItemUrl($ssr_item, 0)}">点击直接添加</a>
                                                    <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getItemUrl($ssr_item, 0)}">拷贝SSR链接</button>
                                                </p>
                                                <hr/>
                                                <table align="center">
                                                    <tbody>
                                                        <tr>
                                                            <td>连接地址：</td>
                                                            <td>{$ssr_item['address']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>连接端口：</td>
                                                            <td>{$ssr_item['port']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>加密方式：</td>
                                                            <td>{$ssr_item['method']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>连接密码：</td>
                                                            <td>{$ssr_item['passwd']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>协议方式：</td>
                                                            <td>{$ssr_item['protocol']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>协议参数：</td>
                                                            <td>{$ssr_item['protocol_param']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>混淆方式：</td>
                                                            <td>{$ssr_item['obfs']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>混淆参数：</td>
                                                            <td>{$ssr_item['obfs_param']}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <hr/>
                                                <p align="center" class="card-heading">二维码</p>
                                                <div class="text-center">
                                                    <div id="ss-qr-n" class="qr-center"></div>
                                                </div>
                                                <hr/>
                                                <p align="center" class="card-heading">配置Json</p>
                                                <pre>{
                                                        "server": "{$ssr_item['address']}",
                                                        "local_address": "127.0.0.1",
                                                        "local_port": 1080,
                                                        "timeout": 300,
                                                        "workers": 1,
                                                        "server_port": {$ssr_item['port']},
                                                        "password": "{$ssr_item['passwd']}",
                                                        "method": "{$ssr_item['method']}",
                                                        "obfs": "{$ssr_item['obfs']}",
                                                        "obfs_param": "{$ssr_item['obfs_param']}",
                                                        "protocol": "{$ssr_item['protocol']}",
                                                        "protocol_param": "{$ssr_item['protocol_param']}"
                                                    }
                                               </pre>
                                            {else}
                                                <p>您好，您目前的 加密方式，混淆，或者协议设置在 ShadowsocksR 客户端下无法连接。请您选用 Shadowsocks
                                                    客户端来连接，或者到 资料编辑 页面修改后再来查看此处。</p>
                                                <p>同时, ShadowsocksR 单端口多用户的连接不受您设置的影响,您可以在此使用相应的客户端进行连接~</p>
                                            {/if}
                                        </div>
                                        <div class="tab-pane fade {if !$ssr_prefer}active in{/if}" id="ss_info">
                                            {if URL::SSCanConnect($user, $mu)}
                                                {$ss_item = $node->getItem($user, $mu, 1)}
                                                <p align="center">链接以及二维码为 SIP002 格式</p>
                                                <p align="center">
                                                    <a class="btn btn-subscription" type="button" href="{URL::getItemUrl($ss_item, 1)}">点击直接添加</a>
                                                    <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getItemUrl($ss_item, 1)}">拷贝SS链接</button>
                                                </p>
                                                <hr/>
                                                <table align="center">
                                                    <tbody>
                                                        <tr>
                                                            <td>连接地址：</td>
                                                            <td>{$ss_item['address']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>连接端口：</td>
                                                            <td>{$ss_item['port']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>加密方式：</td>
                                                            <td>{$ss_item['method']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>连接密码：</td>
                                                            <td>{$ss_item['passwd']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>混淆方式：</td>
                                                            <td>{$ss_item['obfs']}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>混淆参数：</td>
                                                            <td>{$ss_item['obfs_param']}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <hr/>
                                                <p align="center" class="card-heading">二维码</p>
                                                <div class="text-center">
                                                    <div id="ss-qr" class="qr-center"></div>
                                                </div>
                                                <hr/>
                                                <p align="center" class="card-heading">配置Json</p>
                                                <pre>{
                                                        "server": "{$ss_item['address']}",
                                                        "local_address": "127.0.0.1",
                                                        "local_port": 1080,
                                                        "timeout": 300,
                                                        "workers": 1,
                                                        "server_port": {$ss_item['port']},
                                                        "password": "{$ss_item['passwd']}",
                                                        "method": "{$ss_item['method']}",
                                                        "obfs": "{$ss_item['obfs']}",
                                                        "obfs_param": "{$ss_item['obfs_param']}",
                                                        "protocol": "{$ss_item['protocol']}",
                                                        "protocol_param": "{$ss_item['protocol_param']}"
                                                    }
                                                </pre>
                                            {else}
                                                <p>您好，您目前的 加密方式，混淆，或者协议设置在 Shadowsocks 客户端下无法连接。请您选用 ShadowsocksR
                                                    客户端来连接，或者到 资料编辑 页面修改后再来查看此处。</p>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {include file='dialog.tpl'}
                </div>
            </div>
        </section>
    </div>
</main>

{include file='user/footer.tpl'}

<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
<script>
	$(function(){
		new ClipboardJS('.copy-text');
	});
	$(".copy-text").click(function () {
		$("#result").modal();
		$("#msg").html("已复制到您的剪贴板，请您继续接下来的操作。");
	});
</script>
<script>
    $(function () {
        new ClipboardJS('.copy-text');
    });
    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已拷贝订阅链接，请您继续接下来的操作'
    });
    {if URL::SSCanConnect($user, $mu)}
    var text_qrcode = '{URL::getItemUrl($ss_item, 1)}';
    var qrcode1 = new QRCode(document.getElementById("ss-qr"), {
            correctLevel: 3
        });
    qrcode1.clear();
    qrcode1.makeCode(text_qrcode);
    {/if}
    {if URL::SSRCanConnect($user, $mu)}
    var text_qrcode2 = '{URL::getItemUrl($ssr_item, 0)}';
    var qrcode3 = new QRCode(document.getElementById("ss-qr-n"), {
        correctLevel: 3
    });
    qrcode3.clear();
    qrcode3.makeCode(text_qrcode2);
    {/if}
</script>
