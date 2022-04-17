{include file='tabler_header.tpl'}

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a href="#" class="navbar-brand navbar-brand-autodark">
                    <img src="/images/uim-logo-round.png" height="64" alt="">
                </a>
            </div>
            <div class="card card-md">
                {if $config['register_mode'] != 'close'}
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">注册账户</h2>
                        <div class="mb-3">
                            <input id="name" type="text" class="form-control" placeholder="昵称">
                        </div>
                        <div class="mb-3">
                            <input id="email" type="email" class="form-control" placeholder="电子邮箱">
                        </div>
                        <div class="mb-3">
                            <div class="input-group input-group-flat">
                                <input id="passwd" type="password" class="form-control" placeholder="登录密码">
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group input-group-flat">
                                <input id="repasswd" type="password" class="form-control" placeholder="重复登录密码">
                            </div>
                        </div>
                        {if $config['enable_reg_im'] == true}
                            <div class="mb-3">
                                <select id="im_type" class="col form-select">
                                    <option value="0">请选择社交软件</option>
                                    <option value="1">微信</option>
                                    <option value="2">QQ</option>
                                    <option value="4">Telegram</option>
                                    <option value="5">Discord</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="input-group input-group-flat">
                                    <input id="im_value" type="text" class="form-control" placeholder="社交账号">
                                </div>
                            </div>
                        {/if}
                        {if $config['register_mode'] != 'close' }
                            <div class="mb-3">
                                <div class="input-group input-group-flat">
                                    <input id="code" type="text" class="form-control" placeholder="注册邀请码" value="{$code}">
                                </div>
                            </div>
                        {/if}
                        {if $enable_email_verify == true}
                            <div class="mb-3">
                                <div class="input-group mb-2">
                                    <input id="emailcode" type="text" class="form-control" placeholder="邮箱验证码">
                                    <button id="email-verify" class="btn text-blue" type="button">获取</button>
                                </div>
                            </div>
                        {/if}
                        <div class="mb-3">
                            <label class="form-check">
                                <input id="tos" type="checkbox" class="form-check-input" />
                                <span class="form-check-label">
                                    我已阅读并同意 <a href="/tos" tabindex="-1"> 服务条款与隐私政策 </a>
                                </span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button id="confirm-register" type="submit" class="btn btn-primary w-100">注册新账户</button>
                        </div>
                    </div>
                {else}
                    <div class="card-body">
                        <p>还没有开放注册，过两天再来看看吧</p>
                    </div>
                {/if}
            </div>
            <div class="text-center text-muted mt-3">
                已有账户？ <a href="/auth/login" tabindex="-1">点击登录</a>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="success-message" class="text-muted">成功</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a id="success-confirm" href="#" class="btn w-100" data-bs-dismiss="modal">
                                    好
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="send-success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="send-success-message" class="text-muted">成功</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    好
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <p id="fail-message" class="text-muted">失败</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        {if $enable_email_verify == true}
            $("#email-verify").click(function() {
                $.ajax({
                    type: 'POST',
                    url: '/auth/send',
                    dataType: "json",
                    data: {
                        email: $('#email').val(),
                    },
                    success: function(data) {
                        if (data.ret == 1) {
                            $('#send-success-message').text(data.msg);
                            $('#send-success-dialog').modal('show');
                        } else {
                            $('#fail-message').text(data.msg);
                            $('#fail-dialog').modal('show');
                        }
                    }
                })
            });
        {/if}

        $("#confirm-register").click(function() {
            $.ajax({
                type: 'POST',
                url: '/auth/register',
                dataType: "json",
                data: {
                    {if $config['enable_reg_im'] == true}
                        im_value: $('#im_value').val(),
                        im_type: $('#im_type').val(),
                    {/if}
                    {if $enable_email_verify == true}
                        emailcode: $('#emailcode').val(),
                    {/if}
                    tos: $('#tos').prop('checked'), // true / false (string)
                    code: $('#code').val(),
                    name: $('#name').val(),
                    email: $('#email').val(),
                    passwd: $('#passwd').val(),
                    repasswd: $('#repasswd').val(),
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

        $("#success-confirm").click(function() {
            location.reload();
        });
    </script>
</body>
{include file='tabler_footer.tpl'}

</html>