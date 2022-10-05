{include file='user/header_info.tpl'}

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
                                        <p align="center">
                                            <a class="btn btn-subscription" type="button" href="{$node['url']}">点击添加</a>
                                            <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$node['url']}">拷贝链接</button>
                                        </p>
                                        <hr/>
                                        <table align="center">
                                            <tbody>
                                                {foreach $node['info'] as $key => $value}
                                                <tr>
                                                    <td>{$key}</td>
                                                    <td>{$value}</td>
                                                </tr>
                                                {/foreach}
                                            </tbody>
                                        </table>
                                        <hr/>
                                        <p align="center" class="card-heading">二维码</p>
                                        <div class="text-center">
                                            <div id="qr-code" class="qr-center"></div>
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
    var text_qrcode = '{$node['url']}';
    var qrcode1 = new QRCode(document.getElementById("qr-code"), {
            correctLevel: 3
        });
    qrcode1.clear();
    qrcode1.makeCode(text_qrcode);
</script>
