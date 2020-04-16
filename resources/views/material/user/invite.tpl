{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">邀请</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="row">

                <div class="col-lg-6 col-md-6">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <p class="card-heading">说明</p>
                                    <p>您每邀请1位用户注册：</p>
                                    <p>您会获得<code>{$config['invite_gift']} G</code>流量奖励。</p>
                                    <p>对方将获得<code>{$config['invite_get_money']}</code>元奖励作为初始资金。</p>
                                    <p>对方充值时您还会获得对方充值金额的 <code>{$config['code_payback']} %</code> 的返利。</p>
                                    <p class="card-heading">已获得返利：<code>{$paybacks_sum}</code> 元</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {if $user->class!=0}

                    {if $user->invite_num!=-1}
                        <div class="col-lg-6 col-md-6">
                            <div class="card margin-bottom-no">
                                <div class="card-main">
                                    <div class="card-inner margin-bottom-no">
                                        <div class="card-inner margin-bottom-no">
                                            <div class="cardbtn-edit">
                                                <div class="card-heading">邀请链接</div>
                                                <div class="reset-flex"><span>重置链接</span><a
                                                            class="reset-link btn btn-brand-accent btn-flat"><i
                                                                class="icon">autorenew</i>&nbsp;</a></div>
                                            </div>
                                            <p>剩余可邀请次数：{if $user->invite_num<0}无限{else}
                                                <code>{$user->invite_num}</code>{/if}</p>
                                            <p>发送邀请链接给有需要的人，邀请他人注册时，请将以下链接发给被邀请者</p>
                                            <div class="invite-link">
                                                <input type="text"
                                                       class="input form-control form-control-monospace cust-link"
                                                       name="input1" readonly=""
                                                       value="{$config['baseUrl']}/auth/register?code={$code->code}">
                                                <button class="copy-text btn btn-subscription" type="button"
                                                        data-clipboard-text="{$config['baseUrl']}/auth/register?code={$code->code}">
                                                    点击复制
                                                </button>
                                            </div>
                                            <div class="invite-link">
                                                <input type="text"
                                                       class="input form-control form-control-monospace cust-link"
                                                       name="input2" readonly=""
                                                       value="{$config['baseUrl']}/#/auth/register?code={$code->code}">
                                                <button class="copy-text btn btn-subscription" type="button"
                                                        data-clipboard-text="{$config['baseUrl']}/#/auth/register?code={$code->code}">
                                                    点击复制
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if $config['custom_invite_price']>=0}
                            <div class="col-lg-6 col-md-6">
                                <div class="card margin-bottom-no">
                                    <div class="card-main">
                                        <div class="card-inner margin-bottom-no">
                                            <div class="card-inner margin-bottom-no">
                                                <div class="cardbtn-edit">
                                                    <div class="card-heading">定制链接后缀 <code
                                                                class="card-tag tag-green">{$config['custom_invite_price']}
                                                            RMB/次</code></div>
                                                    <button class="btn btn-flat" id="custom-invite-confirm"><span
                                                                class="icon">check</span>&nbsp;
                                                    </button>
                                                </div>
                                                <p>例:输入<code>vip</code>则链接变为<code>{$config['baseUrl']}
                                                        /auth/register?code=vip</code></p>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label"
                                                           for="custom-invite-link">输入链接后缀</label>
                                                    <input class="form-control maxwidth-edit" id="custom-invite-link"
                                                           type="num">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/if}

                    {if $config['invite_price']>=0}
                        <div class="col-xx-12">
                            <div class="card margin-bottom-no">
                                <div class="card-main">
                                    <div class="card-inner">
                                        <div class="card-inner">
                                            <div class="cardbtn-edit">
                                                <div class="card-heading">购买邀请次数 <code
                                                            class="card-tag tag-green">{$config['invite_price']}
                                                        RMB/个</code></div>
                                                <button class="btn btn-flat" id="buy-invite"><span
                                                            class="material-icons">shopping_cart</span></button>
                                            </div>
                                            <p>在下方输入需要购买的邀请次数</p>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="buy-invite-num">输入购买次数</label>
                                                <input class="form-control maxwidth-edit" id="buy-invite-num"
                                                       type="num">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}

                {else}
                    <div class="col-xx-12">
                        <div class="card margin-bottom-no">
                            <div class="card-main">
                                <div class="card-inner">
                                    <p class="card-heading">邀请链接</p>
                                    <h3>{$user->user_name}，您不是VIP暂时无法使用邀请链接，<a href="/user/shop">成为VIP请点击这里</a></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="col-xx-12">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-table">
                                    <div class="table-responsive bgc-fix table-user">
                                        {$paybacks->render()}
                                        <table class="table">
                                            <tr>

                                                <!--   <th>ID</th> -->
                                                <th>ID</th>
                                                <th>被邀请用户ID</th>
                                                <th>获得返利</th>
                                            </tr>
                                            {foreach $paybacks as $payback}
                                                <tr>

                                                    <td>{$payback->id}</td>
                                                    {if $payback->user()!=null}
                                                        <td>{$payback->user()->user_name}
                                                        </td>
                                                    {else}
                                                        <td>已注销
                                                        </td>
                                                    {/if}
                                                    <td>{$payback->ref_get} 元</td>

                                                </tr>
                                            {/foreach}
                                        </table>
                                        {$paybacks->render()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {include file='dialog.tpl'}

            </div>
        </section>
    </div>
</main>

{include file='user/footer.tpl'}

<script>
    $(function () {
        new Clipboard('.copy-text');
    });

    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已复制到您的剪贴板，请您继续接下来的操作。';
    });

    $(document).ready(function () {
        $("#invite").click(function () {
            $.ajax({
                type: "POST",
                url: "/user/invite",
                dataType: "json",
                success: (data) => {
                    window.location.reload();
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            })
        })
    })
</script>

<script>

    $("#buy-invite").click(function () {
        $.ajax({
            type: "POST",
            url: "/user/buy_invite",
            dataType: "json",
            data: {
                num: $$getValue('buy-invite-num')
            },
            success: (data) => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user/invite'", {$config['jump_delay']});
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        data.msg
                        } 出现了一些错误`;
            }
        })
    });

    $("#custom-invite-confirm").click(function () {
        $.ajax({
            type: "POST",
            url: "/user/custom_invite",
            dataType: "json",
            data: {
                customcode: $$getValue('custom-invite-link'),
            },
            success: (data) => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user/invite'", {$config['jump_delay']});
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        data.msg
                        } 出现了一些错误`;
            }
        })
    });

</script>

<script>

    $(".reset-link").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已重置您的邀请链接，复制您的邀请链接发送给其他人！';
        window.setTimeout("location.href='/user/inviteurl_reset'", {$config['jump_delay']});
    });

</script>
