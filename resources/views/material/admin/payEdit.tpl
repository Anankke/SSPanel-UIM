{include file='admin/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">设置支付宝/微信COOKIE</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">


                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="form-group form-group-label">
                                <div class="checkbox switch">
                                    <label for="AliPay_Status">
                                        <input {if $payConfig['AliPay_Status']==1}checked{/if} class="access-hide"
                                               id="AliPay_Status" type="checkbox">
                                        <span class="switch-toggle"></span>开启支付宝支付
                                    </label>
                                </div>
                            </div>

                            <div class="form-group form-group-label">
                                <div class="checkbox switch">
                                    <label for="WxPay_Status">
                                        <input {if $payConfig['WxPay_Status']==1}checked{/if} class="access-hide"
                                               id="WxPay_Status" type="checkbox">
                                        <span class="switch-toggle"></span>开启微信支付
                                    </label>
                                </div>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">失效通知邮箱</label>
                                <input class="form-control maxwidth-edit" id="Notice_EMail" type="text"
                                       value="{$payConfig['Notice_EMail']}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">规定金额设定</label>
                                <input class="form-control maxwidth-edit" id="Pay_Price" type="text"
                                       value="{$payConfig['Pay_Price']}">
                                <p class="form-control-guide"><i class="material-icons">info</i>不设定则无需输入，英文“|”分割，必须大于2
                                </p>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="amount">支付宝二维码</label>
                                <input class="form-control maxwidth-edit" id="AliPay_QRcode" type="text"
                                       value="{$payConfig['AliPay_QRcode']}">
                                <p class="form-control-guide"><i class="material-icons">info</i>金额设定后需要英文“|”分割</p>
                            </div>


                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">支付宝COOKIE</label>
                                <input class="form-control maxwidth-edit" id="AliPay_Cookie" type="text"
                                       value='{$payConfig['AliPay_Cookie']}'>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信登录地址</label>
                                <input class="form-control maxwidth-edit" id="WxPay_Url" type="text"
                                       value="{$payConfig['WxPay_Url']}">
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信二维码</label>
                                <input class="form-control maxwidth-edit" id="WxPay_QRcode" type="text"
                                       value="{$payConfig['WxPay_QRcode']}">
                                <p class="form-control-guide"><i class="material-icons">info</i>金额设定后需要英文“|”分割</p>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信COOKIE</label>
                                <input class="form-control maxwidth-edit" id="WxPay_Cookie" type="text"
                                       value="{$payConfig['WxPay_Cookie']}">
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
                                                class="btn btn-block btn-brand waves-attach waves-light">确定
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {include file='dialog.tpl'}

        </div>


    </div>
</main>


{include file='admin/footer.tpl'}


<script>

    window.addEventListener('load', () => {
        function submit() {
            if ($$.getElementById('AliPay_Status').checked) {
                var AliPay_Status = 1;
            } else {
                var AliPay_Status = 0;
            }
            if ($$.getElementById('WxPay_Status').checked) {
                var WxPay_Status = 1
            } else {
                var WxPay_Status = 0
            }
            ;
            $.ajax({
                type: "POST",
                url: "/admin/saveConfig",
                dataType: "json",
                data: {
                    AliPay_Status,
                    WxPay_Status,
                    Notice_EMail: $$getValue('Notice_EMail'),
                    AliPay_QRcode: $$getValue('AliPay_QRcode'),
                    AliPay_Cookie: $$getValue('AliPay_Cookie'),
                    WxPay_Url: $$getValue('WxPay_Url'),
                    WxPay_QRcode: $$getValue('WxPay_QRcode'),
                    WxPay_Cookie: $$getValue('WxPay_Cookie'),
                    Pay_Price: $$getValue('Pay_Price'),
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.reload()", {$config['jump_delay']});
                    } else {
                        $("#msg-error").hide(10);
                        $("#msg-error").show(100);
                        $$.getElementById('msg-error-p').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${ldelim}data.msg{rdelim} 发生错误了。`;
                }
            });
        }

        $("html").keydown(event => {
            if (event.keyCode == 13) {
                submit();
            }
        });

        $$.getElementById('submit').addEventListener('click', submit);

    })
</script>
