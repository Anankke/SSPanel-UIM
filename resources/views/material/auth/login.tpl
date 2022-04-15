<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta9
* @link https://tabler.io
* Copyright 2018-2022 The Tabler Authors
* Copyright 2018-2022 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{$config['appName']}</title>
    <!-- CSS files -->
    <link href="/theme/tabler/css/tabler.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/demo.min.css" rel="stylesheet" />
    <script src="/theme/tabler/js/jquery-3.6.0.min.js"></script>
    <script src="/theme/tabler/js/tabler.min.js"></script>
</head>

<body class=" border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a href="#" class="navbar-brand navbar-brand-autodark">
                    <img src="/images/uim-logo-round.png" height="64" alt="">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">登录到用户中心</h2>
                    <div class="mb-3">
                        <label class="form-label">注册邮箱</label>
                        <input id="email" type="email" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            登录密码
                            <span class="form-label-description">
                                <a href="/password/reset">忘记密码</a>
                            </span>
                        </label>
                        <div class="input-group input-group-flat">
                            <input id="passwd" type="password" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">两步认证</label>
                        <input id="code" type="email" class="form-control" placeholder="如果没有设置两步认证可留空">
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" />
                            <span class="form-check-label">记住此设备</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button id="login-dashboard" class="btn btn-primary w-100">登录</button>
                    </div>
                </div>
                <!-- <div class="hr-text">or</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col"><a href="#" class="btn btn-white w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-github" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5" />
                                </svg>
                                Login with Github
                            </a></div>
                        <div class="col"><a href="#" class="btn btn-white w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-twitter" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M22 4.01c-1 .49 -1.98 .689 -3 .99c-1.121 -1.265 -2.783 -1.335 -4.38 -.737s-2.643 2.06 -2.62 3.737v1c-3.245 .083 -6.135 -1.395 -8 -4c0 0 -4.182 7.433 4 11c-1.872 1.247 -3.739 2.088 -6 2c3.308 1.803 6.913 2.423 10.034 1.517c3.58 -1.04 6.522 -3.723 7.651 -7.742a13.84 13.84 0 0 0 .497 -3.753c-.002 -.249 1.51 -2.772 1.818 -4.013z" />
                                </svg>
                                Login with Twitter
                            </a></div>
                    </div>
                </div> -->
            </div>
            <div class="text-center text-muted mt-3">
                还没有账户？ <a href="/auth/register" tabindex="-1">点击注册</a>
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
        $("#login-dashboard").click(function() {
            $.ajax({
                type: 'POST',
                url: '/auth/login',
                dataType: "json",
                data: {
                    code: $('#code').val(),
                    email: $('#email').val(),
                    passwd: $('#passwd').val(),
                    remember_me: $('#remember_me').val()
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

</html>