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
                                <input class="form-control" id="Notice_EMail" type="text"
                                       value="{$payConfig['Notice_EMail']}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">规定金额设定（不设定则无需输入，英文“|”分割，必须大于2）</label>
                                <input class="form-control" id="Pay_Price" type="text"
                                       value="{$payConfig['Pay_Price']}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="amount">支付宝二维码（金额设定后需要英文“|”分割）</label>
                                <input class="form-control" id="AliPay_QRcode" type="text"
                                       value="{$payConfig['AliPay_QRcode']}">
                            </div>


                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">支付宝COOKIE</label>
                                <input class="form-control" id="AliPay_Cookie" type="text"
                                       value='{$payConfig['AliPay_Cookie']}'>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信登录地址</label>
                                <input class="form-control" id="WxPay_Url" type="text"
                                       value="{$payConfig['WxPay_Url']}">
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信二维码（金额设定后需要英文“|”分割）</label>
                                <input class="form-control" id="WxPay_QRcode" type="text"
                                       value="{$payConfig['WxPay_QRcode']}">
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">微信COOKIE</label>
                                <input class="form-control" id="WxPay_Cookie" type="text"
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
    $(document).ready(function () {
        function submit() {
            if (document.getElementById('AliPay_Status').checked) var AliPay_Status = 1;
            else var AliPay_Status = 0;
            if (document.getElementById('WxPay_Status').checked) var WxPay_Status = 1;
            else var WxPay_Status = 0;
            $.ajax({
                type: "POST",
                url: "/admin/saveConfig",
                dataType: "json",
                data: {
                    AliPay_Status: AliPay_Status,
                    WxPay_Status: WxPay_Status,
                    Notice_EMail: $("#Notice_EMail").val(),
                    AliPay_QRcode: $("#AliPay_QRcode").val(),
                    AliPay_Cookie: $("#AliPay_Cookie").val(),
                    WxPay_Url: $("#WxPay_Url").val(),
                    WxPay_QRcode: $("#WxPay_QRcode").val(),
                    WxPay_Cookie: $("#WxPay_Cookie").val(),
                    Pay_Price: $("#Pay_Price").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        window.setTimeout("location.reload()", {$config['jump_delay']});
                    } else {
                        $("#msg-error").hide(10);
                        $("#msg-error").show(100);
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
                    $("#msg").html(data.msg + "  发生错误了。");
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                login();
            }
        });
        $("#submit").click(function () {
            submit();
        });

    })
</script>